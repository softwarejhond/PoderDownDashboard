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

    case 'listar':
        $sql = "SELECT u.id, u.username, u.nombre, u.email, u.rol,
                       COALESCE(nu.active, 0) AS notificaciones_activas
                FROM users u
                LEFT JOIN order_notify_users nu ON nu.user_id = u.id
                ORDER BY u.nombre ASC";
        $res = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    case 'toggle':
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $active = isset($_POST['active']) ? (int)$_POST['active'] : 0;

        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'user_id requerido']);
            break;
        }

        // verificar que el usuario exista
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) === 0) {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            break;
        }
        mysqli_stmt_close($stmt);

        if ($active) {
            $stmt = mysqli_prepare($conn, "INSERT INTO order_notify_users (user_id, active) VALUES (?, 1) ON DUPLICATE KEY UPDATE active = 1");
            mysqli_stmt_bind_param($stmt, 'i', $userId);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE order_notify_users SET active = 0 WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $userId);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo json_encode(['success' => true, 'message' => $active ? 'Usuario activado' : 'Usuario desactivado']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
