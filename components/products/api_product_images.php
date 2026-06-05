<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../conexion.php';

$action = $_REQUEST['action'] ?? 'get';

switch ($action) {

    /* ===================================================
     * LISTAR imágenes de un producto
     * =================================================== */
    case 'get':
        $productId = (int)($_GET['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID de producto requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res  = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id=$productId ORDER BY sort_order ASC, created_at ASC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * SUBIR imagen(es)
     * =================================================== */
    case 'upload':
        $productId = (int)($_POST['product_id'] ?? 0);
        $sku       = trim($_POST['sku'] ?? '');

        if (!$productId || $sku === '') {
            echo json_encode(['success' => false, 'message' => 'Datos de producto requeridos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (empty($_FILES['images'])) {
            echo json_encode(['success' => false, 'message' => 'No se recibieron archivos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $allowedMime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
        $uploadDir   = __DIR__ . '/../../img/fotos_productos/';

        // Asegurar que el directorio existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $files = $_FILES['images'];
        $count = is_array($files['name']) ? count($files['name']) : 1;

        // Sort_order máximo actual
        $maxRes   = mysqli_query($conn, "SELECT MAX(sort_order) AS mo FROM product_images WHERE product_id=$productId");
        $maxRow   = mysqli_fetch_assoc($maxRes);
        $sortOrder = (($maxRow['mo'] ?? -1) + 1);

        // ¿Ya tiene imagen principal?
        $primRes   = mysqli_query($conn, "SELECT id FROM product_images WHERE product_id=$productId AND is_primary=1 LIMIT 1");
        $hasPrimary = mysqli_num_rows($primRes) > 0;

        $uploaded = [];
        $errors   = [];
        $skuSafe  = preg_replace('/[^a-zA-Z0-9\-_]/', '', $sku);

        for ($i = 0; $i < $count; $i++) {
            $name = is_array($files['name'])     ? $files['name'][$i]     : $files['name'];
            $type = is_array($files['type'])     ? $files['type'][$i]     : $files['type'];
            $tmp  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $err  = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];
            $size = is_array($files['size'])     ? $files['size'][$i]     : $files['size'];

            if ($err !== UPLOAD_ERR_OK) {
                $errors[] = "Error al subir «$name »";
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($type, $allowedMime) || !in_array($ext, $allowedExt)) {
                $errors[] = "«$name »: solo se permiten PNG, JPG, JPEG o WEBP (no GIF)";
                continue;
            }

            if ($size > 5 * 1024 * 1024) {
                $errors[] = "«$name »: excede el límite de 5 MB";
                continue;
            }

            $timestamp = time() . '_' . $i;
            $newName   = $skuSafe . '_' . $timestamp . '.' . $ext;
            $destPath  = $uploadDir . $newName;
            $dbPath    = 'img/fotos_productos/' . $newName;

            if (!move_uploaded_file($tmp, $destPath)) {
                $errors[] = "No se pudo guardar «$name »";
                continue;
            }

            $isPrimary  = (!$hasPrimary) ? 1 : 0;
            if ($isPrimary) $hasPrimary = true;

            $dbPathE = mysqli_real_escape_string($conn, $dbPath);
            $altE    = mysqli_real_escape_string($conn, pathinfo($name, PATHINFO_FILENAME));

            mysqli_query($conn, "INSERT INTO product_images
                (product_id, image_path, alt_text, sort_order, is_primary)
                VALUES ($productId, '$dbPathE', '$altE', $sortOrder, $isPrimary)");

            $uploaded[] = [
                'id'         => mysqli_insert_id($conn),
                'image_path' => $dbPath,
                'alt_text'   => pathinfo($name, PATHINFO_FILENAME),
                'sort_order' => $sortOrder,
                'is_primary' => $isPrimary,
            ];
            $sortOrder++;
        }

        echo json_encode([
            'success'  => count($uploaded) > 0,
            'uploaded' => $uploaded,
            'errors'   => $errors,
            'message'  => count($uploaded) . ' imagen(es) subida(s) correctamente',
        ], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * GUARDAR ORDEN (drag & drop)
     * =================================================== */
    case 'reorder':
        $order = $_POST['order'] ?? [];
        if (empty($order)) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el orden'], JSON_UNESCAPED_UNICODE);
            break;
        }
        foreach ($order as $i => $id) {
            $id = (int)$id;
            mysqli_query($conn, "UPDATE product_images SET sort_order=$i WHERE id=$id");
        }
        echo json_encode(['success' => true, 'message' => 'Orden actualizado'], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * MARCAR COMO IMAGEN PRINCIPAL
     * =================================================== */
    case 'set_primary':
        $id        = (int)($_POST['id']         ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        if (!$id || !$productId) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        mysqli_query($conn, "UPDATE product_images SET is_primary=0 WHERE product_id=$productId");
        mysqli_query($conn, "UPDATE product_images SET is_primary=1 WHERE id=$id");
        echo json_encode(['success' => true, 'message' => 'Imagen principal actualizada'], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * ELIMINAR imagen
     * =================================================== */
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $res = mysqli_query($conn, "SELECT image_path, product_id, is_primary FROM product_images WHERE id=$id LIMIT 1");
        $img = mysqli_fetch_assoc($res);
        if (!$img) {
            echo json_encode(['success' => false, 'message' => 'Imagen no encontrada'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Borrar archivo físico
        $filePath = __DIR__ . '/../../' . $img['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        mysqli_query($conn, "DELETE FROM product_images WHERE id=$id");

        // Si era la principal, promover la siguiente
        if ($img['is_primary']) {
            $pid  = (int)$img['product_id'];
            $next = mysqli_query($conn, "SELECT id FROM product_images WHERE product_id=$pid ORDER BY sort_order ASC LIMIT 1");
            $nextRow = mysqli_fetch_assoc($next);
            if ($nextRow) {
                mysqli_query($conn, "UPDATE product_images SET is_primary=1 WHERE id={$nextRow['id']}");
            }
        }

        echo json_encode(['success' => true, 'message' => 'Imagen eliminada correctamente'], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
