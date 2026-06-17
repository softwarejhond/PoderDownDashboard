<?php
header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ninguna imagen o hubo un error en la subida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['image'];

$allowedMime = 'image/png';
$allowedExt  = 'png';
$maxSize     = 2 * 1024 * 1024;

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mimeType = mime_content_type($file['tmp_name']);

if ($ext !== $allowedExt || $mimeType !== $allowedMime) {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten imágenes PNG'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'La imagen no debe superar los 2 MB'], JSON_UNESCAPED_UNICODE);
    exit;
}

$uploadDir = __DIR__ . '/../../img/blog/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$newName = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
$destPath = $uploadDir . $newName;

if (move_uploaded_file($file['tmp_name'], $destPath)) {
    $dbPath = 'img/blog/' . $newName;
    echo json_encode([
        'success' => true,
        'message' => 'Imagen subida correctamente',
        'url'     => $dbPath,
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al mover la imagen al directorio de destino'], JSON_UNESCAPED_UNICODE);
}
