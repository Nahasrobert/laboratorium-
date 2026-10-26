<?php
require_once __DIR__ . '/app/bootstrap.php';

$slug = isset($pageSlug) ? $pageSlug : '';
$statement = app_db()->prepare('SELECT * FROM pages WHERE slug = ? AND published = 1 LIMIT 1');
$statement->execute([$slug]);
$contentPage = $statement->fetch();

if (!$contentPage) {
    http_response_code(404);
    $pageTitle = 'Halaman Tidak Ditemukan';
    require __DIR__ . '/header.php';
    ?>
    <section class="mx-auto max-w-4xl px-4 py-20 text-center">
        <h1 class="text-3xl font-extrabold text-slate-900">Halaman tidak ditemukan</h1>
        <p class="mt-3 text-slate-600">Halaman ini tidak tersedia atau belum diterbitkan.</p>
        <a href="index.php" class="mt-6 inline-flex rounded-xl bg-undanaBlue px-5 py-3 font-bold text-white">Kembali ke beranda</a>
    </section>
    <?php
    require __DIR__ . '/footer.php';
    return;
}

$pageTitle = $contentPage['title'];
$bodyBlocks = preg_split('/\R{2,}/', str_replace('|', "\n\n", $contentPage['body']));
$bodyBlocks = array_values(array_filter(array_map('trim', $bodyBlocks), function ($value) { return $value !== ''; }));
require __DIR__ . '/header.php';
?>
<section class="bg-gradient-to-br from-undanaDark via-undanaBlue to-slate-900 text-white py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 <?= $contentPage['image_path'] !== '' ? 'grid items-center gap-10 lg:grid-cols-2' : '' ?>">
        <div>
            <?php if ($contentPage['eyebrow'] !== ''): ?><p class="mb-4 text-xs font-bold uppercase tracking-widest text-undanaGold"><?= h($contentPage['eyebrow']) ?></p><?php endif; ?>
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-5xl"><?= h($contentPage['title']) ?></h1>
            <?php if ($contentPage['intro'] !== ''): ?><p class="mt-5 max-w-3xl leading-relaxed text-slate-300"><?= h($contentPage['intro']) ?></p><?php endif; ?>
        </div>
        <?php if ($contentPage['image_path'] !== ''): ?>
            <img src="<?= h($contentPage['image_path']) ?>" alt="<?= h($contentPage['title']) ?>" class="w-full rounded-3xl border border-white/10 shadow-2xl">
        <?php endif; ?>
    </div>
</section>
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
    <?php if ($bodyBlocks): ?>
        <div class="grid gap-5 md:grid-cols-2">
            <?php foreach ($bodyBlocks as $block): ?>
                <?php
                $separator = strpos($block, ':');
                $heading = $separator !== false ? trim(substr($block, 0, $separator)) : '';
                $text = $separator !== false ? trim(substr($block, $separator + 1)) : $block;
                ?>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
                    <?php if ($heading !== ''): ?><h2 class="font-bold text-slate-900"><?= h($heading) ?></h2><?php endif; ?>
                    <?php if ($text !== ''): ?><p class="<?= $heading !== '' ? 'mt-2 ' : '' ?>leading-relaxed text-slate-600"><?= h($text) ?></p><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600 shadow-sm">Konten halaman belum ditambahkan.</div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/footer.php'; ?>
