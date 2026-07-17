<?php
session_start();
ob_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

function responder($payload)
{
    ob_clean();
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$orderId  = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$carrier  = trim($_POST['carrier'] ?? '');
$tracking = trim($_POST['tracking_number'] ?? '');
$notes    = trim($_POST['notes'] ?? '');

if ($orderId <= 0 || $carrier === '' || $tracking === '') {
    responder(['success' => false, 'message' => 'Transportadora y número de guía son obligatorios.']);
}

$stmt = mysqli_prepare($conn, "SELECT o.*, (SELECT COUNT(*) FROM order_shipments s WHERE s.order_id = o.id) AS shipments
                               FROM orders o WHERE o.id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $orderId);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    responder(['success' => false, 'message' => 'Pedido no encontrado.']);
}
if ($order['payment_status'] !== 'paid') {
    responder(['success' => false, 'message' => 'El pedido no tiene el pago aprobado.']);
}
if ((int)$order['shipments'] > 0) {
    responder(['success' => false, 'message' => 'Este pedido ya tiene un envío registrado.']);
}

$usuario = $_SESSION['username'] ?? 'admin';
$oldStatus = $order['status'];

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn, "INSERT INTO order_shipments (order_id, carrier, tracking_number, notes, shipped_by) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'issss', $orderId, $carrier, $tracking, $notes, $usuario);
    mysqli_stmt_execute($stmt);
    $shipmentId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'shipped', shipping_status = 'shipped', updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $orderId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $comment = "Pedido enviado con {$carrier}. Guía: {$tracking}" . ($notes !== '' ? ". Notas: {$notes}" : '');
    $stmt = mysqli_prepare($conn, "INSERT INTO order_status_history (order_id, old_status, new_status, comment, notify_customer, changed_by) VALUES (?, ?, 'shipped', ?, 1, ?)");
    mysqli_stmt_bind_param($stmt, 'isss', $orderId, $oldStatus, $comment, $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "INSERT INTO order_shipment_audit (order_id, shipment_id, admin_username, action) VALUES (?, ?, ?, 'ship_confirmed')");
    mysqli_stmt_bind_param($stmt, 'iis', $orderId, $shipmentId, $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    responder(['success' => false, 'message' => 'Error al registrar el envío: ' . $e->getMessage()]);
}

