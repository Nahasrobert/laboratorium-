<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$equipment = $db->query('SELECT slug, name, category, published, views, updated_at FROM equipment ORDER BY name')->fetchAll();
admin_layout_header('Peralatan Lab', 'equipment');
?>
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><h1 class="text-3xl font-extrabold text-slate-900">Peralatan Lab</h1><p class="mt-2 text-sm text-slate-600">Kelola katalog peralatan yang tampil di situs publik.</p></div>
    <a href="equipment-edit.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-undanaBlue px-4 py-3 text-sm font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-plus"></i>Tambah peralatan</a>
</div>
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <table id="admin-table" class="display responsive nowrap w-full">
        <thead><tr><th>Nama</th><th>Kategori</th><th>Status</th><th>View</th><th>Diubah</th><th>Aksi</th></tr></thead>
        <tbody><?php foreach ($equipment as $item): ?><tr>
            <td><?= h($item['name']) ?></td><td><?= h($item['category']) ?></td><td><?= $item['published'] ? 'Terbit' : 'Draft' ?></td><td><?= (int) $item['views'] ?></td><td><?= h($item['updated_at']) ?></td>
            <td><div class="flex flex-wrap gap-2"><a class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-undanaBlue" href="equipment-edit.php?slug=<?= rawurlencode($item['slug']) ?>">Edit</a><a class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700" target="_blank" rel="noopener" href="../single_lab.php?item=<?= rawurlencode($item['slug']) ?>">Lihat</a><form method="post" action="delete.php" data-confirm><?= csrf_field() ?><input type="hidden" name="type" value="equipment"><input type="hidden" name="slug" value="<?= h($item['slug']) ?>"><button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form></div></td>
        </tr><?php endforeach; ?></tbody>
    </table>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>new DataTable('#admin-table',{responsive:true,pageLength:10,language:{search:'Cari:',lengthMenu:'Tampilkan _MENU_ baris',info:'Menampilkan _START_–_END_ dari _TOTAL_',infoEmpty:'Tidak ada data',zeroRecords:'Data tidak ditemukan',paginate:{first:'Awal',last:'Akhir',next:'Berikutnya',previous:'Sebelumnya'}}}));</script>
<?php admin_layout_footer(); ?>
