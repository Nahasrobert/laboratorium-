<?php
require_once __DIR__ . '/app/bootstrap.php';

$db = app_db();
$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? $_GET['slug'] : '';
$statement = $db->prepare('SELECT * FROM news WHERE slug = ? AND published = 1 LIMIT 1');
$statement->execute([$slug]);
$article = $statement->fetch();

if ($article) {
    $db->prepare('UPDATE news SET views = views + 1 WHERE id = ?')->execute([$article['id']]);
    $article['views']++;
    $pageTitle = $article['title'];
    $paragraphs = array_filter(array_map('trim', preg_split('/\R{2,}/', $article['body'])));
} else {
    http_response_code(404);
    $pageTitle = 'Berita Tidak Ditemukan';
}

require __DIR__ . '/header.php';
?>
<section class="bg-slate-100 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <?php if (!$article): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12"><i class="fa-regular fa-newspaper text-4xl text-slate-300"></i><h1 class="mt-5 text-2xl font-extrabold text-slate-900">Berita tidak ditemukan</h1><p class="mt-3 text-slate-600">Berita tidak tersedia atau belum diterbitkan.</p><a href="berita.php" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-undanaBlue px-5 py-3 text-sm font-bold text-white">Kembali ke berita</a></div>
        <?php else: ?>
            <a href="berita.php" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left"></i>Kembali ke Berita</a>
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <img src="<?= h($article['image_path']) ?>" alt="Ilustrasi: <?= h($article['title']) ?>" class="max-h-[480px] w-full object-cover">
                <div class="p-6 sm:p-10">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-slate-500"><span class="rounded-full bg-blue-50 px-3 py-1 text-undanaBlue"><?= h($article['category']) ?></span><time><?= h($article['published_at']) ?></time><span class="inline-flex items-center gap-1.5"><i class="fa-regular fa-eye"></i><?= number_format((int) $article['views']) ?> kali dilihat</span></div>
                    <h1 class="mt-5 text-2xl font-extrabold leading-tight text-slate-900 sm:text-4xl"><?= h($article['title']) ?></h1>
                    <p class="mt-5 border-l-4 border-undanaGold pl-4 text-lg leading-relaxed text-slate-600"><?= h($article['summary']) ?></p>
                    <div class="mt-7 space-y-5 text-base leading-relaxed text-slate-700">
                        <?php foreach ($paragraphs as $paragraph): ?><p><?= nl2br(h($paragraph)) ?></p><?php endforeach; ?>
                    </div>
                    <a href="berita.php" class="mt-8 inline-flex items-center gap-2 border-t border-slate-100 pt-5 text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left"></i>Lihat berita lainnya</a>
                </div>
            </article>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
