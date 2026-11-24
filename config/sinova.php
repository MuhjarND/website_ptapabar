<?php

return [
    'public_url' => env('SINOVA_PUBLIC_URL'),

    // Codes and groups follow the reporting form supplied by the user.
    'groups' => [
        '1.a.1 Informasi Umum' => [
            '1.a.1.a' => ['Rencana Aksi Kinerja', 'Daftar dan tautan dokumen Rencana Aksi Kinerja 5 tahun terakhir.', 'page:rencana-dan-kinerja'],
            '1.a.1.b' => ['Laporan Pelaksanaan Kegiatan', 'Daftar dan tautan dokumen Laporan Tahunan 5 tahun terakhir.', 'page:laporan-tahunan'],
        ],
        '1.a.2 Kepaniteraan' => [
            '1.a.2.a' => ['Sistem Informasi Penelusuran Perkara (SIPP)', 'Tautan menuju aplikasi SIPP web satuan kerja.', 'sipp'],
            '1.a.2.b' => ['Agenda/Jadwal Persidangan', 'Jadwal persidangan per hari, termasuk untuk pengadilan tingkat banding.', 'hearings'],
            '1.a.2.c' => ['Statistik Perkara', 'Statistik perkara per bulan: sisa bulan lalu, masuk, putus, dan sisa saat ini.', 'statistics'],
            '1.a.2.d' => ['Panggilan Ghaib', 'Informasi panggilan ghaib. Untuk PTA, isi penjelasan dan rujukan tingkat pertama, bukan data pihak atau relaas.', 'page:panggilan-ghaib'],
            '1.a.2.e' => ['Laporan Penggunaan Biaya Perkara', 'Rincian penggunaan biaya perkara per bulan.', 'page:laporan-penggunaan-biaya-perkara'],
            '1.a.2.f' => ['Laporan Pengembalian Sisa Panjar Perkara', 'Rincian pengembalian sisa panjar perkara per bulan (khusus tingkat pertama).', 'not-applicable'],
        ],
        '1.a.3 Kesekretariatan' => [
            '1.a.3.a' => ['Laporan Harta Kekayaan Penyelenggara Negara (LHKP)', 'Daftar pejabat, tahun LHKPN terakhir, dan tautan ikhtisar kekayaan yang boleh dipublikasikan.', 'page:laporan-lhkpn-dan-lhkasn'],
            '1.a.3.b' => ['Daftar Isian Pelaksanaan Anggaran (DIPA)', 'DIPA, Rencana Penggunaan Anggaran, dan Realisasi DIPA.', 'page:dipa'],
            '1.a.3.c' => ['Laporan Realisasi Anggaran (LRA)', 'Laporan Realisasi Anggaran.', 'page:realisasi-anggaran'],
            '1.a.3.d' => ['Catatan Atas Laporan Keuangan (CALK)', 'Catatan Atas Laporan Keuangan.', 'page:catatan-atas-laporan-keuangan-calk'],
            '1.a.3.e' => ['Pengadaan Barang dan Jasa', 'Rencana Umum Pengadaan Barang dan Jasa.', 'page:pengadaan-barang-dan-jasa'],
            '1.a.3.f' => ['Rencana Strategis (Renstra)', 'Rancangan, dokumen, atau reviu Renstra minimal 5 tahun terakhir.', 'page:renstra'],
            '1.a.3.g' => ['Rencana Kinerja Tahunan (RKT)', 'RKT minimal 5 tahun terakhir.', 'page:rkt'],
            '1.a.3.h' => ['Rencana Aksi Kinerja', 'Daftar dan tautan Rencana Aksi Kinerja paling tidak untuk tahun berjalan.', 'page:rencana-dan-kinerja'],
            '1.a.3.i' => ['Perjanjian Kinerja (PK)', 'PK minimal 5 tahun terakhir.', 'page:perjanjian-kinerja'],
            '1.a.3.j' => ['Indikator Kinerja Utama (IKU) dan Reviu IKU', 'IKU atau reviu IKU minimal 5 tahun terakhir.', 'page:iku'],
            '1.a.3.k' => ['Laporan Kinerja Instansi Pemerintah (LKjIP)', 'LKjIP minimal 5 tahun terakhir.', 'page:lkjip'],
        ],
        '1.a.4.1 Layanan Permintaan Informasi' => [
            '1.a.4.1.a' => ['Laporan Akses Informasi', 'Rekap jumlah permohonan, waktu pemenuhan, permohonan dikabulkan atau ditolak, dan alasan penolakan.', 'page:laporan-akses-informasi'],
        ],
        '1.a.4.2 Layanan Pengaduan' => [
            '1.a.4.2.a' => ['Laporan Rekapitulasi Pengaduan', 'Rekap resmi yang boleh dipublikasikan: inisial, jenis pelanggaran, ringkasan, tindak lanjut, dan sanksi.', 'page:laporan-rekapitulasi-pengaduan'],
        ],
        '1.a.4.3 Pengawasan' => [
            '1.a.4.3.a' => ['Hukuman Disiplin Hasil Pengawasan', 'Ringkasan publik mengenai inisial, jenis pelanggaran, tindak lanjut, dan hukuman disiplin.', 'page:hukuman-disiplin'],
        ],
        'A.1 / 4.4 Survei Pelayanan Masyarakat' => [
            'A.1.4.4' => ['Survei Pelayanan Masyarakat', 'Nilai dan laporan untuk Survei Kepuasan Masyarakat, Survei Persepsi Anti Korupsi, dan Survei Persepsi Kualitas Layanan. Kode ini merujuk ke Lampiran II SK.', 'surveys'],
        ],
        '1.a.4.4 Informasi Lainnya' => [
            '1.a.4.4.a' => ['Statistik Kunjungan Website', 'Statistik kunjungan pada bagian Statistik Web di situs publik.', 'visitors'],
        ],
        '1.b Informasi Wajib' => [
            '1.b.1' => ['Visi dan Misi Pengadilan', 'Halaman yang memuat visi dan misi pengadilan.', 'page:visi-dan-misi'],
        ],
        '1.b.2 Kepaniteraan' => [
            '1.b.2.a' => ['e-Court', 'Informasi e-Court dan tautan ke e-Court Mahkamah Agung.', 'ecourt'],
            '1.b.2.b' => ['Direktori Putusan', 'Tautan koleksi Direktori Putusan MA RI untuk satuan kerja ini.', 'verdicts'],
            '1.b.2.c' => ['Prosedur Berperkara', 'Prosedur tingkat pertama, banding, kasasi, PK, pengambilan produk, dan gugatan sederhana.', 'page:prosedur-pengajuan-dan-biaya-perkara'],
            '1.b.2.d' => ['Biaya Proses Berperkara', 'Rincian biaya proses berperkara.', 'page:biaya-proses-berperkara'],
        ],
        '1.b.3 Kesekretariatan' => [
            '1.b.3.a' => ['Profil Pejabat dan Pegawai', 'Daftar pejabat, pegawai, dan honorer beserta profil singkat yang boleh ditayangkan.', 'page:daftar-nama-pejabat-dan-hakim'],
        ],
        '1.b.4 Layanan Publik' => [
            '1.b.4.a' => ['Standar dan Maklumat Pelayanan Pengadilan', 'Standar dan maklumat pelayanan pengadilan.', 'page:standar-dan-maklumat-pelayanan'],
        ],
        '1.b.p Profil Pengadilan' => [
            '1.b.p.a' => ['Tugas Pokok dan Fungsi Pengadilan', 'Tugas pokok dan fungsi pengadilan.', 'page:fungsi-tugas-dan-yuridiksi-pengadilan'],
            '1.b.p.b' => ['Wilayah Yurisdiksi', 'Daftar wilayah yurisdiksi dan peta.', 'page:wilayah-yurisdiksi'],
            '1.b.p.c' => ['Struktur Organisasi', 'Diagram struktur organisasi, nama pejabat, dan jabatan.', 'page:struktur-organisasi-pengadilan'],
            '1.b.p.d' => ['Alamat dan Kontak Pengadilan', 'Alamat, telepon, email, URL resmi, dan foto gedung.', 'page:alamat-dan-kontak'],
        ],
        '2.a Mobile Responsiveness' => [
            '2.a.1' => ['Mobile Responsiveness', 'Situs dapat digunakan pada smartphone dan tablet.', 'home'],
        ],
        '2.b Aksesibilitas (WCAG)' => [
            '2.b.1' => ['Aksesibilitas (Ramah Disabilitas / WCAG Compliance)', 'Periksa dukungan pembaca layar, kontras tinggi, dan pembesaran teks.', 'checklist'],
        ],
        '2.c Navigasi & Pencarian' => [
            '2.c.1' => ['Navigasi & Pencarian (Searchability)', 'Periksa kolom pencarian internal dan susunan menu.', 'choice'],
        ],
        '3.a Keamanan Website (Security)' => [
            '3.a.1' => ['Keamanan Website (Security)', 'Periksa HTTPS, MFA untuk login, serta versi CMS dan PHP.', 'checklist'],
        ],
        '3.b Kecepatan Akses (Page Speed)' => [
            '3.b.1' => ['Kecepatan Akses (Page Load Speed)', 'Waktu muat pertama: kurang dari 5 detik (10), 5 sampai 15 detik (5), lebih dari 15 detik (0).', 'api'],
        ],
        '3.c Validitas Tautan' => [
            '3.c.1' => ['Validitas Tautan (Broken Links)', 'Periksa tautan rusak dan halaman 404 pada menu utama.', 'manual'],
        ],
    ],
];
