<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../conexion.php';

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

switch ($action) {

    /* ===================================================
     * LISTAR atributos con sus valores
     * =================================================== */
    case 'get':
        $sql = "SELECT a.*,
                       (SELECT COUNT(*) FROM product_attribute_values v WHERE v.attribute_id = a.id) AS total_values
                FROM product_attributes a
                ORDER BY a.sort_order ASC, a.name ASC";
        $res  = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * LISTAR valores de un atributo específico
     * =================================================== */
    case 'get_values':
        $attrId = (int)($_GET['attribute_id'] ?? 0);
        if (!$attrId) {
            echo json_encode(['success' => false, 'message' => 'ID de atributo requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res  = mysqli_query($conn, "SELECT * FROM product_attribute_values WHERE attribute_id=$attrId ORDER BY sort_order ASC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * CREAR atributo
     * =================================================== */
    case 'create_attribute':
        $name       = trim($_POST['name'] ?? '');
        $type       = trim($_POST['type'] ?? 'select');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'El nombre del atributo es obligatorio'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (!in_array($type, ['select', 'color', 'text'])) {
            echo json_encode(['success' => false, 'message' => 'Tipo inválido. Use: select, color o text'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $slug = uniqueSlug($conn, 'product_attributes', slugify($name));
        $n    = mysqli_real_escape_string($conn, $name);
        $t    = mysqli_real_escape_string($conn, $type);
        $sl   = mysqli_real_escape_string($conn, $slug);

        $sql = "INSERT INTO product_attributes (name, slug, type, sort_order)
                VALUES ('$n', '$sl', '$t', $sort_order)";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success'      => true,
                'message'      => 'Atributo creado correctamente',
                'attribute_id' => mysqli_insert_id($conn),
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR atributo
     * =================================================== */
    case 'update_attribute':
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $type       = trim($_POST['type'] ?? 'select');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (!$id || $name === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (!in_array($type, ['select', 'color', 'text'])) {
            echo json_encode(['success' => false, 'message' => 'Tipo inválido. Use: select, color o text'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $n = mysqli_real_escape_string($conn, $name);
        $t = mysqli_real_escape_string($conn, $type);

        $sql = "UPDATE product_attributes
                SET name='$n', type='$t', sort_order=$sort_order
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Atributo actualizado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR atributo (y sus valores asociados)
     * =================================================== */
    case 'delete_attribute':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        mysqli_query($conn, "DELETE FROM product_attribute_values WHERE attribute_id=$id");
        mysqli_query($conn, "DELETE FROM product_attributes WHERE id=$id");
        echo json_encode(['success' => true, 'message' => 'Atributo y sus valores eliminados correctamente'], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * AGREGAR valor a un atributo
     * =================================================== */
    case 'add_value':
        $attribute_id = (int)($_POST['attribute_id'] ?? 0);
        $value        = trim($_POST['value'] ?? '');
        $color_hex    = trim($_POST['color_hex'] ?? '');
        $sort_order   = (int)($_POST['sort_order'] ?? 0);

        if (!$attribute_id || $value === '') {
            echo json_encode(['success' => false, 'message' => 'El atributo y el valor son obligatorios'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $v  = mysqli_real_escape_string($conn, $value);
        $ch = $color_hex !== '' ? "'" . mysqli_real_escape_string($conn, $color_hex) . "'" : 'NULL';

        $sql = "INSERT INTO product_attribute_values (attribute_id, value, color_hex, sort_order)
                VALUES ($attribute_id, '$v', $ch, $sort_order)";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Valor agregado correctamente',
                'value_id' => mysqli_insert_id($conn),
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR valor
     * =================================================== */
    case 'update_value':
        $id        = (int)($_POST['id'] ?? 0);
        $value     = trim($_POST['value'] ?? '');
        $color_hex = trim($_POST['color_hex'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (!$id || $value === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $v  = mysqli_real_escape_string($conn, $value);
        $ch = $color_hex !== '' ? "'" . mysqli_real_escape_string($conn, $color_hex) . "'" : 'NULL';

        $sql = "UPDATE product_attribute_values
                SET value='$v', color_hex=$ch, sort_order=$sort_order
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Valor actualizado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR valor
     * =================================================== */
    case 'delete_value':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        mysqli_query($conn, "DELETE FROM product_variant_attributes WHERE attribute_value_id=$id");
        mysqli_query($conn, "DELETE FROM product_attribute_values WHERE id=$id");
        echo json_encode(['success' => true, 'message' => 'Valor eliminado correctamente'], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