/* Obtener items y datos de cliente para PDF y correo */
$stmt = mysqli_prepare($conn, "SELECT product_name, sku, quantity, unit_price, total FROM order_items WHERE order_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $orderId);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

/* Obtener nombre de empresa (sin NIT) */
$company = ['nombre' => 'Poder Down'];
try {
    $resCompany = mysqli_query($conn, "SELECT nombre FROM company WHERE id = 1 LIMIT 1");
    if ($resCompany && mysqli_num_rows($resCompany) > 0) {
        $company = mysqli_fetch_assoc($resCompany);
    }
} catch (Throwable $e) {}

/* ========== GENERAR FACTURA PDF ========== */
$pdfOk = false;
$pdfFilename = '';
$pdfError = '';
try {
    $docNumber = trim(($order['customer_document_number'] ?? '') . '');
    $ts = date('YmdHis');
    $pdfFilename = $order['order_number'] . '_' . ($docNumber !== '' ? $docNumber : 'SN') . '_' . $ts . '.pdf';
    $pdfPath = __DIR__ . '/../../uploads/facturas/' . $pdfFilename;
    $uploadDir = __DIR__ . '/../../uploads/facturas';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $empresaNombre = htmlspecialchars($company['nombre']);
    $codigo = htmlspecialchars($order['order_number']);
    $nombre = htmlspecialchars($order['customer_name'] ?: 'Cliente');
    $documento = htmlspecialchars(($order['customer_document_type'] ?? '') . ': ' . ($order['customer_document_number'] ?? ''));
    $direccion = htmlspecialchars(trim(($order['shipping_address'] ?? '') . ' ' . ($order['shipping_address_detail'] ?? '')) . ', ' . ($order['shipping_city'] ?? '') . ', ' . ($order['shipping_department'] ?? ''));
    $fechan = date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now'));
    $fechaAct = date('d/m/Y H:i');
    $subtotal = number_format((float)$order['subtotal'], 0, ',', '.');
    $envioVal = number_format((float)$order['shipping_cost'], 0, ',', '.');
    $total = number_format((float)$order['total'], 0, ',', '.');

    $itemsHtml = '';
    foreach ($items as $item) {
        $n = htmlspecialchars($item['product_name']);
        $q = (int)$item['quantity'];
        $p = number_format((float)$item['unit_price'], 0, ',', '.');
        $t = number_format((float)$item['total'], 0, ',', '.');
        $itemsHtml .= "<tr><td style='padding:6px 8px;border-bottom:1px solid #ddd;'>$n</td><td style='text-align:center;padding:6px 8px;border-bottom:1px solid #ddd;'>$q</td><td style='text-align:right;padding:6px 8px;border-bottom:1px solid #ddd;'>\$$p</td><td style='text-align:right;padding:6px 8px;border-bottom:1px solid #ddd;'>\$$t</td></tr>";
    }

    $htmlPdf = <<<PDFHTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Factura $codigo</title>
<style>
body{font-family:DejaVu Sans,Arial,sans-serif;color:#1A3A5C;margin:0;padding:20px;font-size:13px;}
.header{border-bottom:3px solid #1A3A5C;padding-bottom:12px;margin-bottom:16px;}
.header h1{font-size:24px;margin:0;color:#1A3A5C;}
.header .sub{color:#666;font-size:12px;margin-top:4px;}
.section{margin-bottom:16px;}
.section strong{display:inline-block;width:100px;}
table{width:100%;border-collapse:collapse;margin-top:8px;}
th{background:#1A3A5C;color:#fff;padding:8px;text-align:left;font-size:12px;}
th.ta-right{text-align:right;}
td.ta-right{text-align:right;}
.total-row td{font-weight:bold;font-size:15px;border-top:2px solid #1A3A5C;padding-top:8px;}
.foot{text-align:center;color:#999;font-size:11px;margin-top:30px;border-top:1px solid #ddd;padding-top:10px;}
.carrier-box{background:#f0efe9;border-radius:8px;padding:10px 14px;margin-top:10px;}
</style></head>
<body>
<div class="header">
    <h1>$empresaNombre</h1>
    <div class="sub">Factura de pedido</div>
</div>
<div class="section">
    <div><strong>Factura:</strong> $codigo</div>
    <div><strong>Fecha pedido:</strong> $fechan</div>
    <div><strong>Fecha despacho:</strong> $fechaAct</div>
</div>
<div class="section">
    <p style="font-weight:bold;margin:0 0 4px;">Datos del cliente</p>
    <div>$nombre</div>
    <div>$documento</div>
    <div>$direccion</div>
</div>
<div class="section">
    <p style="font-weight:bold;margin:0 0 4px;">Envío</p>
    <div class="carrier-box">
        <strong>Transportadora:</strong> $carrier &nbsp;&nbsp; <strong>Guía:</strong> $tracking
    </div>
</div>
<table>
    <thead><tr><th>Producto</th><th style="width:60px;text-align:center;">Cant.</th><th class="ta-right">Precio</th><th class="ta-right">Subtotal</th></tr></thead>
    <tbody>$itemsHtml</tbody>
    <tfoot>
        <tr><td colspan="3" style="text-align:right;padding:6px 8px;">Subtotal</td><td class="ta-right">\$$subtotal</td></tr>
        <tr><td colspan="3" style="text-align:right;padding:6px 8px;">Envío</td><td class="ta-right">\$$envioVal</td></tr>
        <tr class="total-row"><td colspan="3" style="text-align:right;">TOTAL</td><td class="ta-right">\$$total</td></tr>
    </tfoot>
</table>
<div class="foot">
    $empresaNombre &mdash; Todos los derechos reservados
</div>
</body></html>
PDFHTML;

    $dompdf = new Dompdf();
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->loadHtml($htmlPdf);
    $dompdf->render();
    file_put_contents($pdfPath, $dompdf->output());

    $stmt = mysqli_prepare($conn, "INSERT INTO invoices (order_id, filename) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'is', $orderId, $pdfFilename);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $pdfOk = true;
} catch (Throwable $e) {
    $pdfError = $e->getMessage();
    error_log('enviar_pedido PDF ERROR: ' . $pdfError);
}

/* ========== ENVIAR CORREO CON FACTURA ADJUNTA ========== */
$emailOk = false;
$emailError = '';
try {
    $smtp = null;
    try {
        $resSmtp = mysqli_query($conn, "SELECT * FROM smtpConfig WHERE id = 3 LIMIT 1");
        if ($resSmtp && mysqli_num_rows($resSmtp) > 0) {
            $smtp = mysqli_fetch_assoc($resSmtp);
        }
    } catch (Throwable $e) {
        $smtp = null;
    }
    if (!$smtp) {
        try {
            $resSmtp = mysqli_query($conn, "SELECT * FROM smtpconfig WHERE id = 1 LIMIT 1");
            if ($resSmtp && mysqli_num_rows($resSmtp) > 0) {
                $smtp = mysqli_fetch_assoc($resSmtp);
            }
        } catch (Throwable $e) {
            $smtp = null;
        }
    }
    if (!$smtp) {
        throw new Exception('No se encontró configuración SMTP.');
    }

    $nombreH   = htmlspecialchars($order['customer_name'] ?: 'Cliente');
    $codigoH   = htmlspecialchars($order['order_number']);
    $carrierH  = htmlspecialchars($carrier);
    $trackingH = htmlspecialchars($tracking);
    $direccionH = htmlspecialchars(trim(($order['shipping_address'] ?? '') . ' ' . ($order['shipping_address_detail'] ?? '')) . ', ' . ($order['shipping_city'] ?? '') . ', ' . ($order['shipping_department'] ?? ''));

    $itemsBody = '';
    $itemsText = [];
    foreach ($items as $it) {
        $nit = htmlspecialchars($it['product_name']);
        $nct = (int)$it['quantity'];
        $itemsBody .= "<tr><td style='padding:8px 0;border-bottom:1px solid #d6d4cc;font-size:14px;'>{$nit} x{$nct}</td></tr>";
        $itemsText[] = "{$it['product_name']} x{$nct}";
    }

    $body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#ebeae4;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#ebeae4;padding:30px 0;">
    <tr><td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(26,58,92,.08);">
            <tr>
                <td style="background:linear-gradient(135deg,#1A3A5C,#0D2136);padding:28px 30px;text-align:center;">
                    <h1 style="font-family:'Trebuchet MS',Arial,sans-serif;font-size:22px;color:#fff;margin:0;">¡Tu pedido va en camino, {$nombreH}!</h1>
                </td>
            </tr>
            <tr>
                <td style="padding:28px 30px;">
                    <p style="font-size:15px;color:#1A3A5C;line-height:1.7;margin:0 0 16px;">
                        Tu pedido <strong style="color:#3CAEE0;">{$codigoH}</strong> fue despachado y pronto llegará a tu dirección.
                    </p>
                    <div style="background:#f8f7f4;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
                        <p style="font-size:14px;color:#1A3A5C;margin:0 0 8px;"><strong>Transportadora:</strong> {$carrierH}</p>
                        <p style="font-size:14px;color:#1A3A5C;margin:0;"><strong>Número de guía:</strong> <span style="color:#3CAEE0;font-weight:700;">{$trackingH}</span></p>
                    </div>
                    <div style="background:#f8f7f4;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
                        <p style="font-size:13px;color:#666;margin:0 0 12px;"><strong>Productos</strong></p>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#1A3A5C;">{$itemsBody}</table>
                    </div>
                    <p style="font-size:13px;color:#888;line-height:1.7;margin:0 0 20px;">
                        <strong>Dirección de envío:</strong> {$direccionH}
                    </p>
                    <p style="font-size:14px;color:#1A3A5C;line-height:1.7;margin:0;">
                        La factura de tu pedido se adjunta en PDF. Puedes rastrear tu paquete en la página de la transportadora con el número de guía. Si tienes alguna duda, escríbenos a <a href="mailto:info@poderdown.com" style="color:#3CAEE0;">info@poderdown.com</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="background:#f0efe9;padding:18px 30px;text-align:center;">
                    <p style="font-size:12px;color:#999;margin:0;">Poder Down &mdash; Arte e inclusión<br><a href="https://poderdown.com" style="color:#3CAEE0;text-decoration:none;">poderdown.com</a></p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
HTML;

    $altBody = "¡Tu pedido va en camino, {$order['customer_name']}!\n\n"
        . "Pedido: {$order['order_number']}\n"
        . "Transportadora: {$carrier}\n"
        . "Número de guía: {$tracking}\n\n"
        . implode("\n", $itemsText) . "\n\n"
        . "La factura se adjunta en PDF. Poder Down — poderdown.com";

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host = $smtp['host'];
    $mail->SMTPAuth = true;
    $mail->Username = !empty($smtp['username']) ? $smtp['username'] : $smtp['email'];
    $mail->Password = $smtp['password'];
    $mail->Port = (int)$smtp['port'];
    $mail->SMTPSecure = ((int)$smtp['port'] === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Timeout = 60;
    $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];

    $fromEmail = !empty($smtp['email']) ? $smtp['email'] : 'noreply@poderdown.com';
    $mail->setFrom($fromEmail, $company['nombre']);
    $mail->addAddress($order['customer_email'], $order['customer_name'] ?: 'Cliente');
    $mail->isHTML(true);
    $mail->Subject = 'Tu pedido ' . $order['order_number'] . ' fue enviado — ' . $company['nombre'];

    if ($pdfOk && $pdfFilename !== '') {
        $pdfFullPath = __DIR__ . '/../../uploads/facturas/' . $pdfFilename;
        if (file_exists($pdfFullPath)) {
            $mail->addAttachment($pdfFullPath, 'Factura_' . $order['order_number'] . '.pdf');
        }
    }

    $mail->Body = $body;
    $mail->AltBody = $altBody;
    $mail->send();

    $emailOk = true;
    $stmt = mysqli_prepare($conn, "UPDATE order_shipments SET email_sent = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $shipmentId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} catch (Throwable $e) {
    $emailError = $e->getMessage();
    error_log('enviar_pedido email ERROR: ' . $emailError);
}

$msg = 'Envío registrado';
if ($emailOk) $msg .= ' y correo enviado al cliente';
if ($pdfOk) $msg .= ' (factura PDF generada)';
if (!$emailOk && $emailError !== '') $msg .= ', pero el correo falló: ' . $emailError;
if (!$pdfOk) $msg .= '. El PDF no pudo generarse';

responder([
    'success'    => true,
    'email_sent' => $emailOk,
    'pdf_ok'     => $pdfOk,
    'message'    => $msg
]);
