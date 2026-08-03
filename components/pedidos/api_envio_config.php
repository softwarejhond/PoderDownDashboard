<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../conexion.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'get';

switch ($action) {

    case 'get':
        $res = mysqli_query($conn, "SELECT valor FROM envio_config WHERE id = 1 LIMIT 1");
        $row = mysqli_fetch_assoc($res);
        echo json_encode(['success' => true, 'valor' => (float)$row['valor']]);
        break;

    case 'update':
        $valor = isset($_POST['valor']) ? (float)$_POST['valor'] : 0;
        if ($valor < 0) {
            echo json_encode(['success' => false, 'message' => 'El valor no puede ser negativo']);
            break;
        }
        $stmt = mysqli_prepare($conn, "UPDATE envio_config SET valor = ? WHERE id = 1");
        mysqli_stmt_bind_param($stmt, 'd', $valor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode([
            'success' => true,
            'valor'   => $valor,
            'message' => 'Costo de envío actualizado a $' . number_format($valor, 0, ',', '.')
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
