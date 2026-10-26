<?php
require_once __DIR__ . '/bootstrap.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim(isset($_POST['username']) ? (string) $_POST['username'] : '');
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $statement = $db->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $statement->execute([$username]);
    $admin = $statement->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php');
        exit;
    }
    $error = 'Nama login atau password tidak sesuai.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Laboratorium Pendidikan Kimia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{undanaBlue:'#003366',undanaGold:'#FFB800',undanaDark:'#0A192F'}}}};</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
    <main class="w-full max-w-md rounded-3xl bg-white p-7 shadow-xl sm:p-9">
        <a href="../index.php" class="mb-8 inline-flex items-center gap-3">
            <img src="../Logo_Undana.png" alt="Logo Universitas Nusa Cendana" class="h-12 w-12 object-contain">
            <span><span class="block font-extrabold text-undanaBlue">Admin Laboratorium</span><span class="block text-xs text-slate-500">Pendidikan Kimia FKIP Undana</span></span>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900">Masuk ke dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Kelola halaman, peralatan, dan berita situs laboratorium.</p>
        <!-- <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs leading-relaxed text-blue-900">Untuk membuat akun admin pertama, jalankan dari folder proyek: <code class="font-bold">php admin/create_admin.php nama-admin</code>. Password dibuat di terminal dan tidak memakai kredensial bawaan.</div> -->
        <?php if ($error !== ''): ?><div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700"><?= h($error) ?></div><?php endif; ?>
        <form method="post" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <label class="block text-sm font-semibold text-slate-700">Nama login
                <input name="username" type="text" autocomplete="username" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-undanaBlue focus:ring-2 focus:ring-blue-100">
            </label>
            <label class="block text-sm font-semibold text-slate-700">Password
                <input name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-undanaBlue focus:ring-2 focus:ring-blue-100">
            </label>
            <button type="submit" class="w-full rounded-xl bg-undanaBlue px-4 py-3 font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-right-to-bracket mr-2"></i>Login</button>
        </form>
        <a href="../index.php" class="mt-5 block text-center text-sm text-slate-500 hover:text-undanaBlue">Kembali ke situs</a>
    </main>
</body>
</html>
