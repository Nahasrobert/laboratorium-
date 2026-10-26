<?php

function app_db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0750, true) && !is_dir($dataDirectory)) {
        throw new RuntimeException('Direktori database tidak dapat dibuat.');
    }

    $pdo = new PDO('sqlite:' . $dataDirectory . DIRECTORY_SEPARATOR . 'laboratorium.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS site_settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT NOT NULL DEFAULT ""
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL UNIQUE,
            menu_label TEXT NOT NULL,
            nav_group TEXT NOT NULL DEFAULT "halaman",
            title TEXT NOT NULL,
            eyebrow TEXT NOT NULL DEFAULT "",
            intro TEXT NOT NULL DEFAULT "",
            body TEXT NOT NULL DEFAULT "",
            image_path TEXT NOT NULL DEFAULT "",
            published INTEGER NOT NULL DEFAULT 1,
            sort_order INTEGER NOT NULL DEFAULT 0,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );
    $pageColumns = $pdo->query('PRAGMA table_info(pages)')->fetchAll();
    if (!in_array('nav_group', array_column($pageColumns, 'name'), true)) {
        $pdo->exec('ALTER TABLE pages ADD COLUMN nav_group TEXT NOT NULL DEFAULT "halaman"');
    }
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS equipment (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            category TEXT NOT NULL DEFAULT "",
            description TEXT NOT NULL DEFAULT "",
            image_path TEXT NOT NULL DEFAULT "",
            specifications TEXT NOT NULL DEFAULT "",
            published INTEGER NOT NULL DEFAULT 1,
            views INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS news (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL UNIQUE,
            title TEXT NOT NULL,
            category TEXT NOT NULL DEFAULT "",
            published_at TEXT NOT NULL DEFAULT "",
            summary TEXT NOT NULL DEFAULT "",
            body TEXT NOT NULL DEFAULT "",
            image_path TEXT NOT NULL DEFAULT "",
            published INTEGER NOT NULL DEFAULT 1,
            views INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    seed_content($pdo);
    seed_settings($pdo);
    return $pdo;
}

function seed_settings(PDO $pdo)
{
    $defaults = [
        'brand_name' => 'Laboratorium Pendidikan Kimia',
        'institution' => 'Universitas Nusa Cendana Kupang',
        'faculty' => 'Fakultas Keguruan dan Ilmu Pendidikan',
        'study_program' => 'Pendidikan Kimia',
        'address' => 'Jl. Adisucipto, Kampus Penfui, Kota Kupang, Nusa Tenggara Timur',
        'email' => 'lab.pendidikankimia@staf.undana.ac.id',
        'phone' => '(0380) 881558',
        'weekday_hours' => '08.00–16.00 WITA',
        'friday_hours' => '08.00–16.30 WITA',
        'weekend_hours' => 'Tutup (Kecuali Kegiatan Khusus)',
        'footer_description' => 'Pusat layanan pendidikan, praktikum, dan pengujian kimia terpadu di Provinsi Nusa Tenggara Timur.',
    ];
    $statement = $pdo->prepare('INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($defaults as $key => $value) {
        $statement->execute([$key, $value]);
    }
}

function seed_content(PDO $pdo)
{
    $pageCount = (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
    $pages = [
        ['beranda', 'Beranda', 'halaman', 'Laboratorium Pendidikan Kimia FKIP Undana', 'Pusat Unggulan Riset & Pembelajaran Kimia NTT', 'Universitas Nusa Cendana Kupang|Fakultas Keguruan dan Ilmu Pendidikan|Pendidikan Kimia', 'Menyelenggarakan layanan praktikum berkualitas tinggi, penelitian sains inovatif, serta pengujian kimia terpadu untuk mendukung pengembangan pendidikan dan industri di Nusa Tenggara Timur.', '', 1, 0],
        ['tentang', 'Tentang Laboratorium', 'profil', 'Tentang Laboratorium Pendidikan Kimia', 'Profil Laboratorium', 'Laboratorium Pendidikan Kimia FKIP Universitas Nusa Cendana mendukung kegiatan pendidikan, praktikum, penelitian, dan layanan pengujian kimia di Nusa Tenggara Timur.', 'Institusi: Universitas Nusa Cendana Kupang|Fakultas: Fakultas Keguruan dan Ilmu Pendidikan|Program Studi: Pendidikan Kimia|Peran Laboratorium: Laboratorium menjadi ruang pembelajaran berbasis eksperimen bagi mahasiswa Pendidikan Kimia. Kegiatan di dalamnya mendukung penguatan keterampilan proses sains, penggunaan instrumen, penerapan keselamatan kerja, serta pelaksanaan penelitian sesuai kebutuhan akademik.|Pendidikan & Praktikum: Mendukung pembelajaran kimia melalui kegiatan praktikum terarah.|Penelitian & Layanan: Mendukung kegiatan penelitian dan layanan pengujian sesuai kemampuan fasilitas.', '', 1, 1],
        ['visi-misi', 'Visi & Misi', 'profil', 'Visi & Misi', 'Profil Laboratorium', 'Arah pengembangan Laboratorium Pendidikan Kimia dalam mendukung pendidikan, penelitian, dan layanan kimia.', 'VISI: Menjadi laboratorium pendidikan kimia yang mendukung pembelajaran dan penelitian yang bermutu, aman, inovatif, serta relevan dengan kebutuhan masyarakat dan perkembangan ilmu pengetahuan.|MISI: Menyelenggarakan kegiatan praktikum yang mendukung kompetensi mahasiswa Pendidikan Kimia.|MISI: Mendukung penelitian di bidang pendidikan dan ilmu kimia dengan pemanfaatan fasilitas secara bertanggung jawab.|MISI: Menerapkan budaya keselamatan, ketertiban, dan kepedulian terhadap lingkungan di laboratorium.|MISI: Mengembangkan layanan laboratorium yang profesional sesuai ketersediaan sumber daya dan kompetensi.', '', 1, 2],
        ['struktur-organisasi', 'Struktur Organisasi', 'profil', 'Struktur Organisasi', 'Profil Laboratorium', 'Pembagian peran untuk mendukung pengelolaan laboratorium dan kelancaran kegiatan akademik.', 'Catatan: Nama pejabat dan personel dapat dilengkapi sesuai struktur resmi yang berlaku.|Koordinasi Akademik: Pimpinan Program Studi|Pengelolaan: Koordinator Laboratorium|Laboran / Teknisi: Persiapan alat dan bahan, pemeliharaan fasilitas, serta dukungan operasional.|Dosen & Asisten Praktikum: Perencanaan, pendampingan kegiatan, dan pengawasan praktikum.', '', 1, 3],
        ['tata-tertib', 'Tata Tertib Lab', 'profil', 'Tata Tertib Laboratorium', 'Profil Laboratorium', 'Pedoman umum untuk menjaga keselamatan, ketertiban, dan kelancaran kegiatan di laboratorium.', 'Persiapan: Ikuti pengarahan sebelum bekerja, pelajari prosedur percobaan, dan gunakan alat pelindung diri sesuai instruksi.|Selama Kegiatan: Ikuti arahan dosen atau laboran, jaga kebersihan meja kerja, dan jangan makan atau minum di area laboratorium.|Bahan & Peralatan: Gunakan bahan dan peralatan sesuai prosedur. Laporkan kerusakan, tumpahan, atau kondisi tidak aman kepada petugas.|Setelah Kegiatan: Bersihkan area kerja, kelola limbah sesuai instruksi, dan kembalikan peralatan ke tempat yang ditentukan.|Catatan: Ketentuan khusus setiap praktikum tetap mengikuti SOP dan arahan petugas laboratorium.', '', 1, 4],
        ['praktikum', 'Praktikum Mahasiswa', 'layanan', 'Praktikum Mahasiswa', 'Layanan & Praktikum', 'Informasi umum pelaksanaan praktikum kimia untuk mahasiswa FKIP Universitas Nusa Cendana.', 'Sebelum Praktikum: Periksa jadwal dan pembagian kelompok dari program studi, pelajari modul, serta siapkan kebutuhan sesuai arahan pengampu.|Saat Praktikum: Datang sesuai jadwal, gunakan APD yang ditentukan, dan ikuti instruksi keselamatan dari dosen serta petugas laboratorium.|Setelah Praktikum: Rapikan area kerja dan kumpulkan laporan sesuai format serta tenggat yang ditetapkan pengampu mata kuliah.|Informasi Jadwal: Jadwal, kelompok, modul, dan pengumuman praktikum diumumkan melalui program studi atau dosen pengampu. Hubungi laboratorium untuk informasi operasional lebih lanjut.', '', 1, 5],
        ['layanan-analisis', 'Pengujian & Analisis', 'layanan', 'Layanan Pengujian & Analisis', 'Layanan & Praktikum', 'Informasi awal mengenai permohonan layanan analisis sampel kimia oleh Laboratorium Pendidikan Kimia.', 'Alur Permohonan: Hubungi laboratorium untuk menjelaskan jenis sampel dan kebutuhan pengujian.|Konfirmasi: Konfirmasikan ketersediaan metode, instrumen, jumlah sampel, dan persyaratan penanganan.|Penyerahan Sampel: Serahkan sampel dan dokumen sesuai arahan petugas laboratorium.|Proses: Pengujian dan penyampaian hasil mengikuti kesepakatan dengan laboratorium.|Catatan: Jenis layanan bergantung pada metode, kondisi instrumen, bahan, dan kompetensi yang tersedia. Konfirmasikan kebutuhan sebelum mengirimkan sampel.', '', 1, 6],
        ['peminjaman', 'Peminjaman Alat & Bahan', 'layanan', 'Peminjaman Alat & Bahan', 'Layanan & Praktikum', 'Panduan mengajukan penggunaan atau peminjaman alat dan bahan laboratorium untuk kegiatan akademik.', 'Ajukan Permohonan: Sampaikan tujuan kegiatan, daftar alat atau bahan, jumlah, dan waktu penggunaan kepada petugas atau koordinator laboratorium.|Konfirmasi Ketersediaan: Tunggu konfirmasi ketersediaan, persyaratan penggunaan, serta persetujuan dari pihak laboratorium.|Penggunaan: Gunakan alat dan bahan sesuai prosedur keselamatan dan instruksi petugas. Peminjaman mengikuti ketentuan laboratorium.|Pengembalian: Kembalikan alat sesuai waktu dan kondisi yang disepakati, serta laporkan jika ada kerusakan atau kendala.|Catatan Keselamatan: Bahan kimia dan alat laboratorium hanya dapat digunakan sesuai persetujuan serta prosedur yang berlaku. Jangan membawa bahan atau peralatan keluar tanpa izin.', '', 1, 7],
        ['fasilitas', 'Fasilitas', 'halaman', 'Ruang belajar untuk eksperimen dan penemuan', 'Fasilitas Laboratorium', 'Fasilitas laboratorium mendukung praktikum, penelitian, serta pembelajaran kimia dengan memperhatikan keselamatan dan tata kelola ruang kerja.', 'Ruang Kimia Dasar: Untuk pengenalan teknik dasar, pengukuran, larutan, dan reaksi kimia.|Ruang Kimia Organik & Analitik: Mendukung praktikum dan analisis menggunakan metode yang tersedia.|Ruang Kimia Fisik & Anorganik: Menunjang pengamatan sifat fisik, material, dan percobaan kimia anorganik.|Catatan: Ruang dan fasilitas mendukung ragam kegiatan di laboratorium. Ketersediaan dan penggunaannya mengikuti jadwal serta arahan petugas.', 'assets/fasilitas-lab.svg', 1, 8],
    ];

    if ($pageCount === 0) {
        $insert = $pdo->prepare('INSERT INTO pages (slug, menu_label, nav_group, title, eyebrow, intro, body, image_path, published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($pages as $page) {
            $insert->execute($page);
        }
    } else {
        $brokenSlugs = $pdo->query('SELECT slug FROM pages WHERE image_path IN ("1","2","3","4","5","6","7","8")')->fetchAll(PDO::FETCH_COLUMN);
        if ($brokenSlugs) {
            $repair = $pdo->prepare('UPDATE pages SET menu_label=?, nav_group=?, title=?, eyebrow=?, intro=?, body=?, image_path=?, published=?, sort_order=? WHERE slug=?');
            foreach ($pages as $page) {
                if (in_array($page[0], $brokenSlugs, true)) {
                    $repair->execute([$page[1], $page[2], $page[3], $page[4], $page[5], $page[6], $page[7], $page[8], $page[9], $page[0]]);
                }
            }
        }
    }
    $pdo->exec('UPDATE pages SET intro = "" WHERE slug = "beranda" AND intro = "Universitas Nusa Cendana Kupang|Fakultas Keguruan dan Ilmu Pendidikan|Pendidikan Kimia"');

    $legacyPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'content.php';
    $equipmentCount = (int) $pdo->query('SELECT COUNT(*) FROM equipment')->fetchColumn();
    $newsCount = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
    if (is_file($legacyPath) && ($equipmentCount === 0 || $newsCount === 0)) {
        $legacy = require $legacyPath;
        if (isset($legacy['equipment']) && $equipmentCount === 0) {
            $insert = $pdo->prepare('INSERT INTO equipment (slug, name, category, description, image_path, specifications) VALUES (?, ?, ?, ?, ?, ?)');
            foreach ($legacy['equipment'] as $slug => $item) {
                $insert->execute([$slug, $item['name'], $item['category'], $item['description'], $item['image'], implode("\n", $item['specifications'])]);
            }
        }
        if (isset($legacy['news']) && $newsCount === 0) {
            $insert = $pdo->prepare('INSERT INTO news (slug, title, category, published_at, summary, body, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)');
            foreach ($legacy['news'] as $slug => $article) {
                $insert->execute([$slug, $article['title'], $article['category'], $article['date'], $article['summary'], implode("\n\n", $article['body']), $article['image']]);
            }
        }
    }
}

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_settings()
{
    static $settings = null;
    if ($settings === null) {
        $settings = app_db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $settings;
}

function app_setting($key, $fallback = '')
{
    $settings = app_settings();
    return isset($settings[$key]) ? $settings[$key] : $fallback;
}

function public_page_path($slug)
{
    $routes = [
        'beranda' => 'index.php',
        'tentang' => 'tentang.php',
        'visi-misi' => 'visi-misi.php',
        'struktur-organisasi' => 'struktur-organisasi.php',
        'tata-tertib' => 'tata-tertib.php',
        'praktikum' => 'praktikum.php',
        'layanan-analisis' => 'layanan-analisis.php',
        'peminjaman' => 'peminjaman.php',
        'fasilitas' => 'fasilitas.php',
    ];

    return isset($routes[$slug]) ? $routes[$slug] : 'halaman.php?slug=' . rawurlencode($slug);
}
