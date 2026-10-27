<?php
require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = isset($pageTitle) ? $pageTitle : 'Laboratorium Pendidikan Kimia';
$currentPage = basename($_SERVER['PHP_SELF']);
$siteSettings = app_settings();
$publicPages = app_db()->query('SELECT slug, menu_label, nav_group FROM pages WHERE published = 1 AND slug != "beranda" ORDER BY sort_order, menu_label')->fetchAll();
$profilePages = array_values(array_filter($publicPages, function ($page) { return $page['nav_group'] === 'profil'; }));
$servicePages = array_values(array_filter($publicPages, function ($page) { return $page['nav_group'] === 'layanan'; }));
$otherPages = array_values(array_filter($publicPages, function ($page) { return $page['nav_group'] === 'halaman' && $page['slug'] !== 'fasilitas'; }));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> - FKIP Universitas Nusa Cendana</title>
    <link rel="icon" type="image/png" href="Logo_Undana.png">
    <script>window.pageLoaderStartedAt = Date.now();</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        undanaBlue: '#003366',
                        undanaGold: '#FFB800',
                        undanaDark: '#0A192F',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            opacity: 1;
            visibility: visible;
            transition: opacity 300ms ease, visibility 300ms ease;
        }
        .page-loader.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .page-loader__ring {
            position: absolute;
            inset: 0;
            border: 3px solid transparent;
            border-top-color: #003366;
            border-right-color: #ffb800;
            border-radius: 50%;
            animation: page-loader-spin 1.3s linear infinite;
        }
        .page-loader__logo {
            animation: page-loader-pulse 1.4s ease-in-out infinite;
        }
        @keyframes page-loader-spin {
            to { transform: rotate(360deg); }
        }
        @keyframes page-loader-pulse {
            50% { transform: scale(.92); opacity: .8; }
        }
        @media (prefers-reduced-motion: reduce) {
            .page-loader, .page-loader__ring, .page-loader__logo { animation: none; transition: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-undanaGold selection:text-undanaDark">
    <div id="page-loader" class="page-loader" role="status" aria-live="polite" aria-label="Memuat halaman">
        <div class="relative flex h-24 w-24 items-center justify-center">
            <div class="page-loader__ring"></div>
            <img src="Logo_Undana.png" alt="" class="page-loader__logo h-14 w-14 object-contain">
        </div>
        <span class="sr-only">Memuat halaman...</span>
    </div>
    <noscript><style>.page-loader { display: none; }</style></noscript>
    <div class="bg-undanaDark text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-4 flex-wrap">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-undanaGold"></i> <?= h($siteSettings['address']) ?></span>
                <span class="flex min-w-0 items-center gap-1.5"><i class="fa-solid fa-envelope text-undanaGold"></i><span class="break-all"><?= h($siteSettings['email']) ?></span></span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-phone text-undanaGold"></i> <?= h($siteSettings['phone']) ?></span>
            </div>
            <span class="flex items-center gap-1 bg-slate-800/80 px-2 py-0.5 rounded text-undanaGold font-semibold border border-slate-700">
                <i class="fa-solid fa-globe text-[10px]"></i> ID
            </span>
        </div>
    </div>

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-20 py-3 flex items-center justify-between gap-3">
            <a href="index.php" class="flex min-w-0 items-center gap-3.5 group">
                <div class="w-12 h-12 shrink-0 flex items-center justify-center bg-white rounded-xl border border-blue-100 group-hover:scale-105 transition shadow-sm p-1">
                    <img src="Logo_Undana.png" alt="Logo Universitas Nusa Cendana" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <span class="block text-[11px] sm:text-sm font-extrabold text-undanaBlue tracking-tight uppercase leading-tight"><?= h($siteSettings['brand_name']) ?></span>
                    <span class="hidden sm:block text-[10px] sm:text-xs text-slate-500 font-semibold">FKIP • <?= h($siteSettings['institution']) ?></span>
                </div>
            </a>

            <nav aria-label="Navigasi utama" class="hidden lg:flex items-center gap-1">
                <a href="index.php" class="px-3 py-2 text-sm font-semibold rounded-lg <?= $currentPage === 'index.php' ? 'text-undanaBlue bg-blue-50/80' : 'text-slate-700 hover:text-undanaBlue hover:bg-slate-50' ?>">Beranda</a>
                <div class="relative group">
                    <button type="button" class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-slate-700 hover:text-undanaBlue hover:bg-slate-50 rounded-lg">
                        Profil <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform"></i>
                    </button>
                    <div class="absolute top-full left-0 w-56 bg-white shadow-xl rounded-xl p-2 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <?php foreach ($profilePages as $page): ?>
                            <a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-undanaBlue rounded-lg"><?= h($page['menu_label']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="relative group">
                    <button type="button" class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-slate-700 hover:text-undanaBlue hover:bg-slate-50 rounded-lg">
                        Layanan &amp; Praktikum <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform"></i>
                    </button>
                    <div class="absolute top-full left-0 w-60 bg-white shadow-xl rounded-xl p-2 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <?php foreach ($servicePages as $page): ?>
                            <a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-undanaBlue rounded-lg"><?= h($page['menu_label']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $facilityPage = array_values(array_filter($publicPages, function ($page) { return $page['slug'] === 'fasilitas'; })); ?>
                <?php if ($facilityPage): ?><a href="<?= h(public_page_path($facilityPage[0]['slug'])) ?>" class="px-3 py-2 text-sm font-semibold rounded-lg <?= $currentPage === 'fasilitas.php' ? 'text-undanaBlue bg-blue-50/80' : 'text-slate-700 hover:text-undanaBlue hover:bg-slate-50' ?>"><?= h($facilityPage[0]['menu_label']) ?></a><?php endif; ?>
                <a href="peralatan-lab.php" class="px-3 py-2 text-sm font-semibold rounded-lg <?= $currentPage === 'peralatan-lab.php' ? 'text-undanaBlue bg-blue-50/80' : 'text-slate-700 hover:text-undanaBlue hover:bg-slate-50' ?>">Peralatan Lab</a>
                <a href="berita.php" class="px-3 py-2 text-sm font-semibold rounded-lg <?= $currentPage === 'berita.php' ? 'text-undanaBlue bg-blue-50/80' : 'text-slate-700 hover:text-undanaBlue hover:bg-slate-50' ?>">Berita</a>
                <?php if ($otherPages): ?>
                    <div class="relative group">
                        <button type="button" class="flex items-center gap-1 px-3 py-2 text-sm font-semibold text-slate-700 hover:text-undanaBlue hover:bg-slate-50 rounded-lg">Lainnya <i class="fa-solid fa-chevron-down text-[10px]"></i></button>
                        <div class="absolute top-full right-0 w-52 bg-white shadow-xl rounded-xl p-2 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <?php foreach ($otherPages as $page): ?><a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-undanaBlue rounded-lg"><?= h($page['menu_label']) ?></a><?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>

            <button id="mobile-menu-btn" type="button" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu navigasi" class="lg:hidden shrink-0 text-slate-700 p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-undanaBlue rounded-lg">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>

        <nav id="mobile-menu" aria-label="Navigasi seluler" class="hidden lg:hidden bg-white border-b border-slate-100 px-4 pt-2 pb-6 space-y-2 shadow-lg">
            <a href="index.php" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Beranda</a>
            <p class="px-3 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">Profil</p>
            <?php foreach ($profilePages as $page): ?><a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg"><?= h($page['menu_label']) ?></a><?php endforeach; ?>
            <p class="px-3 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">Layanan &amp; Praktikum</p>
            <?php foreach ($servicePages as $page): ?><a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg"><?= h($page['menu_label']) ?></a><?php endforeach; ?>
            <?php if ($facilityPage): ?><a href="<?= h(public_page_path($facilityPage[0]['slug'])) ?>" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg"><?= h($facilityPage[0]['menu_label']) ?></a><?php endif; ?>
            <a href="peralatan-lab.php" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Peralatan Lab</a>
            <a href="berita.php" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Berita</a>
            <?php foreach ($otherPages as $page): ?><a href="<?= h(public_page_path($page['slug'])) ?>" class="block px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg"><?= h($page['menu_label']) ?></a><?php endforeach; ?>
        </nav>
    </header>
    <main>
