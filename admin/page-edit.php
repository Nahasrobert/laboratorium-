<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$editing = $slug !== '';
$page = ['slug' => '', 'menu_label' => '', 'nav_group' => 'halaman', 'title' => '', 'eyebrow' => '', 'intro' => '', 'body' => '', 'image_path' => '', 'published' => 1, 'sort_order' => 20];
if ($editing) {
    $statement = $db->prepare('SELECT * FROM pages WHERE slug = ?');
    $statement->execute([$slug]);
    $page = $statement->fetch();
    if (!$page) {
        http_response_code(404);
        exit('Halaman tidak ditemukan.');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $page['menu_label'] = trim(isset($_POST['menu_label']) ? (string) $_POST['menu_label'] : '');
    $page['title'] = trim(isset($_POST['title']) ? (string) $_POST['title'] : '');
    $page['nav_group'] = isset($_POST['nav_group']) && in_array($_POST['nav_group'], ['profil', 'layanan', 'halaman'], true) ? $_POST['nav_group'] : 'halaman';
    $page['eyebrow'] = trim(isset($_POST['eyebrow']) ? (string) $_POST['eyebrow'] : '');
    $page['intro'] = trim(isset($_POST['intro']) ? (string) $_POST['intro'] : '');
    $page['body'] = trim(isset($_POST['body']) ? (string) $_POST['body'] : '');
    $page['published'] = isset($_POST['published']) ? 1 : 0;
    $page['sort_order'] = max(0, (int) (isset($_POST['sort_order']) ? $_POST['sort_order'] : 0));

    try {
        if ($page['menu_label'] === '' || $page['title'] === '') {
            throw new RuntimeException('Nama menu dan judul harus diisi.');
        }
        if (!$editing) {
            $page['slug'] = admin_slug(isset($_POST['slug']) ? $_POST['slug'] : $page['menu_label']);
            if ($page['slug'] === '' || $page['slug'] === 'index') {
                throw new RuntimeException('Slug halaman tidak valid.');
            }
        }
        $requestedImagePath = isset($_POST['image_path']) ? $_POST['image_path'] : (isset($page['image_path']) ? $page['image_path'] : '');
        $page['image_path'] = validate_image_path(save_image_upload('image', $requestedImagePath));
        if ($page['slug'] === 'beranda') {
            $page['published'] = 1;
        }
        if ($editing) {
            $statement = $db->prepare('UPDATE pages SET menu_label=?, nav_group=?, title=?, eyebrow=?, intro=?, body=?, image_path=?, published=?, sort_order=?, updated_at=CURRENT_TIMESTAMP WHERE slug=?');
            $statement->execute([$page['menu_label'], $page['nav_group'], $page['title'], $page['eyebrow'], $page['intro'], $page['body'], $page['image_path'], $page['published'], $page['sort_order'], $slug]);
        } else {
            $statement = $db->prepare('INSERT INTO pages (slug, menu_label, nav_group, title, eyebrow, intro, body, image_path, published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $statement->execute([$page['slug'], $page['menu_label'], $page['nav_group'], $page['title'], $page['eyebrow'], $page['intro'], $page['body'], $page['image_path'], $page['published'], $page['sort_order']]);
        }
        header('Location: pages.php?saved=1');
        exit;
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        $error = 'Slug sudah digunakan atau data tidak dapat disimpan.';
    }
}

admin_layout_header($editing ? 'Edit Halaman' : 'Tambah Halaman', 'pages');
?>
<div class="mb-6"><a href="pages.php" class="text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke halaman situs</a><h1 class="mt-4 text-3xl font-extrabold text-slate-900"><?= $editing ? 'Edit halaman' : 'Tambah halaman' ?></h1></div>
<?php if ($error !== ''): ?><div class="mb-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-800"><?= h($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <?= csrf_field() ?>
    <div class="grid gap-5 sm:grid-cols-2">
        <?php if (!$editing): ?><label class="block text-sm font-semibold">Slug URL<input name="slug" value="<?= h($page['slug']) ?>" placeholder="contoh-halaman" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" required></label><?php endif; ?>
        <label class="block text-sm font-semibold">Nama menu<input name="menu_label" value="<?= h($page['menu_label']) ?>" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" required></label>
        <label class="block text-sm font-semibold">Kelompok menu<select name="nav_group" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="halaman" <?= $page['nav_group'] === 'halaman' ? 'selected' : '' ?>>Halaman umum</option><option value="profil" <?= $page['nav_group'] === 'profil' ? 'selected' : '' ?>>Profil</option><option value="layanan" <?= $page['nav_group'] === 'layanan' ? 'selected' : '' ?>>Layanan &amp; Praktikum</option></select></label>
        <label class="block text-sm font-semibold sm:col-span-2">Judul halaman<input name="title" value="<?= h($page['title']) ?>" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" required></label>
        <label class="block text-sm font-semibold">Label kecil / kategori<input name="eyebrow" value="<?= h($page['eyebrow']) ?>" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold">Urutan menu<input type="number" min="0" name="sort_order" value="<?= (int) $page['sort_order'] ?>" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold sm:col-span-2">Ringkasan<textarea name="intro" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h(str_replace('|', "\n", $page['intro'])) ?></textarea><span class="mt-1 block text-xs font-normal text-slate-500">Ringkasan ditampilkan di bagian atas halaman.</span></label>
        <label class="block text-sm font-semibold sm:col-span-2">Isi halaman<textarea name="body" rows="10" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h(str_replace('|', "\n\n", $page['body'])) ?></textarea><span class="mt-1 block text-xs font-normal text-slate-500">Pisahkan paragraf atau poin dengan baris kosong. HTML tidak dijalankan demi keamanan.</span></label>
        <label class="block text-sm font-semibold">Gambar halaman<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-3 text-sm"><span class="mt-1 block text-xs font-normal text-slate-500">Opsional, JPG/PNG/WEBP/GIF maksimal 5 MB. File disimpan di uploads.</span></label>
        <label class="block text-sm font-semibold">Path gambar saat ini<input name="image_path" value="<?= h($page['image_path']) ?>" placeholder="assets/fasilitas-lab.svg" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><span class="mt-1 block text-xs font-normal text-slate-500">Gunakan file lokal dalam folder assets/ atau uploads/.</span></label>
        <label class="flex items-center gap-3 text-sm font-semibold"><input type="checkbox" name="published" value="1" <?= $page['published'] ? 'checked' : '' ?> class="h-4 w-4 accent-blue-800">Terbitkan halaman</label>
    </div>
    <button class="rounded-xl bg-undanaBlue px-6 py-3 font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-floppy-disk mr-2"></i>Simpan halaman</button>
</form>
<?php admin_layout_footer(); ?>
