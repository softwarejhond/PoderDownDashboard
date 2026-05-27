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
        if ($producto_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalido']);
            break;
        }
        $result = mysqli_query($conn,
            "SELECT * FROM producto_imagenes WHERE producto_id=$producto_id ORDER BY orden ASC, fecha_creacion ASC");
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'upload':
        $producto_id = intval($_POST['producto_id'] ?? 0);
        if ($producto_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de producto invalido']);
            break;
        }

        // Verificar que el producto existe
        $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM productos WHERE id=$producto_id"));
        if (!$prod) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            break;
        }

        // Verificar limite de 8 imagenes
        $count_row = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(*) AS total FROM producto_imagenes WHERE producto_id=$producto_id"));
        if ((int)$count_row['total'] >= 8) {
            echo json_encode(['success' => false, 'message' => 'Maximo 8 imagenes por producto']);
            break;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se recibio ninguna imagen']);
            break;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido (jpeg, png, webp, gif)']);
            break;
        }

        $ext      = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $filename = 'gal_' . $producto_id . '_' . uniqid('', true) . '.' . $ext;
        $destDir  = __DIR__ . '/../../uploads/products/';
        $destPath = $destDir . $filename;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destPath)) {
            $ruta = mysqli_real_escape_string($conn, 'uploads/products/' . $filename);
            mysqli_query($conn,
                "INSERT INTO producto_imagenes (producto_id, imagen, orden) VALUES ($producto_id, '$ruta', 0)");
            echo json_encode([
                'success' => true,
                'message' => 'Imagen agregada',
                'imagen'  => 'uploads/products/' . $filename,
                'id'      => mysqli_insert_id($conn),
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen en el servidor']);
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalido']);
            break;
        }
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT imagen FROM producto_imagenes WHERE id=$id"));
        if ($row && $row['imagen'] && file_exists(__DIR__ . '/../../' . $row['imagen'])) {
            @unlink(__DIR__ . '/../../' . $row['imagen']);
        }
        if (mysqli_query($conn, "DELETE FROM producto_imagenes WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'Imagen eliminada']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Accion no reconocida']);
}

mysqli_close($conn);
