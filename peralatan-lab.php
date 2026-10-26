<?php
require_once __DIR__ . '/app/bootstrap.php';

$pageTitle = 'Peralatan Laboratorium';
$items = app_db()->query('SELECT slug, name, category, description, image_path FROM equipment WHERE published = 1 ORDER BY name')->fetchAll();
require __DIR__ . '/header.php';
?>
<section class="bg-slate-100 py-8 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-8 shadow-sm sm:px-10 sm:py-10">
            <div class="mx-auto mb-8 max-w-2xl text-center sm:mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-undanaBlue">Katalog Laboratorium</p>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Peralatan Lab</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600 sm:text-base">Pilih peralatan untuk melihat informasi dan panduan penggunaannya.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                <?php foreach ($items as $item): ?>
                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="single_lab.php?item=<?= rawurlencode($item['slug']) ?>" class="block overflow-hidden bg-slate-50">
                            <img src="<?= h($item['image_path']) ?>" alt="Ilustrasi <?= h($item['name']) ?>" class="aspect-[4/3] w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <span class="self-start rounded-full bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-undanaBlue"><?= h($item['category']) ?></span>
                            <h2 class="mt-3 text-lg font-bold leading-snug text-slate-900"><?= h($item['name']) ?></h2>
                            <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600"><?= h($item['description']) ?></p>
                            <a href="single_lab.php?item=<?= rawurlencode($item['slug']) ?>" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-undanaBlue px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-900">
                                Lihat selengkapnya <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if (!$items): ?><p class="text-sm text-slate-500">Belum ada peralatan yang diterbitkan.</p><?php endif; ?>
            </div>

            <div class="mt-8 rounded-xl border border-blue-100 bg-blue-50 p-4 sm:p-5">
                <p class="text-sm leading-relaxed text-slate-700">Untuk foto dan informasi ketersediaan unit, silakan konfirmasi kepada laboratorium.</p>
                <a href="peminjaman.php" class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-undanaBlue hover:underline">Informasi peminjaman <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/footer.php'; ?>
