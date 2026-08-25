<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../conexion.php';

session_start();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';

function slugify(string $str): string {
    $from = ['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ü','Ñ',
             'à','â','ä','è','ê','ë','î','ï','ô','ù','û','ü','ÿ','ç'];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','u','n',
             'a','a','a','e','e','e','i','i','o','u','u','u','y','c'];
    $str  = str_replace($from, $to, mb_strtolower(trim($str), 'UTF-8'));
    $str  = preg_replace('/[^a-z0-9\s-]/', '', $str);
    return preg_replace('/[\s-]+/', '-', $str);
}

function uniqueSlug($conn, string $table, string $slug, ?int $excludeId = null): string {
    $base = $slug !== '' ? $slug : 'galeria';
    $slug = $base;
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

$author = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anónimo';

function galeriaUnlinkFile(string $img): void {
    $pos = strpos($img, 'img/galeria/');
    if ($pos === false) return;
    $rel = substr($img, $pos);
    $p   = __DIR__ . '/../../' . $rel;
    if (file_exists($p)) @unlink($p);
}

switch ($action) {

    /* ===================================================
     * LISTAR galerías (con conteo de obras)
     * =================================================== */
    case 'list':
        $sql = "SELECT g.id, g.title, g.slug, g.excerpt, g.featured_image, g.author, g.status,
                       g.created_at, g.updated_at,
                       (SELECT COUNT(*) FROM galeria_obras o WHERE o.galeria_id = g.id) AS total_obras
                FROM galerias g
                ORDER BY g.created_at DESC";
        $res  = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * OBTENER una galería
     * =================================================== */
    case 'get':
        $id = (int)($_REQUEST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res = mysqli_query($conn, "SELECT * FROM galerias WHERE id = $id LIMIT 1");
        if ($row = mysqli_fetch_assoc($res)) {
            echo json_encode(['success' => true, 'data' => $row], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Galería no encontrada'], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * CREAR galería
     * =================================================== */
    case 'create':
        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status  = trim($_POST['status'] ?? 'published');

        if ($title === '') {
            echo json_encode(['success' => false, 'message' => 'El título es obligatorio'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'published';
        }

        $slug = uniqueSlug($conn, 'galerias', slugify($title));
        $t    = mysqli_real_escape_string($conn, $title);
        $sl   = mysqli_real_escape_string($conn, $slug);
        $e    = mysqli_real_escape_string($conn, $excerpt);
        $st   = mysqli_real_escape_string($conn, $status);
        $au   = mysqli_real_escape_string($conn, $author);

        $sql = "INSERT INTO galerias (title, slug, excerpt, featured_image, author, status)
                VALUES ('$t', '$sl', '$e', '', '$au', '$st')";

        if (mysqli_query($conn, $sql)) {
            $galeriaId = mysqli_insert_id($conn);

            $emailOk  = false;
            $emailSent = 0;
            $msg = 'Galería creada correctamente';
            if ($status === 'published') {
                require_once __DIR__ . '/../newsletter.php';
                $result = notify_newsletter_subscribers($conn, 'galeria', [
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $excerpt,
                    'featured_image' => '',
                ]);
                $emailOk   = $result['success'];
                $emailSent = $result['sent'];
                if ($emailOk && $emailSent > 0) {
                    $msg .= ' y se notificó a ' . $emailSent . ' suscriptor(es)';
                } elseif (!$emailOk) {
                    $msg .= ', pero el correo de notificación falló';
                }
            }

            echo json_encode([
                'success'    => true,
                'message'    => $msg,
                'galeria_id' => $galeriaId,
                'slug'       => $slug,
                'email_sent' => $emailSent,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ACTUALIZAR galería
     * =================================================== */
    case 'update':
        $id      = (int)($_POST['id'] ?? 0);
        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $status  = trim($_POST['status'] ?? 'published');

        if (!$id || $title === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'published';
        }

        $slug = uniqueSlug($conn, 'galerias', slugify($title), $id);

        $oldStatus = 'draft';
        $oldRes = mysqli_query($conn, "SELECT status FROM galerias WHERE id = $id LIMIT 1");
        if ($oldRow = mysqli_fetch_assoc($oldRes)) {
            $oldStatus = $oldRow['status'];
        }

        $t    = mysqli_real_escape_string($conn, $title);
        $sl   = mysqli_real_escape_string($conn, $slug);
        $e    = mysqli_real_escape_string($conn, $excerpt);
        $st   = mysqli_real_escape_string($conn, $status);

        $sql = "UPDATE galerias SET
                    title = '$t',
                    slug = '$sl',
                    excerpt = '$e',
                    status = '$st'
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            $emailOk  = false;
            $emailSent = 0;
            $msg = 'Galería actualizada correctamente';
            if ($status === 'published' && $oldStatus !== 'published') {
                require_once __DIR__ . '/../newsletter.php';
                $result = notify_newsletter_subscribers($conn, 'galeria', [
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $excerpt,
                    'featured_image' => '',
                ]);
                $emailOk   = $result['success'];
                $emailSent = $result['sent'];
                if ($emailOk && $emailSent > 0) {
                    $msg .= ' y se notificó a ' . $emailSent . ' suscriptor(es)';
                } elseif (!$emailOk) {
                    $msg .= ', pero el correo de notificación falló';
                }
            }

            echo json_encode([
                'success'    => true,
                'message'    => $msg,
                'slug'       => $slug,
                'email_sent' => $emailSent,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR galería (y sus obras + archivos físicos)
     * =================================================== */
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $res = mysqli_query($conn, "SELECT img FROM galeria_obras WHERE galeria_id = $id");
        while ($row = mysqli_fetch_assoc($res)) {
            if (!empty($row['img'])) galeriaUnlinkFile($row['img']);
        }

        if (mysqli_query($conn, "DELETE FROM galerias WHERE id = $id")) {
            echo json_encode(['success' => true, 'message' => 'Galería eliminada correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * LISTAR obras de una galería
     * =================================================== */
    case 'get_obras':
        $galeriaId = (int)($_REQUEST['galeria_id'] ?? 0);
        if (!$galeriaId) {
            echo json_encode(['success' => false, 'message' => 'ID de galería requerido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $fRes = mysqli_query($conn, "SELECT featured_image FROM galerias WHERE id = $galeriaId LIMIT 1");
        $fRow = mysqli_fetch_assoc($fRes);
        $featured = $fRow ? ($fRow['featured_image'] ?? '') : '';

        $res  = mysqli_query($conn, "SELECT * FROM galeria_obras WHERE galeria_id = $galeriaId ORDER BY sort_order ASC, id ASC");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $row['is_featured'] = ($featured !== '' && $row['img'] === $featured) ? 1 : 0;
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data, 'featured_image' => $featured], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * ACTUALIZAR datos de una obra (title / meta / descripcion)
     * =================================================== */
    case 'update_obra':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $title = trim($_POST['title'] ?? '');
        $meta  = trim($_POST['meta'] ?? '');
        $desc  = trim($_POST['descripcion'] ?? '');

        $t = mysqli_real_escape_string($conn, $title);
        $m = mysqli_real_escape_string($conn, $meta);
        $d = mysqli_real_escape_string($conn, $desc);

        $sql = "UPDATE galeria_obras SET title = '$t', meta = '$m', descripcion = '$d' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Obra actualizada'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la obra: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    /* ===================================================
     * ELIMINAR obra (y archivo físico)
     * =================================================== */
    case 'delete_obra':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res = mysqli_query($conn, "SELECT galeria_id, img FROM galeria_obras WHERE id = $id LIMIT 1");
        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Obra no encontrada'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (!empty($row['img'])) galeriaUnlinkFile($row['img']);

        mysqli_query($conn, "DELETE FROM galeria_obras WHERE id = $id");

        $galeriaId = (int)$row['galeria_id'];
        $imgE      = mysqli_real_escape_string($conn, $row['img']);
        mysqli_query($conn, "UPDATE galerias SET featured_image = '' WHERE id = $galeriaId AND featured_image = '$imgE'");

        echo json_encode(['success' => true, 'message' => 'Obra eliminada correctamente'], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * REORDENAR obras (drag & drop)
     * =================================================== */
    case 'reorder':
        $order = $_POST['order'] ?? [];
        if (empty($order)) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el orden'], JSON_UNESCAPED_UNICODE);
            break;
        }
        foreach ($order as $i => $id) {
            $id = (int)$id;
            $i  = (int)$i;
            mysqli_query($conn, "UPDATE galeria_obras SET sort_order = $i WHERE id = $id");
        }
        echo json_encode(['success' => true, 'message' => 'Orden actualizado'], JSON_UNESCAPED_UNICODE);
        break;

    /* ===================================================
     * MARCAR OBRA COMO PORTADA (featured_image)
     * =================================================== */
    case 'set_featured':
        $id        = (int)($_POST['id'] ?? 0);
        $galeriaId = (int)($_POST['galeria_id'] ?? 0);
        if (!$id || !$galeriaId) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res = mysqli_query($conn, "SELECT img FROM galeria_obras WHERE id = $id AND galeria_id = $galeriaId LIMIT 1");
        $row = mysqli_fetch_assoc($res);
        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Obra no encontrada'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $imgE = mysqli_real_escape_string($conn, $row['img']);
        mysqli_query($conn, "UPDATE galerias SET featured_image = '$imgE' WHERE id = $galeriaId");
        echo json_encode(['success' => true, 'message' => 'Portada actualizada', 'featured_image' => $row['img']], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
