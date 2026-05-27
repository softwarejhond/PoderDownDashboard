<?php
session_start();
include(__DIR__ . '/../../conexion.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'get';

/* ─── Genera numero de serie unico de 6 digitos ────────────────────── */
function generarSerieUnico($conn): string
{
    do {
        $serie = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $check = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT id FROM productos WHERE numero_serie = '$serie'"));
    } while ($check !== null);
    return $serie;
}

/* ─── Utilidad: mover imagen subida ──────────────────────────────────── */
function handleImageUpload($field = 'imagen'): string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $_FILES[$field]['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        return '';
    }

    $ext      = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $filename = uniqid('prod_', true) . '.' . strtolower($ext);
    $destDir  = __DIR__ . '/../../uploads/products/';
    $destPath = $destDir . $filename;

    if (move_uploaded_file($_FILES[$field]['tmp_name'], $destPath)) {
        return 'uploads/products/' . $filename;
    }
    return '';
}

switch ($action) {

    case 'get':
        $where = [];
        if (!empty($_GET['categoria'])) {
            $cat = mysqli_real_escape_string($conn, $_GET['categoria']);
            $where[] = "categoria='$cat'";
        }
        if (!empty($_GET['estado']) && in_array($_GET['estado'], ['activo','inactivo'])) {
            $where[] = "estado='{$_GET['estado']}'";
        }
        if (isset($_GET['destacado']) && $_GET['destacado'] !== '') {
            $where[] = 'destacado=' . intval($_GET['destacado']);
        }
        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $result = mysqli_query($conn, "SELECT * FROM productos $whereStr ORDER BY fecha_creacion DESC");
        $data   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'create':
        $nombre        = trim($_POST['nombre'] ?? '');
        $descripcion   = trim($_POST['descripcion'] ?? '');
        $precio        = floatval($_POST['precio'] ?? 0);
        $precio_oferta = !empty($_POST['precio_oferta']) ? floatval($_POST['precio_oferta']) : null;
        $stock         = intval($_POST['stock'] ?? 0);
        $stock_minimo  = intval($_POST['stock_minimo'] ?? 5);
        $categoria     = trim($_POST['categoria'] ?? '');
        $estado        = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';
        $destacado     = !empty($_POST['destacado']) ? 1 : 0;
        $imagen        = handleImageUpload('imagen');

        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
            break;
        }

        $numero_serie    = generarSerieUnico($conn);
        $nombre_esc      = mysqli_real_escape_string($conn, $nombre);
        $descripcion_esc = mysqli_real_escape_string($conn, $descripcion);
        $categoria_esc   = mysqli_real_escape_string($conn, $categoria);
        $imagen_esc      = mysqli_real_escape_string($conn, $imagen);
        $po_sql          = $precio_oferta !== null ? $precio_oferta : 'NULL';

        $sql = "INSERT INTO productos (numero_serie, nombre, descripcion, precio, precio_oferta, stock, stock_minimo, imagen, categoria, estado, destacado)
                VALUES ('$numero_serie', '$nombre_esc', '$descripcion_esc', $precio, $po_sql, $stock, $stock_minimo, '$imagen_esc', '$categoria_esc', '$estado', $destacado)";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Producto creado', 'id' => mysqli_insert_id($conn), 'numero_serie' => $numero_serie]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    case 'update':
        $id            = intval($_POST['id'] ?? 0);
        $nombre        = trim($_POST['nombre'] ?? '');
        $descripcion   = trim($_POST['descripcion'] ?? '');
        $precio        = floatval($_POST['precio'] ?? 0);
        $precio_oferta = !empty($_POST['precio_oferta']) ? floatval($_POST['precio_oferta']) : null;
        $stock         = intval($_POST['stock'] ?? 0);
        $stock_minimo  = intval($_POST['stock_minimo'] ?? 5);
        $categoria     = trim($_POST['categoria'] ?? '');
        $estado        = in_array($_POST['estado'] ?? '', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';
        $destacado     = !empty($_POST['destacado']) ? 1 : 0;
        $imagen_nueva  = handleImageUpload('imagen');

        if ($id <= 0 || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
            break;
        }

        $nombre_esc      = mysqli_real_escape_string($conn, $nombre);
        $descripcion_esc = mysqli_real_escape_string($conn, $descripcion);
        $categoria_esc   = mysqli_real_escape_string($conn, $categoria);
        $po_sql          = $precio_oferta !== null ? $precio_oferta : 'NULL';

        if ($imagen_nueva !== '') {
            $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT imagen FROM productos WHERE id=$id"));
            if ($old && $old['imagen'] && file_exists(__DIR__ . '/../../' . $old['imagen'])) {
                @unlink(__DIR__ . '/../../' . $old['imagen']);
            }
            $imagen_esc = mysqli_real_escape_string($conn, $imagen_nueva);
            $img_sql    = ", imagen='$imagen_esc'";
        } else {
            $img_sql = '';
        }

        $sql = "UPDATE productos
                SET nombre='$nombre_esc', descripcion='$descripcion_esc',
                    precio=$precio, precio_oferta=$po_sql, stock=$stock,
                    stock_minimo=$stock_minimo, categoria='$categoria_esc',
                    estado='$estado', destacado=$destacado $img_sql
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado']);
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
        // Eliminar imagen física si existe
        $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT imagen FROM productos WHERE id=$id"));
        if ($old && $old['imagen'] && file_exists(__DIR__ . '/../../' . $old['imagen'])) {
            @unlink(__DIR__ . '/../../' . $old['imagen']);
        }
        if (mysqli_query($conn, "DELETE FROM productos WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;

    case 'duplicate':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        $orig = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM productos WHERE id=$id"));
        if (!$orig) { echo json_encode(['success'=>false,'message'=>'Producto no encontrado']); break; }
        $serie  = generarSerieUnico($conn);
        $nombre = mysqli_real_escape_string($conn, $orig['nombre'] . ' (Copia)');
        $desc   = mysqli_real_escape_string($conn, $orig['descripcion'] ?? '');
        $cat    = mysqli_real_escape_string($conn, $orig['categoria'] ?? '');
        $po     = $orig['precio_oferta'] !== null ? floatval($orig['precio_oferta']) : 'NULL';
        $sql    = "INSERT INTO productos (numero_serie, nombre, descripcion, precio, precio_oferta, stock, stock_minimo, categoria, estado, destacado)
                   VALUES ('$serie', '$nombre', '$desc', {$orig['precio']}, $po, 0, {$orig['stock_minimo']}, '$cat', 'inactivo', 0)";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success'=>true,'message'=>'Producto duplicado','numero_serie'=>$serie,'id'=>mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]);
        }
        break;

    case 'toggle_destacado':
        $id  = intval($_POST['id'] ?? 0);
        $val = intval($_POST['destacado'] ?? 0);
        if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'ID invalido']); break; }
        mysqli_query($conn, "UPDATE productos SET destacado=$val WHERE id=$id");
        echo json_encode(['success'=>true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Accion no reconocida']);
}

mysqli_close($conn);
