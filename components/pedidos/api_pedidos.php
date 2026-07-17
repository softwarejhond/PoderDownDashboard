<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../conexion.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'listar';

switch ($action) {

    /* ===================================================
     * LISTAR pedidos con pago aprobado (orden ascendente)
     * =================================================== */
    case 'listar':
        $sql = "SELECT o.id,
                       o.order_number,
                       o.customer_name,
                       o.customer_email,
                       o.customer_phone,
                       o.shipping_department,
                       o.shipping_city,
                       o.shipping_address,
                       o.shipping_address_detail,
                       o.shipping_postal_code,
                       o.customer_notes,
                       o.total,
                       o.status,
                       o.shipping_status,
                       o.created_at,
                       s.carrier,
                       s.tracking_number,
                       s.created_at AS shipped_at,
                       s.email_sent,
                       (SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.product_name) SEPARATOR ' | ')
                          FROM order_items oi WHERE oi.order_id = o.id) AS items
                FROM orders o
                LEFT JOIN order_shipments s ON s.order_id = o.id
                WHERE o.payment_status = 'paid'
                ORDER BY o.created_at ASC";
        $res = mysqli_query($conn, $sql);
        if (!$res) {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
            break;
        }
        $data = [];
        $maxId = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $maxId = max($maxId, (int)$row['id']);
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data, 'max_id' => $maxId], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * NUEVOS: pedidos pagados con id > last_id (polling)
     * =================================================== */
    case 'nuevos':
        $lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt, COALESCE(MAX(id), ?) AS max_id
                                       FROM orders
                                       WHERE payment_status = 'paid' AND id > ?");
        mysqli_stmt_bind_param($stmt, 'ii', $lastId, $lastId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        echo json_encode([
            'success' => true,
            'count'   => (int)$row['cnt'],
            'max_id'  => (int)$row['max_id']
        ]);
        break;

    /* ===================================================
     * DETALLE: pedido completo + items para el modal
     * =================================================== */
    case 'detalle':
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? AND payment_status = 'paid' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            break;
        }

        $shipmentStmt = mysqli_prepare($conn, "SELECT carrier, tracking_number, email_sent FROM order_shipments WHERE order_id = ? LIMIT 1");
        mysqli_stmt_bind_param($shipmentStmt, 'i', $orderId);
        mysqli_stmt_execute($shipmentStmt);
        $shipment = mysqli_fetch_assoc(mysqli_stmt_get_result($shipmentStmt));
        mysqli_stmt_close($shipmentStmt);

        $itemsStmt = mysqli_prepare($conn, "SELECT product_name, sku, quantity, unit_price, total FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($itemsStmt, 'i', $orderId);
        mysqli_stmt_execute($itemsStmt);
        $itemsRes = mysqli_stmt_get_result($itemsStmt);
        $items = [];
        while ($row = mysqli_fetch_assoc($itemsRes)) {
            $items[] = $row;
        }
        mysqli_stmt_close($itemsStmt);

        echo json_encode([
            'success'  => true,
            'order'    => $order,
            'shipment' => $shipment ?: null,
            'items'    => $items
        ], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * DETALLE_ENVIO: auditoría de quién y cuándo envió
     * =================================================== */
    case 'detalle_envio':
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        $stmt = mysqli_prepare($conn, "SELECT o.order_number, o.customer_name, o.total, o.status,
                                              s.carrier, s.tracking_number, s.created_at AS shipped_at, s.email_sent,
                                              a.admin_username, a.created_at AS audit_at,
                                              inv.filename AS invoice_filename
                                       FROM orders o
                                       JOIN order_shipments s ON s.order_id = o.id
                                       LEFT JOIN order_shipment_audit a ON a.shipment_id = s.id
                                       LEFT JOIN invoices inv ON inv.order_id = o.id
                                       WHERE o.id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Este pedido no tiene información de envío.']);
            break;
        }

        $itemsStmt = mysqli_prepare($conn, "SELECT product_name, quantity, unit_price, total FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($itemsStmt, 'i', $orderId);
        mysqli_stmt_execute($itemsStmt);
        $itemsRes = mysqli_stmt_get_result($itemsStmt);
        $items = [];
        while ($item = mysqli_fetch_assoc($itemsRes)) {
            $items[] = $item;
        }
        mysqli_stmt_close($itemsStmt);

        echo json_encode([
            'success' => true,
            'data'    => $row,
            'items'   => $items
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
