<?php
header('Content-Type: application/json; charset=utf-8');
include('../../conexion.php');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'get';

/* -------------------------------------------------------
 * Genera un slug a partir de un texto (soporte UTF-8)
 * ------------------------------------------------------- */
function slugify(string $str): string {
    $from = ['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ü','Ñ',
             'à','â','ä','è','ê','ë','î','ï','ô','ù','û','ü','ÿ','ç'];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','u','n',
             'a','a','a','e','e','e','i','i','o','u','u','u','y','c'];
    $str  = str_replace($from, $to, mb_strtolower(trim($str), 'UTF-8'));
    $str  = preg_replace('/[^a-z0-9\s-]/', '', $str);
    return preg_replace('/[\s-]+/', '-', $str);
}

/* -------------------------------------------------------
 * Devuelve un slug único comprobando duplicados en BD
 * ------------------------------------------------------- */
function uniqueSlug($conn, string $table, string $slug, ?int $excludeId = null): string {
    $base = $slug;
    $i    = 1;
    while (true) {
        $s  = mysqli_real_escape_string($conn, $slug);
        $ex = $excludeId ? ' AND id != ' . $excludeId : '';
        $r  = mysqli_query($conn, "SELECT id FROM `$table` WHERE slug='$s'$ex LIMIT 1");
        if (mysqli_num_rows($r) === 0) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}

/* -------------------------------------------------------
 * Convierte cadena vacía en NULL para campos opcionales
 * ------------------------------------------------------- */
function nullOrFloat($val): string {
    return (isset($val) && $val !== '') ? (float)$val : 'NULL';
}
function nullOrInt($val): string {
    return (isset($val) && $val !== '') ? (int)$val : 'NULL';
}

switch ($action) {

    /* ===================================================
     * LISTAR productos con nombre de categoría
     * =================================================== */
    case 'get':
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                ORDER BY p.created_at DESC";
        $res  = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * CREAR nuevo producto
     * =================================================== */
    case 'create':
        $sku       = trim($_POST['sku'] ?? '');
        $name      = trim($_POST['name'] ?? '');
        $desc      = trim($_POST['description'] ?? '');
        $shortDesc = trim($_POST['short_description'] ?? '');
        $catId     = nullOrInt($_POST['category_id'] ?? '');
        $tags      = trim($_POST['tags'] ?? '');
        $price     = (float)($_POST['price'] ?? 0);
        $compPrice = nullOrFloat($_POST['compare_price'] ?? '');
        $costPrice = nullOrFloat($_POST['cost_price'] ?? '');
        $stock     = (int)($_POST['stock'] ?? 0);
        $lowStock  = nullOrInt($_POST['low_stock_threshold'] ?? '');
        $isActive  = (int)($_POST['is_active'] ?? 1);
        $isFeat    = (int)($_POST['is_featured'] ?? 0) ? 1 : 0;
        $isDig     = (int)($_POST['is_digital'] ?? 0) ? 1 : 0;

        if ($sku === '' || $name === '') {
            echo json_encode(['success' => false, 'message' => 'El SKU y el nombre son obligatorios'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Unicidad del SKU
        $skuE     = mysqli_real_escape_string($conn, $sku);
        $skuCheck = mysqli_query($conn, "SELECT id FROM products WHERE sku='$skuE' LIMIT 1");
        if (mysqli_num_rows($skuCheck) > 0) {
            echo json_encode(['success' => false, 'message' => "El SKU «$sku» ya está en uso"], JSON_UNESCAPED_UNICODE);
            break;
        }

        $slug = uniqueSlug($conn, 'products', slugify($name));
        $n    = mysqli_real_escape_string($conn, $name);
        $sl   = mysqli_real_escape_string($conn, $slug);
        $d    = mysqli_real_escape_string($conn, $desc);
        $sd   = mysqli_real_escape_string($conn, $shortDesc);
        $tg   = mysqli_real_escape_string($conn, $tags);

        $sql = "INSERT INTO products
                    (sku, name, slug, description, short_description, category_id, tags,
                     price, compare_price, cost_price, stock, low_stock_threshold,
                     is_active, is_featured, is_digital)
                VALUES
                    ('$skuE','$n','$sl','$d','$sd',$catId,'$tg',
                     $price,$compPrice,$costPrice,$stock,$lowStock,
                     $isActive,$isFeat,$isDig)";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Producto creado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR producto existente
     * =================================================== */
    case 'update':
        $id        = (int)($_POST['id'] ?? 0);
        $sku       = trim($_POST['sku'] ?? '');
        $name      = trim($_POST['name'] ?? '');
        $desc      = trim($_POST['description'] ?? '');
        $shortDesc = trim($_POST['short_description'] ?? '');
        $catId     = nullOrInt($_POST['category_id'] ?? '');
        $tags      = trim($_POST['tags'] ?? '');
        $price     = (float)($_POST['price'] ?? 0);
        $compPrice = nullOrFloat($_POST['compare_price'] ?? '');
        $costPrice = nullOrFloat($_POST['cost_price'] ?? '');
        $stock     = (int)($_POST['stock'] ?? 0);
        $lowStock  = nullOrInt($_POST['low_stock_threshold'] ?? '');
        $isActive  = (int)($_POST['is_active'] ?? 1);
        $isFeat    = (int)($_POST['is_featured'] ?? 0) ? 1 : 0;
        $isDig     = (int)($_POST['is_digital'] ?? 0) ? 1 : 0;

        if (!$id || $sku === '' || $name === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Unicidad del SKU (excluyendo el producto actual)
        $skuE     = mysqli_real_escape_string($conn, $sku);
        $skuCheck = mysqli_query($conn, "SELECT id FROM products WHERE sku='$skuE' AND id != $id LIMIT 1");
        if (mysqli_num_rows($skuCheck) > 0) {
            echo json_encode(['success' => false, 'message' => "El SKU «$sku» ya está en uso por otro producto"], JSON_UNESCAPED_UNICODE);
            break;
        }

        $n  = mysqli_real_escape_string($conn, $name);
        $d  = mysqli_real_escape_string($conn, $desc);
        $sd = mysqli_real_escape_string($conn, $shortDesc);
        $tg = mysqli_real_escape_string($conn, $tags);

        $sql = "UPDATE products SET
                    sku='$skuE', name='$n', description='$d', short_description='$sd',
                    category_id=$catId, tags='$tg',
                    price=$price, compare_price=$compPrice, cost_price=$costPrice,
                    stock=$stock, low_stock_threshold=$lowStock,
                    is_active=$isActive, is_featured=$isFeat, is_digital=$isDig
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR producto
     * =================================================== */
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (mysqli_query($conn, "DELETE FROM products WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
