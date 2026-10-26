<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$editing = $slug !== '';
$item = ['slug' => '', 'name' => '', 'category' => '', 'description' => '', 'image_path' => '', 'specifications' => '', 'published' => 1];
if ($editing) {
    $statement = $db->prepare('SELECT * FROM equipment WHERE slug=?');
    $statement->execute([$slug]);
    $item = $statement->fetch();
    if (!$item) {
        http_response_code(404);
        exit('Peralatan tidak ditemukan.');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (['name', 'category', 'description', 'specifications'] as $field) {
        $item[$field] = trim(isset($_POST[$field]) ? (string) $_POST[$field] : '');
    }
    $item['published'] = isset($_POST['published']) ? 1 : 0;
    try {
        if ($item['name'] === '' || $item['category'] === '') {
            throw new RuntimeException('Nama peralatan dan kategori harus diisi.');
        }
        if (!$editing) {
            $item['slug'] = admin_slug(isset($_POST['slug']) && $_POST['slug'] !== '' ? $_POST['slug'] : $item['name']);
            if ($item['slug'] === '') {
                throw new RuntimeException('Slug peralatan tidak valid.');
            }
        }
        $requestedImagePath = isset($_POST['image_path']) ? $_POST['image_path'] : (isset($item['image_path']) ? $item['image_path'] : '');
        $item['image_path'] = validate_image_path(save_image_upload('image', $requestedImagePath));
        if ($editing) {
            $statement = $db->prepare('UPDATE equipment SET name=?, category=?, description=?, image_path=?, specifications=?, published=?, updated_at=CURRENT_TIMESTAMP WHERE slug=?');
            $statement->execute([$item['name'], $item['category'], $item['description'], $item['image_path'], $item['specifications'], $item['published'], $slug]);
        } else {
            $statement = $db->prepare('INSERT INTO equipment (slug, name, category, description, image_path, specifications, published) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $statement->execute([$item['slug'], $item['name'], $item['category'], $item['description'], $item['image_path'], $item['specifications'], $item['published']]);
        }
        header('Location: equipment.php?saved=1');
        exit;
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        $error = 'Slug sudah digunakan atau data tidak dapat disimpan.';
    }
}

admin_layout_header($editing ? 'Edit Peralatan' : 'Tambah Peralatan', 'equipment');
?>
<div class="mb-6"><a href="equipment.php" class="text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke peralatan</a><h1 class="mt-4 text-3xl font-extrabold text-slate-900"><?= $editing ? 'Edit peralatan' : 'Tambah peralatan' ?></h1></div>
<?php if ($error !== ''): ?><div class="mb-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-800"><?= h($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <?= csrf_field() ?>
    <div class="grid gap-5 sm:grid-cols-2">
        <?php if (!$editing): ?><label class="block text-sm font-semibold">Slug<input name="slug" value="<?= h($item['slug']) ?>" placeholder="contoh-alat" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label><?php endif; ?>
        <label class="block text-sm font-semibold">Nama peralatan<input name="name" value="<?= h($item['name']) ?>" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold">Kategori<input name="category" value="<?= h($item['category']) ?>" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold sm:col-span-2">Deskripsi<textarea name="description" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h($item['description']) ?></textarea></label>
        <label class="block text-sm font-semibold sm:col-span-2">Spesifikasi / petunjuk<textarea name="specifications" rows="5" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h($item['specifications']) ?></textarea><span class="mt-1 block text-xs font-normal text-slate-500">Satu butir per baris.</span></label>
        <label class="block text-sm font-semibold">Unggah gambar<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-3 text-sm"><span class="mt-1 block text-xs font-normal text-slate-500">JPG/PNG/WEBP/GIF, maksimal 5 MB.</span></label>
        <label class="block text-sm font-semibold">Path gambar<input name="image_path" value="<?= h($item['image_path']) ?>" placeholder="assets/neraca-analitik.svg" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><span class="mt-1 block text-xs font-normal text-slate-500">File lokal dari folder assets/ atau uploads/.</span></label>
        <label class="flex items-center gap-3 text-sm font-semibold"><input type="checkbox" name="published" value="1" <?= $item['published'] ? 'checked' : '' ?> class="h-4 w-4 accent-blue-800">Tampilkan di situs publik</label>
    </div>
    <button class="rounded-xl bg-undanaBlue px-6 py-3 font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-floppy-disk mr-2"></i>Simpan peralatan</button>
</form>
<?php admin_layout_footer(); ?>
