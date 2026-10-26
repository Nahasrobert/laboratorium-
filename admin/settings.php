<?php
require_once __DIR__ . '/bootstrap.php';
require_admin();

$keys = [
    'brand_name' => ['Nama situs / laboratorium', 'text'],
    'institution' => ['Institusi', 'text'],
    'faculty' => ['Fakultas', 'text'],
    'study_program' => ['Program studi', 'text'],
    'address' => ['Alamat', 'textarea'],
    'email' => ['Email kontak', 'email'],
    'phone' => ['Nomor telepon', 'text'],
    'weekday_hours' => ['Jam operasional Senin–Kamis', 'text'],
    'friday_hours' => ['Jam operasional Jumat', 'text'],
    'weekend_hours' => ['Jam operasional akhir pekan', 'text'],
    'footer_description' => ['Deskripsi footer', 'textarea'],
];
$settings = app_settings();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $updated = [];
    foreach ($keys as $key => $field) {
        $updated[$key] = trim(isset($_POST[$key]) ? (string) $_POST[$key] : '');
    }
    if (in_array('', $updated, true)) {
        $error = 'Semua pengaturan wajib diisi.';
    } elseif (!filter_var($updated['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Masukkan alamat email yang valid.';
    } else {
        $statement = $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value');
        foreach ($updated as $key => $value) {
            $statement->execute([$key, $value]);
        }
        header('Location: settings.php?saved=1');
        exit;
    }
    $settings = $updated;
}

admin_layout_header('Pengaturan Situs', 'settings');
?>
<div class="mb-6"><h1 class="text-3xl font-extrabold text-slate-900">Pengaturan Situs</h1><p class="mt-2 text-sm text-slate-600">Informasi institusi dan kontak ini digunakan bersama di header, beranda, dan footer publik.</p></div>
<?php if ($error !== ''): ?><div class="mb-5 rounded-xl bg-rose-50 p-4 text-sm text-rose-800"><?= h($error) ?></div><?php endif; ?>
<form method="post" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
    <?= csrf_field() ?>
    <div class="grid gap-5 sm:grid-cols-2">
        <?php foreach ($keys as $key => $field): ?>
            <label class="block text-sm font-semibold text-slate-700 <?= $field[1] === 'textarea' ? 'sm:col-span-2' : '' ?>"><?= h($field[0]) ?>
                <?php if ($field[1] === 'textarea'): ?>
                    <textarea name="<?= h($key) ?>" rows="3" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"><?= h(isset($settings[$key]) ? $settings[$key] : '') ?></textarea>
                <?php else: ?>
                    <input name="<?= h($key) ?>" type="<?= h($field[1]) ?>" value="<?= h(isset($settings[$key]) ? $settings[$key] : '') ?>" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3">
                <?php endif; ?>
            </label>
        <?php endforeach; ?>
    </div>
    <button class="mt-6 rounded-xl bg-undanaBlue px-6 py-3 font-bold text-white hover:bg-blue-900"><i class="fa-solid fa-floppy-disk mr-2"></i>Simpan pengaturan</button>
</form>
<?php admin_layout_footer(); ?>
