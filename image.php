<?php
/**
 * image.php — отдаёт изображение товара из MEDIUMBLOB колонки базы данных
 * Использование: <img src="image.php?id=5">
 */
require_once 'includes/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { http_response_code(404); exit; }

$stmt = db()->prepare("SELECT image, image_type FROM products WHERE id = ? AND image IS NOT NULL");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row || !$row['image']) {
    http_response_code(404);
    exit;
}

// Определяем MIME-тип
$mime = $row['image_type'] ?? 'image/jpeg';

// Кешируем на 7 дней
$etag = md5($id . strlen($row['image']));
header('Cache-Control: public, max-age=604800');
header('ETag: "' . $etag . '"');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === '"'.$etag.'"') {
    http_response_code(304);
    exit;
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($row['image']));
echo $row['image'];
