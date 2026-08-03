<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../conexion.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'get';

/* -------------------------------------------------------
 * Sincroniza stock y precio del producto padre desde
 * sus variantes activas
 * ------------------------------------------------------- */
function syncProductFromVariants($conn, int $productId): void {
    $res = mysqli_query($conn, "
        SELECT
            COALESCE(SUM(stock), 0) AS total_stock,
            COALESCE(MIN(price), 0) AS min_price
        FROM product_variants
        WHERE product_id = $productId AND is_active = 1
    ");
    $row = mysqli_fetch_assoc($res);
    $totalStock = (int)$row['total_stock'];
    $minPrice   = (float)$row['min_price'];

    mysqli_query($conn, "
        UPDATE products
        SET stock = $totalStock, price = $minPrice
        WHERE id = $productId
    ");
}

switch ($action) {

    /* ===================================================
     * LISTAR variantes de un producto con sus atributos
     * =================================================== */
    case 'get':
        $productId = (int)($_GET['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID de producto requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $res  = mysqli_query($conn, "
            SELECT v.*,
                   (SELECT GROUP_CONCAT(CONCAT(pa.name, ': ', pav.value) ORDER BY pa.sort_order SEPARATOR ' | ')
                    FROM product_variant_attributes pva
                    JOIN product_attribute_values pav ON pav.id = pva.attribute_value_id
                    JOIN product_attributes pa ON pa.id = pav.attribute_id
                    WHERE pva.variant_id = v.id
                   ) AS attributes_display
            FROM product_variants v
            WHERE v.product_id = $productId
            ORDER BY v.sort_order ASC, v.id ASC
        ");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * LISTAR detalle de una variante con sus valores
     * =================================================== */
    case 'get_detail':
        $variantId = (int)($_GET['variant_id'] ?? 0);
        if (!$variantId) {
            echo json_encode(['success' => false, 'message' => 'ID de variante requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $res = mysqli_query($conn, "SELECT * FROM product_variants WHERE id=$variantId LIMIT 1");
        $variant = mysqli_fetch_assoc($res);
        if (!$variant) {
            echo json_encode(['success' => false, 'message' => 'Variante no encontrada'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $attrRes = mysqli_query($conn, "
            SELECT pva.attribute_value_id, pav.value, pav.color_hex, pa.id AS attribute_id, pa.name AS attribute_name, pa.type AS attribute_type
            FROM product_variant_attributes pva
            JOIN product_attribute_values pav ON pav.id = pva.attribute_value_id
            JOIN product_attributes pa ON pa.id = pva.attribute_id
            WHERE pva.variant_id = $variantId
            ORDER BY pa.sort_order ASC
        ");
        $variant['attributes'] = [];
        while ($attr = mysqli_fetch_assoc($attrRes)) {
            $variant['attributes'][] = $attr;
        }

        echo json_encode(['success' => true, 'data' => $variant], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * GENERAR variantes automáticamente combinando
     * valores de atributos (predefinidos o inline)
     * =================================================== */
    case 'generate':
        $productId      = (int)($_POST['product_id'] ?? 0);
        $attributeIds   = $_POST['attribute_ids'] ?? [];
        $attributesData = json_decode($_POST['attributes_data'] ?? '[]', true);

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Producto requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $productRes = mysqli_query($conn, "SELECT sku, price, stock FROM products WHERE id=$productId LIMIT 1");
        $product    = mysqli_fetch_assoc($productRes);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $valuesByAttr = [];

        if (!empty($attributesData)) {
            foreach ($attributesData as $attrEntry) {
                $attrId  = (int)($attrEntry['attribute_id'] ?? 0);
                $valsRaw = $attrEntry['values'] ?? [];
                if (!$attrId || empty($valsRaw)) {
                    echo json_encode(['success' => false, 'message' => "Faltan datos para el atributo ID $attrId"], JSON_UNESCAPED_UNICODE);
                    break 2;
                }

                $attrVals = [];
                $sortIdx  = 0;
                foreach ($valsRaw as $rawVal) {
                    $rawVal = trim($rawVal);
                    if ($rawVal === '') continue;
                    $rawE = mysqli_real_escape_string($conn, $rawVal);

                    $existRes = mysqli_query($conn, "SELECT id FROM product_attribute_values WHERE attribute_id=$attrId AND value='$rawE' LIMIT 1");
                    if ($existRow = mysqli_fetch_assoc($existRes)) {
                        $vid = (int)$existRow['id'];
                    } else {
                        $colorHex = 'NULL';
                        if (strlen($rawVal) === 7 && preg_match('/^#[0-9A-Fa-f]{6}$/', $rawVal)) {
                            $attrTypeRes = mysqli_query($conn, "SELECT type FROM product_attributes WHERE id=$attrId LIMIT 1");
                            $attrTypeRow = mysqli_fetch_assoc($attrTypeRes);
                            if ($attrTypeRow && $attrTypeRow['type'] === 'color') {
                                $colorHex = "'" . mysqli_real_escape_string($conn, $rawVal) . "'";
                            }
                        }
                        mysqli_query($conn, "INSERT INTO product_attribute_values (attribute_id, value, color_hex, sort_order) VALUES ($attrId, '$rawE', $colorHex, $sortIdx)");
                        $vid = (int)mysqli_insert_id($conn);
                    }
                    $attrVals[] = ['id' => $vid, 'value' => $rawVal];
                    $sortIdx++;
                }

                if (empty($attrVals)) {
                    echo json_encode(['success' => false, 'message' => "El atributo ID $attrId no tiene valores válidos"], JSON_UNESCAPED_UNICODE);
                    break 2;
                }
                $valuesByAttr[] = $attrVals;
            }
        } elseif (!empty($attributeIds)) {
            if (!$productId || empty($attributeIds)) {
                echo json_encode(['success' => false, 'message' => 'Producto y al menos un atributo requeridos'], JSON_UNESCAPED_UNICODE);
                break;
            }
            foreach ($attributeIds as $attrId) {
                $attrId = (int)$attrId;
                $vRes   = mysqli_query($conn, "SELECT id, value FROM product_attribute_values WHERE attribute_id=$attrId ORDER BY sort_order ASC");
                $vals   = [];
                while ($v = mysqli_fetch_assoc($vRes)) {
                    $vals[] = $v;
                }
                if (empty($vals)) {
                    echo json_encode(['success' => false, 'message' => "El atributo ID $attrId no tiene valores definidos"], JSON_UNESCAPED_UNICODE);
                    break 2;
                }
                $valuesByAttr[] = $vals;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Debe enviar attributes_data o attribute_ids'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (empty($valuesByAttr)) {
            echo json_encode(['success' => false, 'message' => 'No se encontraron valores para combinar'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $combinations = [[]];
        foreach ($valuesByAttr as $attrValues) {
            $newCombos = [];
            foreach ($combinations as $combo) {
                foreach ($attrValues as $val) {
                    $newCombos[] = array_merge($combo, [$val]);
                }
            }
            $combinations = $newCombos;
        }

        $baseSku   = $product['sku'];
        $basePrice = $product['price'];
        $baseStock = 0;
        $maxSort   = 0;
        $sortRes   = mysqli_query($conn, "SELECT MAX(sort_order) AS mo FROM product_variants WHERE product_id=$productId");
        if ($sortRow = mysqli_fetch_assoc($sortRes)) {
            $maxSort = (int)($sortRow['mo'] ?? 0);
        }

        $created = [];
        foreach ($combinations as $idx => $combo) {
            $valueIds = array_column($combo, 'id');
            $valueNames = array_column($combo, 'value');
            sort($valueIds);

            $variantName = implode(' / ', $valueNames);
            $variantSku  = $baseSku . '-' . implode('-', array_map(function ($name) {
                return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));
            }, $valueNames));

            $sortOrder = $maxSort + $idx + 1;

            $nE  = mysqli_real_escape_string($conn, $variantName);
            $skE = mysqli_real_escape_string($conn, $variantSku);

            $check = mysqli_query($conn, "SELECT id FROM product_variants WHERE product_id=$productId AND sku='$skE' LIMIT 1");
            if (mysqli_num_rows($check) > 0) {
                continue;
            }

            mysqli_query($conn, "INSERT INTO product_variants
                (product_id, sku, name, price, stock, sort_order)
                VALUES ($productId, '$skE', '$nE', $basePrice, $baseStock, $sortOrder)");
            $variantId = mysqli_insert_id($conn);

            foreach ($valueIds as $vid) {
                $aRes = mysqli_query($conn, "SELECT attribute_id FROM product_attribute_values WHERE id=$vid LIMIT 1");
                $aRow = mysqli_fetch_assoc($aRes);
                $attrId = (int)$aRow['attribute_id'];
                mysqli_query($conn, "INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
                    VALUES ($variantId, $attrId, $vid)");
            }

            $created[] = ['id' => $variantId, 'sku' => $variantSku, 'name' => $variantName];
        }

        syncProductFromVariants($conn, $productId);

        echo json_encode([
            'success' => true,
            'message' => count($created) . ' variante(s) generada(s)',
            'created' => $created,
        ], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * CREAR una variante manualmente
     * =================================================== */
    case 'create':
        $productId     = (int)($_POST['product_id'] ?? 0);
        $sku           = trim($_POST['sku'] ?? '');
        $name          = trim($_POST['name'] ?? '');
        $comparePrice  = $_POST['compare_price'] !== '' ? (float)$_POST['compare_price'] : 'NULL';
        $costPrice     = $_POST['cost_price'] !== '' ? (float)$_POST['cost_price'] : 'NULL';
        $stock         = (int)($_POST['stock'] ?? 0);
        $isActive      = (int)($_POST['is_active'] ?? 1);
        $sortOrder     = (int)($_POST['sort_order'] ?? 0);
        $attrValueIds  = $_POST['attribute_value_ids'] ?? [];

        if (!$productId || $sku === '') {
            echo json_encode(['success' => false, 'message' => 'Producto y SKU requeridos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if ($_POST['price'] !== '') {
            $price = (float)$_POST['price'];
        } else {
            $prodRes = mysqli_query($conn, "SELECT price FROM products WHERE id=$productId LIMIT 1");
            $prodRow = mysqli_fetch_assoc($prodRes);
            $price = ($prodRow && $prodRow['price'] !== null) ? (float)$prodRow['price'] : 0;
        }

        $skuE = mysqli_real_escape_string($conn, $sku);
        $skuCheck = mysqli_query($conn, "SELECT id FROM product_variants WHERE sku='$skuE' LIMIT 1");
        if (mysqli_num_rows($skuCheck) > 0) {
            echo json_encode(['success' => false, 'message' => "El SKU « $sku » ya está en uso"], JSON_UNESCAPED_UNICODE);
            break;
        }

        $nE = mysqli_real_escape_string($conn, $name);

        $sql = "INSERT INTO product_variants
                    (product_id, sku, name, price, compare_price, cost_price, stock, is_active, sort_order)
                VALUES ($productId, '$skuE', '$nE', $price, $comparePrice, $costPrice, $stock, $isActive, $sortOrder)";

        if (mysqli_query($conn, $sql)) {
            $variantId = mysqli_insert_id($conn);
            foreach ($attrValueIds as $vid) {
                $vid   = (int)$vid;
                $aRes  = mysqli_query($conn, "SELECT attribute_id FROM product_attribute_values WHERE id=$vid LIMIT 1");
                $aRow  = mysqli_fetch_assoc($aRes);
                $attrId = (int)$aRow['attribute_id'];
                mysqli_query($conn, "INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
                    VALUES ($variantId, $attrId, $vid)");
            }

            syncProductFromVariants($conn, $productId);

            echo json_encode([
                'success'    => true,
                'message'    => 'Variante creada correctamente',
                'variant_id' => $variantId,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR variante
     * =================================================== */
    case 'update':
        $id            = (int)($_POST['id'] ?? 0);
        $sku           = trim($_POST['sku'] ?? '');
        $name          = trim($_POST['name'] ?? '');
        $comparePrice  = $_POST['compare_price'] !== '' ? (float)$_POST['compare_price'] : 'NULL';
        $costPrice     = $_POST['cost_price'] !== '' ? (float)$_POST['cost_price'] : 'NULL';
        $stock         = (int)($_POST['stock'] ?? 0);
        $isActive      = (int)($_POST['is_active'] ?? 1);
        $sortOrder     = (int)($_POST['sort_order'] ?? 0);

        if (!$id || $sku === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $skuE = mysqli_real_escape_string($conn, $sku);
        $skuCheck = mysqli_query($conn, "SELECT id, product_id FROM product_variants WHERE sku='$skuE' AND id != $id LIMIT 1");
        if (mysqli_num_rows($skuCheck) > 0) {
            echo json_encode(['success' => false, 'message' => "El SKU « $sku » ya está en uso por otra variante"], JSON_UNESCAPED_UNICODE);
            break;
        }

        if ($_POST['price'] !== '') {
            $price = (float)$_POST['price'];
        } else {
            $cur = mysqli_query($conn, "SELECT price FROM product_variants WHERE id=$id LIMIT 1");
            $curRow = mysqli_fetch_assoc($cur);
            $price = ($curRow && $curRow['price'] !== null) ? (float)$curRow['price'] : 'NULL';
        }

        $nE = mysqli_real_escape_string($conn, $name);

        $sql = "UPDATE product_variants SET
                    sku='$skuE', name='$nE', price=$price, compare_price=$comparePrice,
                    cost_price=$costPrice, stock=$stock, is_active=$isActive, sort_order=$sortOrder
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Variante actualizada correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR variante
     * =================================================== */
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $vRes = mysqli_query($conn, "SELECT product_id FROM product_variants WHERE id=$id LIMIT 1");
        $vRow = mysqli_fetch_assoc($vRes);
        if (!$vRow) {
            echo json_encode(['success' => false, 'message' => 'Variante no encontrada'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $productId = (int)$vRow['product_id'];

        mysqli_query($conn, "DELETE FROM product_variant_attributes WHERE variant_id=$id");
        mysqli_query($conn, "DELETE FROM product_variants WHERE id=$id");

        syncProductFromVariants($conn, $productId);

        echo json_encode(['success' => true, 'message' => 'Variante eliminada correctamente'], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
