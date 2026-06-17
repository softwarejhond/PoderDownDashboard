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

$author = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anónimo';

switch ($action) {

    case 'list':
        $sql = "SELECT id, title, slug, excerpt, featured_image, status, author, created_at, updated_at
                FROM blog_posts ORDER BY created_at DESC";
        $res  = mysqli_query($conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    case 'get':
        $id = (int)($_REQUEST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res = mysqli_query($conn, "SELECT * FROM blog_posts WHERE id = $id LIMIT 1");
        if ($row = mysqli_fetch_assoc($res)) {
            echo json_encode(['success' => true, 'data' => $row], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post no encontrado'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'create':
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $featured_image = trim($_POST['featured_image'] ?? '');
        $status  = trim($_POST['status'] ?? 'draft');

        if ($title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'El título y el contenido son obligatorios'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        $slug   = uniqueSlug($conn, 'blog_posts', slugify($title));
        $t      = mysqli_real_escape_string($conn, $title);
        $sl     = mysqli_real_escape_string($conn, $slug);
        $c      = mysqli_real_escape_string($conn, $content);
        $e      = mysqli_real_escape_string($conn, $excerpt);
        $fi     = mysqli_real_escape_string($conn, $featured_image);
        $st     = mysqli_real_escape_string($conn, $status);
        $au     = mysqli_real_escape_string($conn, $author);

        $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, status, author)
                VALUES ('$t', '$sl', '$c', '$e', '$fi', '$st', '$au')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Post creado correctamente',
                'post_id' => mysqli_insert_id($conn),
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'update':
        $id      = (int)($_POST['id'] ?? 0);
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $featured_image = trim($_POST['featured_image'] ?? '');
        $status  = trim($_POST['status'] ?? 'draft');

        if (!$id || $title === '' || $content === '') {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (!in_array($status, ['draft', 'published'])) {
            $status = 'draft';
        }

        $t      = mysqli_real_escape_string($conn, $title);
        $c      = mysqli_real_escape_string($conn, $content);
        $e      = mysqli_real_escape_string($conn, $excerpt);
        $fi     = mysqli_real_escape_string($conn, $featured_image);
        $st     = mysqli_real_escape_string($conn, $status);

        $sql = "UPDATE blog_posts SET
                    title = '$t',
                    content = '$c',
                    excerpt = '$e',
                    featured_image = '$fi',
                    status = '$st'
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'Post actualizado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $res = mysqli_query($conn, "SELECT featured_image FROM blog_posts WHERE id = $id LIMIT 1");
        if ($row = mysqli_fetch_assoc($res)) {
            if (!empty($row['featured_image'])) {
                $imgPath = __DIR__ . '/../../' . $row['featured_image'];
                if (file_exists($imgPath)) {
                    @unlink($imgPath);
                }
            }
        }
        if (mysqli_query($conn, "DELETE FROM blog_posts WHERE id = $id")) {
            echo json_encode(['success' => true, 'message' => 'Post eliminado correctamente'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida'], JSON_UNESCAPED_UNICODE);
}
