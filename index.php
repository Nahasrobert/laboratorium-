<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = app_db();

$statement = $db->prepare('SELECT * FROM pages WHERE slug = ? AND published = 1 LIMIT 1');
$statement->execute(['beranda']);
$home = $statement->fetch();
if (!$home) {
    http_response_code(404);
    exit('Konten beranda tidak tersedia.');
}
$facts = [
    app_setting('institution'),
    app_setting('faculty'),
    app_setting('study_program'),
];
$equipmentCount = (int) $db->query('SELECT COUNT(*) FROM equipment WHERE published = 1')->fetchColumn();
$pageCount = (int) $db->query('SELECT COUNT(*) FROM pages WHERE published = 1')->fetchColumn();
$newsCount = (int) $db->query('SELECT COUNT(*) FROM news WHERE published = 1')->fetchColumn();
$latestNews = $db->query('SELECT slug, title, category, published_at, summary, image_path FROM news WHERE published = 1 ORDER BY published_at DESC, id DESC LIMIT 3')->fetchAll();
$facilities = $db->query('SELECT slug, menu_label, title, intro FROM pages WHERE slug = "fasilitas" AND published = 1')->fetch();
$pageTitle = $home['title'];
require __DIR__ . '/header.php';
?>
<section class="relative overflow-hidden bg-gradient-to-br from-undanaDark via-undanaBlue to-slate-900 py-16 text-white sm:py-24 lg:py-28">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#FFB800_1px,transparent_1px)] [background-size:24px_24px]"></div>
    <div class="absolute right-0 top-1/2 h-96 w-96 -translate-y-1/2 rounded-full bg-undanaGold/10 blur-3xl"></div>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl">
            <?php if ($home['eyebrow'] !== ''): ?><p class="mb-5 inline-flex rounded-full border border-undanaGold/30 bg-undanaGold/10 px-4 py-2 text-xs font-bold uppercase tracking-wider text-undanaGold"><?= h($home['eyebrow']) ?></p><?php endif; ?>
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl"><?= h($home['title']) ?></h1>
            <p class="mt-6 max-w-3xl text-base leading-relaxed text-slate-300 sm:text-lg"><?= h($home['body']) ?></p>
            <?php if ($home['intro'] !== ''): ?><p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-300"><?= h($home['intro']) ?></p><?php endif; ?>
            <div class="mt-8 grid gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur sm:grid-cols-3 sm:gap-0 sm:p-5">
                <?php foreach ($facts as $index => $fact): ?>
                    <div class="sm:px-4 <?= $index < count($facts) - 1 ? 'sm:border-r sm:border-white/15' : '' ?>">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-undanaGold"><?= ['Institusi', 'Fakultas', 'Program Studi'][$index] ?? 'Informasi' ?></p>
                        <p class="mt-1 text-sm font-semibold text-white"><?= h($fact) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="peralatan-lab.php" class="inline-flex items-center gap-2 rounded-xl bg-undanaGold px-6 py-3.5 font-bold text-undanaDark hover:bg-amber-400"><i class="fa-solid fa-flask"></i>Lihat peralatan lab</a>
                <a href="peminjaman.php" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-6 py-3.5 font-bold text-white hover:bg-white/20"><i class="fa-solid fa-calendar-check"></i>Jadwal &amp; peminjaman</a>
            </div>
        </div>
    </div>
</section>

<section class="relative z-20 mx-4 -mt-7 grid grid-cols-2 gap-3 rounded-2xl border border-slate-100 bg-white p-5 text-center shadow-lg sm:mx-auto sm:max-w-5xl sm:grid-cols-4 sm:gap-5 sm:p-7">
    <?php foreach ([[$pageCount, 'Halaman informasi'], [$equipmentCount, 'Peralatan terdaftar'], [$newsCount, 'Berita terbit'], ['100%', 'Fokus keselamatan']] as $stat): ?>
        <div><p class="text-3xl font-extrabold text-undanaBlue"><?= h($stat[0]) ?></p><p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500"><?= h($stat[1]) ?></p></div>
    <?php endforeach; ?>
</section>

<section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-undanaBlue">Profil Laboratorium</p>
        <h2 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">Pembelajaran kimia melalui eksperimen</h2>
        <p class="mt-4 leading-relaxed text-slate-600">Laboratorium Pendidikan Kimia FKIP Universitas Nusa Cendana mendukung praktikum, penelitian, dan layanan kimia dengan memperhatikan prosedur kerja serta keselamatan pengguna.</p>
        <a href="tentang.php" class="mt-5 inline-flex items-center gap-2 font-bold text-undanaBlue hover:underline">Tentang laboratorium<i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="rounded-3xl bg-slate-900 p-6 text-white sm:p-8">
        <p class="text-xs font-bold uppercase tracking-widest text-undanaGold">Jam operasional</p>
        <h2 class="mt-2 text-xl font-bold">Kunjungan dan kegiatan laboratorium</h2>
        <div class="mt-6 space-y-3 text-sm text-slate-300">
            <div class="flex justify-between gap-4 border-b border-slate-700 pb-3"><span>Senin – Kamis</span><span class="font-semibold text-white"><?= h(app_setting('weekday_hours')) ?></span></div>
            <div class="flex justify-between gap-4 border-b border-slate-700 pb-3"><span>Jumat</span><span class="font-semibold text-white"><?= h(app_setting('friday_hours')) ?></span></div>
            <div class="flex justify-between gap-4"><span>Sabtu &amp; Minggu</span><span class="font-semibold text-undanaGold"><?= h(app_setting('weekend_hours')) ?></span></div>
        </div>
    </div>
</section>

<?php if ($facilities): ?>
<section class="bg-slate-100 py-14 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-widest text-undanaBlue">Fasilitas</p><h2 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl"><?= h($facilities['title']) ?></h2><p class="mt-2 max-w-2xl text-slate-600"><?= h($facilities['intro']) ?></p></div>
            <a href="fasilitas.php" class="font-bold text-undanaBlue hover:underline">Lihat fasilitas<i class="fa-solid fa-arrow-right ml-2"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-widest text-undanaBlue">Informasi Terkini</p><h2 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">Berita &amp; Pengumuman</h2></div><a href="berita.php" class="shrink-0 text-sm font-bold text-undanaBlue hover:underline">Semua berita</a></div>
    <div class="grid gap-5 md:grid-cols-3">
        <?php foreach ($latestNews as $article): ?>
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>"><img src="<?= h($article['image_path']) ?>" alt="Ilustrasi <?= h($article['title']) ?>" class="h-44 w-full object-cover"></a>
                <div class="p-5"><p class="text-xs font-bold text-undanaBlue"><?= h($article['category']) ?> · <?= h($article['published_at']) ?></p><h3 class="mt-3 font-bold leading-snug text-slate-900"><a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>" class="hover:text-undanaBlue"><?= h($article['title']) ?></a></h3><p class="mt-2 text-sm leading-relaxed text-slate-600"><?= h($article['summary']) ?></p><a href="berita_single.php?slug=<?= rawurlencode($article['slug']) ?>" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-undanaBlue">Baca selengkapnya<i class="fa-solid fa-arrow-right"></i></a></div>
            </article>
        <?php endforeach; ?>
        <?php if (!$latestNews): ?><p class="text-sm text-slate-500">Belum ada berita yang diterbitkan.</p><?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
