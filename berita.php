<?php
require_once __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Berita & Pengumuman';
$articles = app_db()->query('SELECT slug, title, category, published_at, summary, image_path FROM news WHERE published = 1 ORDER BY published_at DESC, id DESC')->fetchAll();
require __DIR__ . '/header.php';
?>
<section class="bg-gradient-to-br from-undanaDark via-undanaBlue to-slate-900 py-14 text-white sm:py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <p class="mb-4 text-xs font-bold uppercase tracking-widest text-undanaGold">Informasi Terkini</p>
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-5xl">Berita &amp; Pengumuman Laboratorium</h1>
            <p class="mt-5 max-w-xl leading-relaxed text-slate-300">Informasi kegiatan akademik, keselamatan kerja, penelitian, dan layanan Laboratorium Pendidikan Kimia.</p>
        </div>
        <img src="assets/berita-laboratorium.svg" alt="Ilustrasi mahasiswa mengikuti kegiatan praktikum kimia" class="w-full rounded-3xl border border-white/10 shadow-2xl">
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
    <?php if ($articles): ?>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($articles as $article): ?>
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                    <a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>" class="block overflow-hidden">
                        <img src="<?= h($article['image_path']) ?>" alt="Ilustrasi: <?= h($article['title']) ?>" class="h-48 w-full object-cover transition-transform hover:scale-105">
                    </a>
                    <div class="p-6">
                        <span class="rounded-md bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-undanaBlue"><?= h($article['category']) ?></span>
                        <p class="mt-4 text-xs font-semibold text-slate-400"><?= h($article['published_at']) ?></p>
                        <h2 class="mt-2 text-base font-bold leading-snug text-slate-900"><a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>" class="hover:text-undanaBlue"><?= h($article['title']) ?></a></h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600"><?= h($article['summary']) ?></p>
                        <a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-undanaBlue hover:underline">Baca selengkapnya <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">Belum ada berita yang diterbitkan.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/footer.php'; ?>
