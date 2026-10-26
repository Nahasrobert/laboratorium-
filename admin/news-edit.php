<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$editing = $slug !== '';
$article = ['slug' => '', 'title' => '', 'category' => '', 'published_at' => date('Y-m-d'), 'summary' => '', 'body' => '', 'image_path' => '', 'published' => 1];
if ($editing) {
    $statement = $db->prepare('SELECT * FROM news WHERE slug=?');
    $statement->execute([$slug]);
    $article = $statement->fetch();
    if (!$article) {
        http_response_code(404);
        exit('Berita tidak ditemukan.');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (['title', 'category', 'published_at', 'summary', 'body'] as $field) {
        $article[$field] = trim(isset($_POST[$field]) ? (string) $_POST[$field] : '');
    }
    $article['published'] = isset($_POST['published']) ? 1 : 0;
    try {
        if ($article['title'] === '' || $article['category'] === '' || $article['summary'] === '' || $article['body'] === '') {
            throw new RuntimeException('Judul, kategori, ringkasan, dan isi berita wajib diisi.');
        }
        if (!$editing) {
            $article['slug'] = admin_slug(isset($_POST['slug']) && $_POST['slug'] !== '' ? $_POST['slug'] : $article['title']);
            if ($article['slug'] === '') {
                throw new RuntimeException('Slug berita tidak valid.');
            }
        }
        $requestedImagePath = isset($_POST['image_path']) ? $_POST['image_path'] : (isset($article['image_path']) ? $article['image_path'] : '');
        $article['image_path'] = validate_image_path(save_image_upload('image', $requestedImagePath));
        if ($editing) {
            $statement = $db->prepare('UPDATE news SET title=?, category=?, published_at=?, summary=?, body=?, image_path=?, published=?, updated_at=CURRENT_TIMESTAMP WHERE slug=?');
            $statement->execute([$article['title'], $article['category'], $article['published_at'], $article['summary'], $article['body'], $article['image_path'], $article['published'], $slug]);
        } else {
            $statement = $db->prepare('INSERT INTO news (slug, title, category, published_at, summary, body, image_path, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $statement->execute([$article['slug'], $article['title'], $article['category'], $article['published_at'], $article['summary'], $article['body'], $article['image_path'], $article['published']]);
        }
        header('Location: news.php?saved=1');
        exit;
    } catch (RuntimeException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        $error = 'Slug sudah digunakan atau data tidak dapat disimpan.';
    }
}

admin_layout_header($editing ? 'Edit Berita' : 'Tulis Berita', 'news');
?>
<div class="mb-6"><a href="news.php" class="text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke berita</a><h1 class="mt-4 text-3xl font-extrabold text-slate-900"><?= $editing ? 'Edit berita' : 'Tulis berita' ?></h1></div>
<?php if ($error !== ''): ?><div class="mb-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-800"><?= h($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <?= csrf_field() ?>
    <div class="grid gap-5 sm:grid-cols-2">
        <?php if (!$editing): ?><label class="block text-sm font-semibold">Slug<input name="slug" value="<?= h($article['slug']) ?>" placeholder="judul-berita" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label><?php endif; ?>
        <label class="block text-sm font-semibold sm:col-span-2">Judul berita<input name="title" value="<?= h($article['title']) ?>" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold">Kategori<input name="category" value="<?= h($article['category']) ?>" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold">Tanggal publikasi<input name="published_at" value="<?= h($article['published_at']) ?>" placeholder="27 Oktober 2026" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"></label>
        <label class="block text-sm font-semibold sm:col-span-2">Ringkasan<textarea name="summary" rows="3" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h($article['summary']) ?></textarea></label>
        <label class="block text-sm font-semibold sm:col-span-2">Isi berita<textarea name="body" rows="9" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h($article['body']) ?></textarea><span class="mt-1 block text-xs font-normal text-slate-500">Pisahkan paragraf dengan satu baris kosong. HTML tidak dijalankan.</span></label>
        <label class="block text-sm font-semibold">Unggah gambar<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-3 text-sm"><span class="mt-1 block text-xs font-normal text-slate-500">JPG/PNG/WEBP/GIF, maksimal 5 MB.</span></label>
        <label class="block text-sm font-semibold">Path gambar<input name="image_path" value="<?= h($article['image_path']) ?>" placeholder="assets/berita-laboratorium.svg" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><span class="mt-1 block text-xs font-normal text-slate-500">File lokal dari folder assets/ atau uploads/.</span></label>
        <label class="flex items-center gap-3 text-sm font-semibold"><input type="checkbox" name="published" value="1" <?= $article['published'] ? 'checked' : '' ?> class="h-4 w-4 accent-blue-800">Terbitkan berita</label>
    </div>
    <button class="rounded-xl bg-undanaBlue px-6 py-3 font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-floppy-disk mr-2"></i>Simpan berita</button>
</form>
<?php admin_layout_footer(); ?>
