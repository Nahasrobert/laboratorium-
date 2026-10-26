<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$pages = $db->query('SELECT slug, menu_label, nav_group, title, published, updated_at FROM pages ORDER BY sort_order, title')->fetchAll();
admin_layout_header('Halaman Situs', 'pages');
?>
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><h1 class="text-3xl font-extrabold text-slate-900">Halaman Situs</h1><p class="mt-2 text-sm text-slate-600">Edit teks, gambar, urutan menu, dan status publikasi halaman.</p></div>
    <a href="page-edit.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-undanaBlue px-4 py-3 text-sm font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-plus"></i>Tambah halaman</a>
</div>
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <table id="admin-table" class="display responsive nowrap w-full">
        <thead><tr><th>Nama menu</th><th>Judul</th><th>Kelompok</th><th>Status</th><th>Terakhir diubah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
                <tr>
                    <td><?= h($page['menu_label']) ?></td><td><?= h($page['title']) ?></td><td><?= h($page['nav_group']) ?></td>
                    <td><?= $page['published'] ? 'Terbit' : 'Draft' ?></td><td><?= h($page['updated_at']) ?></td>
                    <td><div class="flex flex-wrap gap-2">
                        <a class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-undanaBlue" href="page-edit.php?slug=<?= rawurlencode($page['slug']) ?>">Edit</a>
                        <a class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700" target="_blank" rel="noopener" href="../<?= h(public_page_path($page['slug'])) ?>">Lihat</a>
                        <form method="post" action="delete.php" data-confirm><?= csrf_field() ?><input type="hidden" name="type" value="pages"><input type="hidden" name="slug" value="<?= h($page['slug']) ?>"><button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form>
                    </div></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>new DataTable('#admin-table',{responsive:true,pageLength:10,language:{search:'Cari:',lengthMenu:'Tampilkan _MENU_ baris',info:'Menampilkan _START_–_END_ dari _TOTAL_',infoEmpty:'Tidak ada data',zeroRecords:'Data tidak ditemukan',paginate:{first:'Awal',last:'Akhir',next:'Berikutnya',previous:'Sebelumnya'}}}));</script>
<?php admin_layout_footer(); ?>
