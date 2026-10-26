<?php
require_once __DIR__ . '/app/bootstrap.php';

$db = app_db();
$itemId = isset($_GET['item']) && is_string($_GET['item']) ? $_GET['item'] : '';
$statement = $db->prepare('SELECT * FROM equipment WHERE slug = ? AND published = 1 LIMIT 1');
$statement->execute([$itemId]);
$item = $statement->fetch();

if ($item) {
    $db->prepare('UPDATE equipment SET views = views + 1 WHERE id = ?')->execute([$item['id']]);
    $item['views']++;
    $specifications = array_filter(array_map('trim', preg_split('/\R+/', $item['specifications'])));
    $pageTitle = $item['name'];
} else {
    http_response_code(404);
    $pageTitle = 'Peralatan Tidak Ditemukan';
}

require __DIR__ . '/header.php';
?>
<section class="bg-slate-100 py-8 sm:py-12">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <?php if (!$item): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">
                <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300"></i><h1 class="mt-5 text-2xl font-extrabold text-slate-900">Peralatan tidak ditemukan</h1><p class="mt-3 text-slate-600">Peralatan tidak tersedia atau belum diterbitkan.</p><a href="peralatan-lab.php" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-undanaBlue px-5 py-3 text-sm font-bold text-white">Kembali ke peralatan lab</a>
            </div>
        <?php else: ?>
            <a href="peralatan-lab.php" class="mb-5 inline-flex items-center gap-2 text-sm font-bold text-undanaBlue hover:underline"><i class="fa-solid fa-arrow-left"></i>Kembali ke Peralatan Lab</a>
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="grid lg:grid-cols-2">
                    <div class="flex items-center bg-slate-50 p-5 sm:p-8"><img src="<?= h($item['image_path']) ?>" alt="Ilustrasi <?= h($item['name']) ?>" class="max-h-[560px] w-full rounded-xl object-contain"></div>
                    <div class="p-6 sm:p-10">
                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-undanaBlue"><?= h($item['category']) ?></span>
                        <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl"><?= h($item['name']) ?></h1>
                        <p class="mt-5 leading-relaxed text-slate-600"><?= h($item['description']) ?></p>
                        <h2 class="mt-8 text-lg font-bold text-slate-900">Informasi penggunaan</h2>
                        <ul class="mt-4 space-y-3">
                            <?php foreach ($specifications as $specification): ?><li class="flex gap-3 text-sm leading-relaxed text-slate-600"><i class="fa-solid fa-check mt-1 text-emerald-600"></i><span><?= h($specification) ?></span></li><?php endforeach; ?>
                        </ul>
                        <div class="mt-8 flex flex-wrap items-center gap-4 border-t border-slate-100 pt-5">
                            <span class="inline-flex items-center gap-2 text-sm text-slate-500"><i class="fa-regular fa-eye"></i>Dilihat <?= number_format((int) $item['views']) ?> kali</span>
                            <a href="peminjaman.php" class="inline-flex items-center gap-2 rounded-xl bg-undanaBlue px-5 py-3 text-sm font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-clipboard-list"></i>Panduan peminjaman</a>
                        </div>
                        <p class="mt-4 text-xs text-slate-500">Konfirmasikan foto, kondisi, dan ketersediaan unit kepada petugas laboratorium.</p>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
