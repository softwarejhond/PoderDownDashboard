<?php
session_start();
include(__DIR__ . '/../../conexion.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'get';

switch ($action) {

    case 'get':
        $producto_id = intval($_GET['producto_id'] ?? 0);
        if ($producto_id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        $res = mysqli_query($conn, "SELECT * FROM producto_atributos WHERE producto_id=$producto_id ORDER BY tipo, valor");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
        echo json_encode(['success'=>true,'data'=>$data]);
        break;

    case 'create':
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $tipo  = in_array($_POST['tipo'] ?? '', ['color','talla']) ? $_POST['tipo'] : '';
        $valor = trim($_POST['valor'] ?? '');
        if ($producto_id <= 0 || !$tipo || !$valor) {
            echo json_encode(['success'=>false,'message'=>'Datos invalidos']); break;
        }
        $valor_esc = mysqli_real_escape_string($conn, $valor);
        $dup = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT id FROM producto_atributos WHERE producto_id=$producto_id AND tipo='$tipo' AND valor='$valor_esc'"));
        if ($dup) { echo json_encode(['success'=>false,'message'=>'Ya existe ese atributo']); break; }
        mysqli_query($conn, "INSERT INTO producto_atributos (producto_id, tipo, valor) VALUES ($producto_id, '$tipo', '$valor_esc')");
        echo json_encode(['success'=>true,'message'=>'Atributo agregado','id'=>mysqli_insert_id($conn)]);
        break;

    case 'toggle':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        mysqli_query($conn, "UPDATE producto_atributos SET disponible = 1 - disponible WHERE id=$id");
        echo json_encode(['success'=>true]);
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        mysqli_query($conn, "DELETE FROM producto_atributos WHERE id=$id");
        echo json_encode(['success'=>true,'message'=>'Atributo eliminado']);
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Accion no reconocida']);
}
mysqli_close($conn);