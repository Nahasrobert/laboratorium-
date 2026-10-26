<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$counts = [
    'pages' => (int) $db->query('SELECT COUNT(*) FROM pages')->fetchColumn(),
    'published_pages' => (int) $db->query('SELECT COUNT(*) FROM pages WHERE published = 1')->fetchColumn(),
    'equipment' => (int) $db->query('SELECT COUNT(*) FROM equipment')->fetchColumn(),
    'news' => (int) $db->query('SELECT COUNT(*) FROM news')->fetchColumn(),
    'published_news' => (int) $db->query('SELECT COUNT(*) FROM news WHERE published = 1')->fetchColumn(),
    'views' => (int) $db->query('SELECT (SELECT COALESCE(SUM(views), 0) FROM news) + (SELECT COALESCE(SUM(views), 0) FROM equipment)')->fetchColumn(),
];
$recentNews = $db->query('SELECT title, published, updated_at FROM news ORDER BY updated_at DESC LIMIT 5')->fetchAll();

admin_layout_header('Dashboard', 'dashboard');
?>
<div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><p class="text-sm font-semibold text-undanaBlue">Ringkasan situs</p><h1 class="mt-1 text-3xl font-extrabold text-slate-900">Dashboard Admin</h1><p class="mt-2 text-sm text-slate-600">Kelola konten publik Laboratorium Pendidikan Kimia.</p></div>
    <a href="../index.php" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat situs</a>
</div>
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ([
        ['Halaman situs', $counts['pages'], 'fa-file-lines', 'pages.php', 'Jumlah halaman yang dikelola'],
        ['Peralatan lab', $counts['equipment'], 'fa-flask', 'equipment.php', 'Item di katalog peralatan'],
        ['Berita', $counts['news'], 'fa-newspaper', 'news.php', 'Artikel dan pengumuman'],
        ['Total view', $counts['views'], 'fa-eye', 'news.php', 'View berita dan peralatan'],
    ] as $card): ?>
        <a href="<?= h($card[3]) ?>" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between"><span class="text-sm font-semibold text-slate-500"><?= h($card[0]) ?></span><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-undanaBlue"><i class="fa-solid <?= h($card[2]) ?>"></i></span></div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900"><?= number_format($card[1]) ?></p><p class="mt-1 text-xs text-slate-500"><?= h($card[4]) ?></p>
        </a>
    <?php endforeach; ?>
</section>
<section class="mt-7 grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between gap-3"><h2 class="text-lg font-bold text-slate-900">Berita terbaru diubah</h2><a href="news.php" class="text-sm font-bold text-undanaBlue hover:underline">Kelola berita</a></div>
        <div class="mt-4 divide-y divide-slate-100">
            <?php if (!$recentNews): ?><p class="py-5 text-sm text-slate-500">Belum ada berita.</p><?php endif; ?>
            <?php foreach ($recentNews as $news): ?>
                <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between"><span class="font-semibold text-slate-800"><?= h($news['title']) ?></span><span class="text-xs <?= $news['published'] ? 'text-emerald-700' : 'text-slate-500' ?>"><?= $news['published'] ? 'Terbit' : 'Draft' ?> · <?= h($news['updated_at']) ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="rounded-2xl bg-undanaDark p-6 text-white shadow-sm">
        <h2 class="text-lg font-bold">Aksi cepat</h2>
        <div class="mt-5 space-y-3">
            <a href="page-edit.php" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-semibold hover:bg-white/15"><i class="fa-solid fa-file-circle-plus text-undanaGold"></i>Tambah halaman</a>
            <a href="equipment-edit.php" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-semibold hover:bg-white/15"><i class="fa-solid fa-flask text-undanaGold"></i>Tambah peralatan</a>
                <a href="news-edit.php" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-semibold hover:bg-white/15"><i class="fa-solid fa-pen-to-square text-undanaGold"></i>Tulis berita</a>
                <a href="settings.php" class="flex items-center gap-3 rounded-xl bg-white/10 p-3 text-sm font-semibold hover:bg-white/15"><i class="fa-solid fa-gear text-undanaGold"></i>Pengaturan situs</a>
        </div>
    </div>
</section>
<?php admin_layout_footer(); ?>
