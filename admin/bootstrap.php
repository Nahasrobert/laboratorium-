<?php

require_once dirname(__DIR__) . '/app/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isSecure,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$db = app_db();

function require_admin()
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf()
{
    $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if ($token === '' || !hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba kembali.');
    }
}

function admin_slug($value)
{
    $value = strtolower(trim((string) $value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

function save_image_upload($field, $currentPath = '')
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Unggah gambar gagal. Silakan pilih ulang file.');
    }
    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES[$field]['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime]) || getimagesize($_FILES[$field]['tmp_name']) === false) {
        throw new RuntimeException('Format gambar harus JPG, PNG, WEBP, atau GIF.');
    }

    $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Direktori unggahan tidak dapat dibuat.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
        throw new RuntimeException('Gambar tidak dapat disimpan.');
    }

    return 'uploads/' . $filename;
}

function validate_image_path($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    if (strpos($path, '..') !== false || !preg_match('~^(assets|uploads)/[A-Za-z0-9._/-]+$~', $path)) {
        throw new RuntimeException('Path gambar harus mengarah ke file lokal pada folder assets atau uploads.');
    }
    return $path;
}

function admin_layout_header($title, $active)
{
    $adminName = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';
    $links = [
        'Dashboard' => ['index.php', 'dashboard'],
        'Halaman Situs' => ['pages.php', 'pages'],
        'Peralatan' => ['equipment.php', 'equipment'],
        'Berita' => ['news.php', 'news'],
        'Pengaturan Situs' => ['settings.php', 'settings'],
    ];
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h($title) ?> - Admin Lab Pendidikan Kimia</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { theme: { extend: { colors: { undanaBlue: '#003366', undanaGold: '#FFB800', undanaDark: '#0A192F' } } } };
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css">
        <style>body{font-family:Arial,sans-serif}.dataTables_wrapper{overflow-x:auto}.dt-container{width:100%}</style>
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800">
        <header class="sticky top-0 z-40 bg-undanaDark text-white shadow-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <a href="index.php" class="flex items-center gap-3 font-bold">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white p-1"><img src="../Logo_Undana.png" alt="Logo Undana" class="h-full w-full object-contain"></span>
                    <span><span class="block text-sm">Admin Laboratorium</span><span class="block text-xs font-normal text-slate-300">Halo, <?= h($adminName) ?></span></span>
                </a>
                <button id="admin-nav-toggle" type="button" aria-expanded="false" aria-controls="admin-nav" class="rounded-lg p-2 hover:bg-white/10 lg:hidden" aria-label="Buka navigasi admin"><i class="fa-solid fa-bars text-xl"></i></button>
                <nav id="admin-nav" class="absolute left-0 right-0 top-full hidden border-t border-white/10 bg-undanaDark p-3 lg:static lg:flex lg:border-0 lg:bg-transparent lg:p-0">
                    <?php foreach ($links as $label => $link): ?>
                        <a href="<?= h($link[0]) ?>" class="block rounded-lg px-3 py-2 text-sm <?= $active === $link[1] ? 'bg-white/15 text-undanaGold' : 'text-slate-200 hover:bg-white/10' ?>"><?= h($label) ?></a>
                    <?php endforeach; ?>
                    <a href="logout.php" class="block rounded-lg px-3 py-2 text-sm text-rose-200 hover:bg-rose-500/20"><i class="fa-solid fa-right-from-bracket mr-2"></i>Keluar</a>
                </nav>
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
    <?php
}

function admin_layout_footer()
{
    ?>
        </main>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const navToggle = document.getElementById('admin-nav-toggle');
            const adminNav = document.getElementById('admin-nav');
            if (navToggle && adminNav) {
                navToggle.addEventListener('click', () => {
                    const open = navToggle.getAttribute('aria-expanded') === 'true';
                    navToggle.setAttribute('aria-expanded', String(!open));
                    adminNav.classList.toggle('hidden', open);
                });
            }
            document.querySelectorAll('[data-confirm]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Hapus data ini?',
                        text: 'Data yang dihapus tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#b91c1c',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });
            const params = new URLSearchParams(window.location.search);
            if (params.get('saved') === '1') {
                Swal.fire({ icon: 'success', title: 'Tersimpan', text: 'Perubahan berhasil disimpan.', timer: 1700, showConfirmButton: false });
            } else if (params.get('deleted') === '1') {
                Swal.fire({ icon: 'success', title: 'Terhapus', text: 'Data berhasil dihapus.', timer: 1700, showConfirmButton: false });
            }
        </script>
    </body>
    </html>
    <?php
}
