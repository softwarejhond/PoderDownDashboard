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

switch ($action) {

    /* ===================================================
     * LISTAR todas las categorías con conteo de productos
     * =================================================== */
    case 'get':
        $sql = "SELECT c.*, COUNT(p.id) AS total_products
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id
                GROUP BY c.id
                ORDER BY c.sort_order ASC, c.name ASC";
        $res  = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * CREAR nueva categoría
     * =================================================== */
    case 'create':
        $name       = trim($_POST['name'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $icon       = trim($_POST['icon'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_active  = (int)($_POST['is_active'] ?? 1) ? 1 : 0;
        $is_feat    = (int)($_POST['is_featured'] ?? 0) ? 1 : 0;

        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $slug = uniqueSlug($conn, 'categories', slugify($name));
        $n    = mysqli_real_escape_string($conn, $name);
        $d    = mysqli_real_escape_string($conn, $desc);
        $ic   = mysqli_real_escape_string($conn, $icon);
        $sl   = mysqli_real_escape_string($conn, $slug);

        $sql = "INSERT INTO categories (name, slug, description, icon, sort_order, is_active, is_featured)
                VALUES ('$n', '$sl', '$d', '$ic', $sort_order, $is_active, $is_feat)";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Categoría creada correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR categoría existente
     * =================================================== */
    case 'update':
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $icon       = trim($_POST['icon'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_active  = (int)($_POST['is_active'] ?? 1) ? 1 : 0;
        $is_feat    = (int)($_POST['is_featured'] ?? 0) ? 1 : 0;

        if (!$id || $name === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $n  = mysqli_real_escape_string($conn, $name);
        $d  = mysqli_real_escape_string($conn, $desc);
        $ic = mysqli_real_escape_string($conn, $icon);

        $sql = "UPDATE categories
                SET name='$n', description='$d', icon='$ic',
                    sort_order=$sort_order, is_active=$is_active, is_featured=$is_feat
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR categoría (si no tiene productos)
     * =================================================== */
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM products WHERE category_id=$id"));
        if ($check['t'] > 0) {
            echo json_encode([
                'success' => false,
                'message' => "No se puede eliminar: la categoría tiene {$check['t']} producto(s) asociado(s)"
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (mysqli_query($conn, "DELETE FROM categories WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
