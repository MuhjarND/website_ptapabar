<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Page;
use App\Setting;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create author user
        User::create([
            'name' => 'Author',
            'email' => 'author@author.com',
            'password' => bcrypt('password'),
            'role' => 'author',
        ]);

        // Settings
        Setting::set('site_name', 'Pengadilan Tinggi Agama Papua Barat');
        Setting::set('site_description', 'Website Resmi Pengadilan Tinggi Agama Papua Barat - Mahkamah Agung Republik Indonesia');
        Setting::set('address', 'Jl. Brawijaya, Kelurahan Manokwari Timur, Distrik Manokwari, Provinsi Papua Barat. Kode POS 98311');
        Setting::set('phone', '0811 4088 3744');
        Setting::set('email', 'ptapapuabarat@gmail.com');
        Setting::set('fax', '0811 4088 3744');

        $placeholder = '<p>Konten halaman ini sedang dalam proses pembaruan. Silakan hubungi admin untuk informasi lebih lanjut.</p>';

        // ==============================
        // TENTANG PENGADILAN
        // ==============================
        $menuGroup = 'tentang-pengadilan';
        $pages = [
            'Visi dan Misi',
            'Daftar Nama Pejabat dan Hakim',
            'Daftar Nama Mantan Pimpinan',
            'Struktur Organisasi Pengadilan',
            'Standar Operasional Prosedur',
            'Fungsi Tugas dan Yuridiksi Pengadilan',
            'Alamat dan Kontak',
            'Sejarah Pengadilan',
        ];
        foreach ($pages as $i => $title) {
            Page::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => $placeholder,
                'menu_group' => $menuGroup,
                'order' => $i,
                'is_active' => true,
            ]);
        }

        // ==============================
        // INFORMASI UMUM
        // ==============================
        $menuGroup = 'informasi-umum';

        // Informasi Layanan Pengadilan (parent with children)
        $parent = Page::create(['title' => 'Informasi Layanan Pengadilan', 'slug' => 'informasi-layanan-pengadilan', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 0, 'is_active' => true]);
        foreach (['Jam Kerja Kantor', 'Jadwal Sidang', 'Tata Tertib Persidangan'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Layanan Informasi Perkara
        $parent = Page::create(['title' => 'Layanan Informasi Perkara', 'slug' => 'layanan-informasi-perkara', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 1, 'is_active' => true]);
        foreach (['Statistik Perkara', 'Direktori Putusan'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Layanan Permintaan Informasi
        $parent = Page::create(['title' => 'Layanan Permintaan Informasi', 'slug' => 'layanan-permintaan-informasi', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 2, 'is_active' => true]);
        foreach ([
        'Tata Cara Memperoleh Pelayanan Informasi',
        'Hak-hak Pemohon Dalam Pelayanan Informasi',
        'Prosedur Keberatan Atas Permintaan Informasi',
        'Biaya Memperoleh Salinan Informasi',
        'Contoh Formulir Permintaan Informasi',
        'Laporan Akses Informasi',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Pengaduan Masyarakat
        $parent = Page::create(['title' => 'Pengaduan Masyarakat', 'slug' => 'pengaduan-masyarakat', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 3, 'is_active' => true]);
        foreach ([
        'Form Pengaduan',
        'Tata Cara Pengaduan',
        'Laporan Rekapitulasi Pengaduan',
        'Hak-hak Pelapor Dugaan Pelanggaran',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Informasi Umum standalone pages
        $standalones = [
            'Fasilitas Publik',
            'Standar dan Maklumat Pelayanan',
            'Pedoman Pengelolaan Kesekretariatan',
            'Unit Teknis Kesekretariatan',
            'Prosedur Evakuasi',
            'Statistik Jumlah Pegawai',
            'Putusan Majelis Kehormatan Hakim',
            'Petugas Informasi Pelayanan Terpadu Satu Pintu',
            'SOP Khusus Pelayanan Publik',
            'Maklumat Layanan Informasi Publik',
        ];
        foreach ($standalones as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => $i + 4, 'is_active' => true]);
        }

        // ==============================
        // INFORMASI HUKUM
        // ==============================
        $menuGroup = 'informasi-hukum';

        // Prosedur Pengajuan dan Biaya Perkara
        $parent = Page::create(['title' => 'Prosedur Pengajuan dan Biaya Perkara', 'slug' => 'prosedur-pengajuan-dan-biaya-perkara', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 0, 'is_active' => true]);
        foreach ([
        'PB Tingkat Pertama',
        'PB Gugatan Sederhana',
        'PB Tingkat Banding',
        'PB Tingkat Kasasi',
        'Peninjauan Kembali',
        'Tata Cara Pengambilan Produk Pengadilan',
        'eCourt',
        'Rincian Biaya Proses Perkara',
        'Laporan Pengembalian Sisa Panjar',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Hak-hak
        $parent = Page::create(['title' => 'Hak-hak', 'slug' => 'hak-hak', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 1, 'is_active' => true]);
        foreach ([
        'Hak-hak Pokok Dalam Persidangan',
        'Hak-hak Pencari Keadilan',
        'Hak-hak Perempuan dan Anak Pasca Persidangan',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Perkara Prodeo
        $parent = Page::create(['title' => 'Perkara Prodeo', 'slug' => 'perkara-prodeo', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 2, 'is_active' => true]);
        foreach ([
        'Syarat-syarat Perkara Prodeo',
        'Rincian Biaya Prodeo',
        'Prosedur Berperkara Prodeo',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Biaya Berperkara
        $parent = Page::create(['title' => 'Biaya Berperkara', 'slug' => 'biaya-berperkara', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 3, 'is_active' => true]);
        foreach ([
        'Biaya Proses Berperkara',
        'Biaya Hak-hak Kepaniteraan',
        'Radius Biaya Panggilan',
        'Laporan Penggunaan Biaya Perkara',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Pengawasan
        $parent = Page::create(['title' => 'Pengawasan', 'slug' => 'pengawasan-hukum', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 4, 'is_active' => true]);
        foreach ([
        'Pedoman Pengawasan',
        'Kode Etik Hakim',
        'Nama-nama Pejabat Pengawas',
        'Hukuman Disiplin',
        'Putusan Kehormatan Hakim',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Pedoman Pengelolaan Kepaniteraan
        Page::create(['title' => 'Pedoman Pengelolaan Kepaniteraan', 'slug' => 'pedoman-pengelolaan-kepaniteraan', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 5, 'is_active' => true]);

        // Posbakum
        $parent = Page::create(['title' => 'Posbakum', 'slug' => 'posbakum', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 6, 'is_active' => true]);
        foreach ([
        'Penerima Jasa',
        'Jenis Jasa',
        'Syarat Posbakum',
        'Dasar Aturan Posbakum',
        'Keberadaan Posbakum',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Mediasi
        $parent = Page::create(['title' => 'Mediasi', 'slug' => 'mediasi', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 7, 'is_active' => true]);
        foreach (['Prosedur Mediasi', 'Foto Mediator'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Standalone info hukum pages
        foreach (['Panggilan Ghaib', 'Delegasi Tabayun', 'Radius Biaya Panggilan Hukum'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => $i + 8, 'is_active' => true]);
        }

        // ==============================
        // TRANSPARANSI
        // ==============================
        $menuGroup = 'transparansi';

        Page::create(['title' => 'Laporan Tahunan', 'slug' => 'laporan-tahunan', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 0, 'is_active' => true]);
        Page::create(['title' => 'Laporan LHKPN dan LHKASN', 'slug' => 'laporan-lhkpn-dan-lhkasn', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 1, 'is_active' => true]);

        // Anggaran
        $parent = Page::create(['title' => 'Anggaran', 'slug' => 'anggaran', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 2, 'is_active' => true]);
        foreach (['DIPA', 'POK', 'Neraca', 'Realisasi Anggaran', 'Realisasi PNBP', 'Catatan Atas Laporan Keuangan CALK'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // SAKIP
        $parent = Page::create(['title' => 'SAKIP', 'slug' => 'sakip', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 3, 'is_active' => true]);
        foreach (['IKU', 'Renstra', 'RKT', 'Perjanjian Kinerja', 'LKJIP', 'Rencana dan Kinerja'] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        // Transparansi standalone
        foreach ([
        'Daftar Aset dan Investasi',
        'Pengadaan Barang dan Jasa',
        'Arsip File-file Multimedia',
        'Arsip Hasil Penelitian',
        'Arsip Surat Perjanjian dengan Pihak Ketiga MoU',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => $i + 4, 'is_active' => true]);
        }

        // ==============================
        // PERATURAN DAN KEBIJAKAN
        // ==============================
        $menuGroup = 'peraturan-kebijakan';

        $parent = Page::create(['title' => 'Pengawasan', 'slug' => 'pengawasan-kebijakan', 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => 0, 'is_active' => true]);
        foreach ([
        'Kode Etik Hakim Kebijakan',
        'Pedoman Pengawasan Kebijakan',
        'Daftar Nama Pejabat Pengawas Kebijakan',
        'SIWAS Mahkamah Agung',
        'Tingkat dan Jenis Hukuman Disiplin',
        'Statistik Hukuman Disiplin',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'parent_id' => $parent->id, 'order' => $i, 'is_active' => true]);
        }

        foreach ([
        'Pedoman Pengelolaan Kepaniteraan Kebijakan',
        'Pedoman Pengelolaan Kesekretariatan Kebijakan',
        'Regulasi Aturan',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => $i + 1, 'is_active' => true]);
        }

        // ==============================
        // INFORMASI
        // ==============================
        $menuGroup = 'informasi';
        foreach ([
        'Tautan ke Media Social',
        'Tautan Instansi Terkait',
        'Ucapan Selamat dan Duka Cita',
        'Aplikasi',
        ] as $i => $t) {
            Page::create(['title' => $t, 'slug' => Str::slug($t), 'content' => $placeholder, 'menu_group' => $menuGroup, 'order' => $i, 'is_active' => true]);
        }
    }
}
