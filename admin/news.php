<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$news = $db->query('SELECT slug, title, category, published_at, published, views, updated_at FROM news ORDER BY published_at DESC, id DESC')->fetchAll();
admin_layout_header('Berita', 'news');
?>
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><h1 class="text-3xl font-extrabold text-slate-900">Berita</h1><p class="mt-2 text-sm text-slate-600">Buat dan perbarui berita serta pengumuman pada situs publik.</p></div>
    <a href="news-edit.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-undanaBlue px-4 py-3 text-sm font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-plus"></i>Tulis berita</a>
</div>
<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <table id="admin-table" class="display responsive nowrap w-full">
        <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>View</th><th>Diubah</th><th>Aksi</th></tr></thead>
        <tbody><?php foreach ($news as $article): ?><tr>
            <td><?= h($article['title']) ?></td><td><?= h($article['category']) ?></td><td><?= h($article['published_at']) ?></td><td><?= $article['published'] ? 'Terbit' : 'Draft' ?></td><td><?= (int) $article['views'] ?></td><td><?= h($article['updated_at']) ?></td>
            <td><div class="flex flex-wrap gap-2"><a class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-undanaBlue" href="news-edit.php?slug=<?= rawurlencode($article['slug']) ?>">Edit</a><a class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700" target="_blank" rel="noopener" href="../berita_single.php?slug=<?= rawurlencode($article['slug']) ?>">Lihat</a><form method="post" action="delete.php" data-confirm><?= csrf_field() ?><input type="hidden" name="type" value="news"><input type="hidden" name="slug" value="<?= h($article['slug']) ?>"><button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form></div></td>
        </tr><?php endforeach; ?></tbody>
    </table>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>new DataTable('#admin-table',{responsive:true,pageLength:10,language:{search:'Cari:',lengthMenu:'Tampilkan _MENU_ baris',info:'Menampilkan _START_–_END_ dari _TOTAL_',infoEmpty:'Tidak ada data',zeroRecords:'Data tidak ditemukan',paginate:{first:'Awal',last:'Akhir',next:'Berikutnya',previous:'Sebelumnya'}}}));</script>
<?php admin_layout_footer(); ?>
