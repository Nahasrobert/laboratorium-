<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

$username = isset($argv[1]) ? trim($argv[1]) : '';
if (!preg_match('/^[A-Za-z0-9._-]{3,40}$/', $username)) {
    fwrite(STDERR, "Penggunaan: php admin/create_admin.php NAMA_LOGIN\nNama login 3-40 karakter: huruf, angka, titik, garis bawah, atau strip.\n");
    exit(1);
}

$adminCount = (int) $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($adminCount > 0) {
    fwrite(STDERR, "Admin sudah ada. Akun awal hanya dapat dibuat saat tabel admin masih kosong.\n");
    exit(1);
}

fwrite(STDOUT, 'Password admin (minimal 12 karakter): ');
$password = trim((string) fgets(STDIN));
if (strlen($password) < 12) {
    fwrite(STDERR, "Password terlalu pendek.\n");
    exit(1);
}

$statement = $db->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
try {
    $statement->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
} catch (PDOException $exception) {
    fwrite(STDERR, "Admin tidak dapat dibuat: " . $exception->getMessage() . "\n");
    exit(1);
}

fwrite(STDOUT, "Akun admin berhasil dibuat. Silakan login di /admin/login.php.\n");
