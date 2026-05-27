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
        $result = mysqli_query($conn,
            "SELECT c.*, COUNT(p.id) as total_productos
             FROM categorias c
             LEFT JOIN productos p ON p.categoria = c.nombre
             GROUP BY c.id
             ORDER BY c.fecha_creacion DESC");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'create':
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';

        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            break;
        }

        $nombre_esc      = mysqli_real_escape_string($conn, $nombre);
        $descripcion_esc = mysqli_real_escape_string($conn, $descripcion);

        $sql = "INSERT INTO categorias (nombre, descripcion, estado)
                VALUES ('$nombre_esc', '$descripcion_esc', '$estado')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Categoría creada', 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    case 'update':
        $id          = intval($_POST['id'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';

        if ($id <= 0 || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            break;
        }

        $nombre_esc      = mysqli_real_escape_string($conn, $nombre);
        $descripcion_esc = mysqli_real_escape_string($conn, $descripcion);

        $sql = "UPDATE categorias SET nombre='$nombre_esc', descripcion='$descripcion_esc', estado='$estado' WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            break;
        }
        if (mysqli_query($conn, "DELETE FROM categorias WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
}

mysqli_close($conn);
