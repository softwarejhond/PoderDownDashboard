<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../conexion.php';

session_start();

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ninguna imagen o hubo un error en la subida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$galeriaId = (int)($_POST['galeria_id'] ?? 0);
if (!$galeriaId) {
    echo json_encode(['success' => false, 'message' => 'Debes crear la galería antes de subir obras'], JSON_UNESCAPED_UNICODE);
    exit;
}

$gRes = mysqli_query($conn, "SELECT slug FROM galerias WHERE id = $galeriaId LIMIT 1");
$gRow = mysqli_fetch_assoc($gRes);
if (!$gRow) {
    echo json_encode(['success' => false, 'message' => 'Galería no encontrada'], JSON_UNESCAPED_UNICODE);
    exit;
}
$slug = $gRow['slug'];

$file = $_FILES['image'];

$allowedMime = ['image/webp', 'image/avif'];
$allowedExt  = ['webp', 'avif'];
$maxSize     = 5 * 1024 * 1024;

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mimeType = mime_content_type($file['tmp_name']);

if (!in_array($ext, $allowedExt) || !in_array($mimeType, $allowedMime)) {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten imágenes WEBP o AVIF'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 5 MB'], JSON_UNESCAPED_UNICODE);
    exit;
}

$uploadDir = __DIR__ . '/../../img/galeria/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$slugSafe = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);
$newName  = 'galeria_' . $slugSafe . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . $newName;

// Base URL según entorno: local o producción
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    $baseUrl = 'http://localhost/PODER-DOWN/';
} else {
    $baseUrl = 'https://dashboard.poderdown.com/';
}
$dbPath = $baseUrl . 'img/galeria/' . $newName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'message' => 'Error al mover la imagen al directorio de destino'], JSON_UNESCAPED_UNICODE);
    exit;
}

$maxRes   = mysqli_query($conn, "SELECT MAX(sort_order) AS mo FROM galeria_obras WHERE galeria_id = $galeriaId");
$maxRow   = mysqli_fetch_assoc($maxRes);
$sortOrder = (($maxRow['mo'] ?? -1) + 1);

$title    = pathinfo($file['name'], PATHINFO_FILENAME);
$dbPathE  = mysqli_real_escape_string($conn, $dbPath);
$titleE   = mysqli_real_escape_string($conn, $title);

$sql = "INSERT INTO galeria_obras (galeria_id, img, title, meta, descripcion, sort_order)
        VALUES ($galeriaId, '$dbPathE', '$titleE', '', '', $sortOrder)";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Obra subida correctamente',
        'obra'    => [
            'id'          => mysqli_insert_id($conn),
            'galeria_id'  => $galeriaId,
            'img'         => $dbPath,
            'title'       => $title,
            'meta'        => '',
            'descripcion' => '',
            'sort_order'  => $sortOrder,
        ],
    ], JSON_UNESCAPED_UNICODE);
} else {
    @unlink($destPath);
    echo json_encode(['success' => false, 'message' => 'Error al registrar la obra: ' . mysqli_error($conn)], JSON_UNESCAPED_UNICODE);
}
