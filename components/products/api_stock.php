<?php
session_start();
include(__DIR__ . '/../../conexion.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

switch ($action) {

    case 'list':
        $sql = "SELECT id, numero_serie, nombre, categoria, stock,
                       COALESCE(stock_minimo, 5) as stock_minimo, estado, imagen
                FROM productos ORDER BY nombre ASC";
        $res = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        echo json_encode(['success'=>true,'data'=>$data]);
        break;

    case 'movimientos':
        $producto_id = intval($_GET['producto_id'] ?? 0);
        if ($producto_id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        $res = mysqli_query($conn, "SELECT * FROM stock_movimientos WHERE producto_id=$producto_id ORDER BY fecha DESC LIMIT 50");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        echo json_encode(['success'=>true,'data'=>$data]);
        break;

    case 'movimiento':
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $tipo        = in_array($_POST['tipo'] ?? '', ['entrada','salida','ajuste']) ? $_POST['tipo'] : '';
        $cantidad    = intval($_POST['cantidad'] ?? 0);
        $motivo      = trim($_POST['motivo'] ?? '');

        if ($producto_id <= 0 || !$tipo || $cantidad <= 0) {
            echo json_encode(['success'=>false,'message'=>'Datos invalidos']); break;
        }

        $motivo_esc = mysqli_real_escape_string($conn, $motivo);
        $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM productos WHERE id=$producto_id"));
        if (!$prod) { echo json_encode(['success'=>false,'message'=>'Producto no encontrado']); break; }

        $stock_anterior = intval($prod['stock']);
        if ($tipo === 'entrada') {
            $stock_nuevo = $stock_anterior + $cantidad;
        } elseif ($tipo === 'salida') {
            $stock_nuevo = $stock_anterior - $cantidad;
            if ($stock_nuevo < 0) { echo json_encode(['success'=>false,'message'=>'Stock insuficiente para la salida']); break; }
        } else {
            $stock_nuevo = $cantidad;
        }

        mysqli_query($conn, "UPDATE productos SET stock=$stock_nuevo WHERE id=$producto_id");
        mysqli_query($conn, "INSERT INTO stock_movimientos (producto_id, tipo, cantidad, motivo, stock_anterior, stock_nuevo)
                             VALUES ($producto_id, '$tipo', $cantidad, '$motivo_esc', $stock_anterior, $stock_nuevo)");

        echo json_encode(['success'=>true,'message'=>'Movimiento registrado','stock_nuevo'=>$stock_nuevo,'stock_anterior'=>$stock_anterior]);
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Accion no reconocida']);
}
mysqli_close($conn);