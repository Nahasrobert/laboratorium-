<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
verify_csrf();

$type = isset($_POST['type']) && is_string($_POST['type']) ? $_POST['type'] : '';
$slug = isset($_POST['slug']) && is_string($_POST['slug']) ? $_POST['slug'] : '';
$tables = ['pages' => 'pages', 'equipment' => 'equipment', 'news' => 'news'];
if (!isset($tables[$type]) || $slug === '') {
    http_response_code(400);
    exit('Jenis data atau identitas tidak valid.');
}
if ($type === 'pages' && $slug === 'beranda') {
    http_response_code(400);
    exit('Halaman beranda tidak dapat dihapus.');
}

$statement = $db->prepare('DELETE FROM ' . $tables[$type] . ' WHERE slug = ?');
$statement->execute([$slug]);
if ($statement->rowCount() === 0) {
    http_response_code(404);
    exit('Data tidak ditemukan.');
}
header('Location: ' . $type . '.php?deleted=1');
exit;
