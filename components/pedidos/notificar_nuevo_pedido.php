<?php
session_start();
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

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'order_id requerido']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT order_number, customer_name, total, created_at FROM orders WHERE id = ? AND payment_status = 'paid' LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $orderId);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado o no pagado']);
    exit;
}

$res = mysqli_query($conn, "SELECT u.email, u.nombre FROM order_notify_users nu JOIN users u ON u.id = nu.user_id WHERE nu.active = 1");
$notifyUsers = [];
while ($row = mysqli_fetch_assoc($res)) {
    $notifyUsers[] = $row;
}

if (empty($notifyUsers)) {
    echo json_encode(['success' => true, 'sent' => 0, 'message' => 'No hay usuarios configurados para notificaciones']);
    exit;
}

$smtp = null;
try {
    $resSmtp = mysqli_query($conn, "SELECT * FROM smtpConfig WHERE id = 3 LIMIT 1");
    if ($resSmtp && mysqli_num_rows($resSmtp) > 0) {
        $smtp = mysqli_fetch_assoc($resSmtp);
    }
} catch (Throwable $e) { $smtp = null; }
if (!$smtp) {
    try {
        $resSmtp = mysqli_query($conn, "SELECT * FROM smtpconfig WHERE id = 1 LIMIT 1");
        if ($resSmtp && mysqli_num_rows($resSmtp) > 0) {
            $smtp = mysqli_fetch_assoc($resSmtp);
        }
    } catch (Throwable $e) { $smtp = null; }
}
if (!$smtp) {
    echo json_encode(['success' => false, 'message' => 'No se encontró configuración SMTP']);
    exit;
}

$codigo = htmlspecialchars($order['order_number']);
$cliente = htmlspecialchars($order['customer_name'] ?: 'Cliente');
$total = number_format((float)$order['total'], 0, ',', '.');
$fecha = date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now'));

$subject = 'Nuevo pedido pagado: ' . $order['order_number'] . ' — Requiere gestión';
$body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;font-family:Arial,sans-serif;background:#ebeae4;">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#1A3A5C,#0D2136);padding:22px;text-align:center;">
        <h1 style="color:#fff;font-size:20px;margin:0;">Nuevo pedido — Requiere gestión</h1>
    </div>
    <div style="padding:22px;">
        <p style="font-size:15px;color:#1A3A5C;">Ha llegado un nuevo pedido con pago aprobado:</p>
        <div style="background:#f8f7f4;border-radius:8px;padding:14px;margin-bottom:16px;">
            <p style="margin:0 0 4px;"><strong>Pedido:</strong> {$codigo}</p>
            <p style="margin:0 0 4px;"><strong>Cliente:</strong> {$cliente}</p>
            <p style="margin:0 0 4px;"><strong>Total:</strong> \${$total}</p>
            <p style="margin:0;"><strong>Fecha:</strong> {$fecha}</p>
        </div>
        <p style="font-size:14px;color:#555;">Ingresa al panel de administración para gestionar este pedido: preparar el envío, generar el recibo y notificar al cliente.</p>
        <div style="text-align:center;margin-top:16px;">
            <a href="https://poderdown.com/admin/main.php" style="display:inline-block;background:#3CAEE0;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-size:14px;">Ir al panel de administración</a>
        </div>
    </div>
    <div style="background:#f0efe9;padding:14px;text-align:center;font-size:12px;color:#999;">
        Poder Down — <a href="https://poderdown.com" style="color:#3CAEE0;">poderdown.com</a>
    </div>
</div>
</body>
</html>
HTML;

$altBody = "Nuevo pedido pagado: {$order['order_number']}\nCliente: {$cliente}\nTotal: \${$total}\nFecha: {$fecha}\n\nIngresa a https://poderdown.com/admin/main.php para gestionarlo.";

$sent = 0;
$errors = [];
foreach ($notifyUsers as $user) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = !empty($smtp['username']) ? $smtp['username'] : $smtp['email'];
        $mail->Password = $smtp['password'];
        $mail->Port = (int)$smtp['port'];
        $mail->SMTPSecure = ((int)$smtp['port'] === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout = 30;
        $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
        $fromEmail = !empty($smtp['email']) ? $smtp['email'] : 'noreply@poderdown.com';
        $mail->setFrom($fromEmail, 'Poder Down - Pedidos');
        $mail->addAddress($user['email'], $user['nombre'] ?: 'Admin');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        $mail->send();
        $sent++;
    } catch (Throwable $e) {
        $errors[] = $user['email'] . ': ' . $e->getMessage();
    }
}

echo json_encode([
    'success' => $sent > 0,
    'sent'    => $sent,
    'total'   => count($notifyUsers),
    'errors'  => $errors,
    'message' => "Notificaciones enviadas: {$sent}/" . count($notifyUsers)
], JSON_UNESCAPED_UNICODE);
