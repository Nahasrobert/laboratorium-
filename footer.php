<?php
$footerPages = app_db()->query('SELECT slug, menu_label, nav_group FROM pages WHERE published = 1 AND nav_group IN ("profil", "layanan") ORDER BY sort_order, menu_label')->fetchAll();
$footerProfilePages = array_values(array_filter($footerPages, function ($page) { return $page['nav_group'] === 'profil'; }));
$footerServicePages = array_values(array_filter($footerPages, function ($page) { return $page['nav_group'] === 'layanan'; }));
?>
    </main>
    <footer class="bg-undanaDark text-slate-300 pt-14 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 shrink-0 flex items-center justify-center bg-white rounded-xl border border-blue-700/50 p-1">
                        <img src="Logo_Undana.png" alt="Logo Universitas Nusa Cendana" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-sm"><?= h(app_setting('brand_name', 'Laboratorium Pendidikan Kimia')) ?></h2>
                        <p class="text-xs text-slate-400">FKIP <?= h(app_setting('institution', 'Universitas Nusa Cendana Kupang')) ?></p>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed"><?= h(app_setting('footer_description')) ?></p>
            </div>
            <div>
                <h2 class="text-white font-bold text-sm mb-4">Profil Laboratorium</h2>
                <ul class="space-y-2 text-xs">
                    <?php foreach ($footerProfilePages as $page): ?><li><a href="<?= h(public_page_path($page['slug'])) ?>" class="hover:text-undanaGold"><?= h($page['menu_label']) ?></a></li><?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h2 class="text-white font-bold text-sm mb-4">Layanan Akademik</h2>
                <ul class="space-y-2 text-xs">
                    <?php foreach ($footerServicePages as $page): ?><li><a href="<?= h(public_page_path($page['slug'])) ?>" class="hover:text-undanaGold"><?= h($page['menu_label']) ?></a></li><?php endforeach; ?>
                    <li><a href="fasilitas.php" class="hover:text-undanaGold"><?= h((app_db()->query('SELECT menu_label FROM pages WHERE slug = "fasilitas" AND published = 1')->fetchColumn()) ?: 'Fasilitas') ?></a></li>
                    <li><a href="peralatan-lab.php" class="hover:text-undanaGold">Peralatan Lab</a></li>
                    <li><a href="berita.php" class="hover:text-undanaGold">Berita</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-white font-bold text-sm mb-4">Kontak &amp; Lokasi</h2>
                <ul class="space-y-3 text-xs text-slate-400">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-location-dot text-undanaGold mt-0.5"></i><span><?= h(app_setting('address')) ?></span></li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-envelope text-undanaGold mt-0.5"></i><span class="break-all"><?= h(app_setting('email')) ?></span></li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-undanaGold"></i><span><?= h(app_setting('phone')) ?></span></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <p>&copy; <?= date('Y') ?> <?= h(app_setting('brand_name', 'Laboratorium Pendidikan Kimia')) ?> - FKIP <?= h(app_setting('institution', 'Universitas Nusa Cendana Kupang')) ?>.</p>
            <a href="index.php" class="hover:text-undanaGold">Beranda</a>
        </div>
    </footer>
    <script>
        const menuButton = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuButton.addEventListener('click', () => {
            const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!isExpanded));
            menuButton.setAttribute('aria-label', isExpanded ? 'Buka menu navigasi' : 'Tutup menu navigasi');
            mobileMenu.classList.toggle('hidden', isExpanded);
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuButton.setAttribute('aria-expanded', 'false');
                menuButton.setAttribute('aria-label', 'Buka menu navigasi');
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuButton.setAttribute('aria-expanded', 'false');
                menuButton.setAttribute('aria-label', 'Buka menu navigasi');
                menuButton.focus();
            }
        });

        const pageLoader = document.getElementById('page-loader');
        const hidePageLoader = () => {
            const minimumLoaderDuration = 500;
            const elapsed = Date.now() - window.pageLoaderStartedAt;
            window.setTimeout(() => {
                pageLoader.classList.add('is-hidden');
                pageLoader.setAttribute('aria-hidden', 'true');
            }, Math.max(0, minimumLoaderDuration - elapsed));
        };

        if (document.readyState === 'complete') {
            hidePageLoader();
        } else {
            window.addEventListener('load', hidePageLoader, { once: true });
        }
    </script>
</body>
</html>
