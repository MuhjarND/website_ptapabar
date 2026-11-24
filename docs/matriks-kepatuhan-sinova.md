# Matriks Kepatuhan Konten SINOVA - PTA Papua Barat

**Acuan:** SK Dirjen Badilag No. 2722/DJA/SK.TI1/X/2026, 12 Oktober 2026, Lampiran II bagian A, halaman 15-22 PDF. Bagian ini mencakup 24 indikator informasi berkala (40% nilai keseluruhan) dan 11 indikator informasi wajib (20%). Matriks ini adalah inventaris awal, bukan penilaian resmi SINOVA.

**Basis pemeriksaan:** kode dan seeder workspace, 19 November 2026. MySQL lokal tidak merespons saat diperiksa, sehingga isi database, status tayang, dokumen WordPress lama, dan akses publik belum diverifikasi. Jalur lokal di tabel relatif terhadap root aplikasi; jalur yang bersumber dari seeder hanya kandidat, belum tentu tersedia di database aktif. URL yang diberi tanda **Usulan** belum ditemukan sebagai halaman khusus. PIC adalah usulan unit pemilik data, bukan penugasan resmi.

| Status awal | Arti |
|---|---|
| Isi | Template halaman sudah ada di seeder. Isi resmi perlu dihimpun dan bukti tayang belum diperiksa. |
| Sesuaikan | Ada halaman atau fitur terkait, tetapi struktur, konten, atau tujuan tautannya perlu penyesuaian. |
| Rancang | Belum ditemukan penyajian yang sesuai di kode. Tentukan sumber resmi sebelum memilih input manual atau integrasi. |
| Uji | Fitur sudah tersedia di kode. Perlu bukti operasional dan verifikasi di situs tayang. |
| N/A | Kriteria di Lampiran II ditujukan untuk pengadilan tingkat pertama; tidak dihitung sebagai kekurangan PTA dalam audit internal. |

**Rekap awal:** 26 perlu diisi atau diverifikasi isinya, 5 perlu disesuaikan, 2 perlu dirancang, 1 perlu diuji secara operasional, dan 1 ditandai N/A. Jumlah ini bukan skor.

## Pemeriksaan 24 November 2026

Pembaruan: lihat [hasil penyesuaian per butir dan verifikasi 30 PDF](hasil-penyesuaian-lampiran-sk.md) serta [langkah pemasangan di subdomain](rilis-sinova-subdomain.md).

Pembobotan Lampiran II: **konten 60%, desain dan aksesibilitas 20%, keamanan dan performa 20%**. Tabel di bawah tetap menjadi catatan pemeriksaan awal. Temuan terbaru membedakan kode lokal dari isi subdomain tes sebelum rilis.

| Area | Sudah diperbaiki di kode lokal | Yang masih perlu dilengkapi atau diuji |
|---|---|---|
| Perkara | Pintasan SIPP Banding, tautan koleksi Direktori Putusan PTA, halaman e-Court, serta penjelasan Panggilan Ghaib tingkat pertama. Eviden SINOVA mengarah ke jadwal banding per tanggal dan statistik per tahun saat ada data publik. | Halaman jadwal dan statistik di subdomain tes tidak menampilkan data pada tanggal dan tahun yang diperiksa. Terbitkan jadwal harian, rekap bulanan, laporan penggunaan biaya perkara, serta prosedur dan tarif resmi. Verifikasi akses koleksi putusan dengan browser tanpa login. |
| Profil dan pelayanan | Alamat Katebu dan foto gedung, tugas pokok dan fungsi, serta 12 kabupaten dan 1 kota per PA dengan sumber dan peta. Halaman Standar dan Maklumat memakai arsip resmi jika belum diisi admin. | Terbitkan diagram organisasi dan profil lengkap yang terbaru. Konfirmasi standar dan maklumat versi yang masih berlaku. |
| Survei dan akses informasi | Halaman `/hasil-survei-pelayanan` menampilkan nilai, periode, dan tautan laporan yang tayang. SINOVA menandai jenis laporan yang belum lengkap. | Terbitkan nilai dan laporan untuk SKM, SPAK, dan SPKL. Isi laporan akses informasi, rekap pengaduan, dan hukuman disiplin dari rekap resmi yang boleh dipublikasikan, bukan tiket atau laporan mentah. |
| SAKIP, keuangan, dan LHKPN | Gunakan Halaman dan Dokumen Transparansi yang sudah ada; tidak diperlukan menu unduhan baru. | Sejumlah halaman di subdomain tes masih kosong, antara lain Rencana Aksi Kinerja, Laporan Tahunan, Renstra, DIPA, RKT, LKjIP dan LHKPN. Periksa dokumen yang telah dihimpun di [inventaris arsip](inventaris-dokumen-sinova.md), tahun cakupan, tiga komponen DIPA, dan ikhtisar LHKPN per pejabat sebelum tayang. |
| Desain dan aksesibilitas | Navigasi, pencarian, kontras tinggi, ukuran teks, serta fitur baca halaman sudah tersedia. | Uji keyboard dan pembaca layar sungguhan, tampilan ponsel, pencarian, dan hierarki menu. Fitur baca halaman bukan bukti otomatis dukungan pembaca layar. |
| Keamanan dan performa | Subdomain tes dapat diakses via HTTPS. | Autentikasi multifaktor admin belum diterapkan. Verifikasi HTTPS dan versi PHP/CMS pada domain produksi, ukur waktu muat pertama sesuai Lampiran II, dan cek semua tautan eviden tanpa login setelah rilis. Belum ada skor resmi. |

Sumber konten lokal: [alamat dan foto kantor](https://pta-papuabarat.go.id/tentang-pengadilan/profil-pengadilan/alamat-dan-kontak/), [tugas dan fungsi](https://pta-papuabarat.go.id/tentang-pengadilan/profil-pengadilan/fungsi-tugas-dan-yuridiksi-pengadilan/), [SK Ketua MA, Lampiran II halaman 65](https://jdih.mahkamahagung.go.id/storage/uploads/produk_hukum/2025/2025sk-kma28/KMA/SK.OT1.1/II/2025.pdf), [standar dan maklumat](https://pta-papuabarat.go.id/informasi-umum/standar-dan-maklumat-pelayanan/), serta [koleksi putusan dari situs lama](https://pta-papuabarat.go.id/informasi-umum/layanan-informasi-perkara/direktori-putusan/).

Rilis kode ini tidak membutuhkan migrasi atau seeder baru. Jika `COURT_VERDICTS_URL` masih diisi dengan tautan lama, kosongkan agar aplikasi memakai tautan resmi bawaan. Pastikan `WORDPRESS_MEDIA_URL` masih mengarah ke direktori arsip yang dapat diakses publik dan berisi foto gedung serta berkas eviden.

## A1. Informasi berkala

| No. / kode SK | Indikator | URL atau lokasi kandidat | Status | Bukti yang perlu disiapkan / tindakan | PIC usulan |
|---|---|---|---|---|---|
| 01 / 1.1 | Rencana Aksi Kinerja | `/halaman/rencana-dan-kinerja` | Isi | Daftar dan tautan dokumen 5 tahun terakhir bila usia satker minimal 5 tahun; jika belum 5 tahun, arsip sampai periode terbaru. Gunakan arsip yang sama dengan butir 16. | Perencanaan |
| 02 / 1.2 | Laporan Pelaksanaan Kegiatan | `/halaman/laporan-tahunan` | Isi | Daftar dan tautan laporan tahunan/pelaksanaan kegiatan 5 tahun terakhir bila usia satker minimal 5 tahun, atau sampai periode terbaru untuk satker yang lebih baru. | Kesekretariatan |
| 03 / 2.1 | SIPP | Beranda: Akses Cepat, Daftar Aplikasi; footer | Sesuaikan | Inventaris semua pintasan SIPP; seeder masih memakai alamat umum. Untuk PTA, arahkan ke `https://banding.mahkamahagung.go.id/` sebagaimana tercantum di SK. | Kepaniteraan + Tim IT |
| 04 / 2.2 | Agenda/Jadwal Persidangan | `/halaman/jadwal-sidang` | Rancang | Jadwal persidangan banding **per hari** dari Kepaniteraan. Jangan memakai `/agenda` yang berisi Agenda Pimpinan. Tentukan sumber dan cara menampilkan hari tanpa sidang. | Kepaniteraan |
| 05 / 2.3 | Statistik perkara | `/halaman/statistik-perkara` | Rancang | Tabel bulanan: sisa bulan lalu, masuk, putus, dan sisa saat ini. Konfirmasi sumber rekap dan pembaruan per bulan. | Kepaniteraan |
| 06 / 2.4 | Panggilan Ghaib | `/halaman/panggilan-ghaib` | Isi | Untuk PTA, publikasikan penjelasan Panggilan Ghaib pada tingkat pertama. Tidak perlu menyalin daftar pihak dan relaas dari PA. | Kepaniteraan |
| 07 / 2.5 | Laporan penggunaan biaya perkara | `/halaman/laporan-penggunaan-biaya-perkara` | Isi | Terbitkan rincian penggunaan biaya perkara setiap bulan; bedakan laporan realisasi ini dari tarif pada butir wajib 09. | Kepaniteraan |
| 08 / 2.6 | Laporan pengembalian sisa panjar | `/halaman/laporan-pengembalian-sisa-panjar` (template lama) | N/A | Kriteria dinyatakan untuk tingkat pertama. Tidak perlu membuat laporan dummy atau menghapus halaman yang masih berguna. | Kepaniteraan |
| 09 / 3.1 | LHKPN | `/halaman/laporan-lhkpn-dan-lhkasn` | Isi | Daftar pejabat, tahun penerbitan terakhir, dan tautan **Ikhtisar Kekayaan** per pejabat. SK menyebut cakupan minimal sampai dua tahun yang lalu (hlm. 17); konfirmasi periode publikasi. Jangan unggah berkas sumber yang tidak untuk publik. | Kepegawaian |
| 10 / 3.2 | DIPA | `/halaman/dipa` | Isi | Lengkapi tiga komponen: DIPA, rencana penggunaan anggaran, dan realisasi DIPA. | Keuangan |
| 11 / 3.3 | Laporan Realisasi Anggaran (LRA) | `/halaman/realisasi-anggaran` | Isi | LRA resmi dengan periode yang jelas dan tautan berkas yang dapat dibuka publik. | Keuangan |
| 12 / 3.4 | Catatan atas Laporan Keuangan (CALK) | `/halaman/catatan-atas-laporan-keuangan-calk` | Isi | CALK resmi sesuai periode pelaporan. | Keuangan |
| 13 / 3.5 | Pengadaan Barang dan Jasa | `/halaman/pengadaan-barang-dan-jasa` | Isi | Rencana Umum Pengadaan satker; pastikan tautan langsung menuju data yang dimaksud. | Pengadaan |
| 14 / 3.6 | Renstra | `/halaman/renstra` | Isi | Daftar dan tautan rancangan, dokumen, atau reviu Renstra yang relevan. Cakupan 5 tahun terakhir bagi satker berusia minimal 5 tahun, atau tahun berjalan bagi satker baru. | Perencanaan |
| 15 / 3.7 | Rencana Kinerja Tahunan (RKT) | `/halaman/rkt` | Isi | RKT minimal 5 tahun terakhir bagi satker berusia minimal 5 tahun, atau tahun berjalan bagi satker baru. | Perencanaan |
| 16 / 3.8 | Rencana Aksi Kinerja tahun berjalan | `/halaman/rencana-dan-kinerja` | Isi | Pastikan dokumen tahun berjalan terlihat jelas di arsip yang sama dengan butir 01. Kedua indikator tetap dicatat terpisah. | Perencanaan |
| 17 / 3.9 | Perjanjian Kinerja (PK) | `/halaman/perjanjian-kinerja` | Isi | PK minimal 5 tahun terakhir bagi satker berusia minimal 5 tahun, atau tahun berjalan bagi satker baru. | Perencanaan |
| 18 / 3.10 | IKU dan Reviu IKU | `/halaman/iku` | Isi | Daftar dan tautan IKU dan/atau reviunya. Cakupan minimal 5 tahun terakhir bagi satker berusia minimal 5 tahun, atau tahun berjalan bagi satker baru. | Perencanaan |
| 19 / 3.11 | LKjIP | `/halaman/lkjip` | Isi | LKjIP minimal 5 tahun terakhir bagi satker berusia minimal 5 tahun. Ketentuan untuk satker yang telah berusia 1 tahun: minimal 1 tahun terakhir. | Perencanaan |
| 20 / 4.1 | Laporan Akses Informasi | `/halaman/laporan-akses-informasi`; formulir terkait: `/layanan-publik` | Isi | Publikasikan jumlah permohonan masuk, waktu pemenuhan, jumlah yang dikabulkan sebagian/seluruhnya dan ditolak, serta alasan penolakan. Formulir masuk bukan pengganti laporan. | PPID |
| 21 / 4.2 | Laporan Rekapitulasi Pengaduan | `/halaman/laporan-rekapitulasi-pengaduan` | Isi | Publikasikan laporan yang disetujui: inisial pelapor, jenis dan ringkasan pelanggaran, status/tindak lanjut, serta sanksi jika ada. Jangan tampilkan tiket dan lampiran mentah. | Pengaduan + Pengawasan |
| 22 / 4.3 | Hukuman Disiplin Hasil Pengawasan | `/halaman/hukuman-disiplin` | Isi | Publikasikan ringkasan resmi: inisial penerima hukuman, jenis dan ringkasan pelanggaran, status/tindak lanjut, dan hukuman yang diberikan. | Pengawasan + Kepegawaian |
| 23 / 4.4 | Survei Pelayanan Masyarakat | Beranda: Survei & Indeks Pelayanan Publik; **Usulan** `/halaman/hasil-survei-pelayanan` | Sesuaikan | Kartu yang ada hanya menayangkan angka. Sajikan nilai **dan laporan** per periode untuk Survei Kepuasan Masyarakat, Survei Persepsi Anti Korupsi, dan Survei Persepsi Kualitas Layanan. | PTSP / Tim Survei |
| 24 / 5.1 | Statistik Kunjungan Website | Beranda: footer Statistik Web | Uji | Kode sudah menampilkan hari ini, minggu ini, bulan ini, dan total. Verifikasi pencatatan kunjungan nyata; jangan gunakan data pratinjau sebagai eviden. | Tim IT |

## A2. Informasi wajib

| No. / kode SK | Indikator | URL atau lokasi kandidat | Status | Bukti yang perlu disiapkan / tindakan | PIC usulan |
|---|---|---|---|---|---|
| 01 / 1.1 | Visi dan Misi Pengadilan | `/halaman/visi-dan-misi` | Isi | Visi dan misi resmi, masing-masing dapat dibaca langsung pada halaman. | Kesekretariatan |
| 02 / 1.2 | Tugas Pokok dan Fungsi | `/halaman/fungsi-tugas-dan-yuridiksi-pengadilan` | Isi | Sajikan tugas pokok dan fungsi secara eksplisit; pertahankan slug lama meski judul/label dibetulkan. | Kesekretariatan |
| 03 / 1.3 | Wilayah Yurisdiksi | **Usulan** `/halaman/wilayah-yurisdiksi`; template gabungan tersedia pada butir 02 | Sesuaikan | Daftar nama kabupaten/kota di wilayah hukum PTA dan peta yurisdiksi. Peta lokasi kantor di footer tidak menggantikan peta wilayah hukum. | Kepaniteraan + Kesekretariatan |
| 04 / 1.4 | Struktur Organisasi | `/halaman/struktur-organisasi-pengadilan` | Isi | Diagram, nama pejabat, dan nama jabatan. Staf dan tenaga honorer tidak perlu dimasukkan dalam diagram. | Kepegawaian |
| 05 / 1.5 | Alamat dan Kontak Pengadilan | `/halaman/alamat-dan-kontak`; topbar dan footer sudah menampilkan sebagian kontak | Isi | Nama jalan, kelurahan/desa, kecamatan, provinsi, kode pos, telepon, email, URL resmi, dan **foto gedung**. | Umum / PTSP |
| 06 / 2.1 | e-Court | `/halaman/ecourt`; tersedia pintasan aplikasi | Isi | Isi landing page dengan definisi e-Court dan tautan ke aplikasi e-Court Mahkamah Agung; pintasan saja belum cukup. | Kepaniteraan |
| 07 / 2.2 | Direktori Putusan | `/halaman/direktori-putusan`; tersedia pintasan aplikasi | Sesuaikan | Gunakan tautan langsung ke koleksi putusan **PTA Papua Barat**; seeder masih memakai beranda Direktori Putusan. | Kepaniteraan + Tim IT |
| 08 / 2.3 | Prosedur Berperkara | `/halaman/prosedur-pengajuan-dan-biaya-perkara` beserta halaman turunannya | Isi | Sediakan prosedur tingkat pertama, banding, kasasi, peninjauan kembali, pengambilan produk pengadilan, dan gugatan sederhana. | Kepaniteraan |
| 09 / 2.4 | Biaya Proses Berperkara | `/halaman/biaya-proses-berperkara`; template lain: `/halaman/rincian-biaya-proses-perkara` | Isi | Tampilkan rincian biaya resmi dan pilih satu URL eviden agar dua halaman lama tidak menampilkan angka berbeda. | Kepaniteraan |
| 10 / 3.1 | Profil Pejabat dan Pegawai | `/halaman/daftar-nama-pejabat-dan-hakim` | Sesuaikan | Cakup pejabat, pegawai, dan tenaga honorer. Profil memuat foto, nama dan gelar, TMT relevan, riwayat pendidikan, dan khusus PNS: pangkat/golongan serta riwayat jabatan. Validasi data yang boleh tayang; SK menyebut SIMTEPA sebagai salah satu sumber. | Kepegawaian |
| 11 / 4.1 | Standar dan Maklumat Pelayanan | `/halaman/standar-dan-maklumat-pelayanan` | Isi | Terbitkan **keduanya**: standar pelayanan dan maklumat pelayanan versi resmi. Pertahankan satu URL publik yang mudah ditemukan. | PTSP / Tim Pelayanan |

## Tindak lanjut

Temuan lanjutan untuk SAKIP dan keuangan dicatat di [inventaris dokumen SINOVA](inventaris-dokumen-sinova.md). Dokumen sumber yang ditemukan belum diverifikasi sebagai eviden pada situs Laravel.

1. **Inventarisasi dokumen resmi.** Minta unit terkait mencatat kategori, tahun/periode, versi, URL/berkas sumber, dan izin tayang, termasuk berkas WordPress lama. Konfirmasi usia satker dan periode yang berlaku sebelum menyatakan arsip lengkap. Bedakan laporan periodik yang sudah terbit dari dokumen yang belum jatuh tempo.
2. **Isi halaman yang sudah punya template.** Dahulukan Profil, Prosedur Berperkara, Panggilan Ghaib, serta Standar dan Maklumat Pelayanan. Siapkan pengelolaan PDF/tautan berperiode dari admin untuk SAKIP dan Keuangan; tampilkan pada halaman terkait, tanpa membuat menu unduhan umum.
3. **Konfirmasi sumber Kepaniteraan.** Minta satu contoh jadwal harian dan satu rekap perkara bulanan. Baru tentukan apakah data dimasukkan admin, diimpor dari berkas, atau diambil dari sumber resmi. Betulkan pintasan SIPP Banding dan verifikasi tautan koleksi putusan PTA.
4. **Verifikasi sebelum memasukkan eviden.** Untuk setiap butir, catat URL publik spesifik, periode, tanggal cek, pemeriksa, dan catatan. Uji tanpa login, termasuk tautan PDF, dan cek isi yang terbaca pada tangkapan layar. Teks placeholder, konten dummy, atau sekadar respons HTTP 200 bukan bukti kelengkapan. Konfirmasi penerapan N/A dengan instrumen SINOVA yang digunakan.

Rujukan kode: [template halaman](../database/seeds/DatabaseSeeder.php), [tautan aplikasi](../database/seeds/SiteApplicationSeeder.php), [tautan footer](../database/seeds/FooterLinkSeeder.php), [pintasan pratinjau](../database/seeds/HomepageDemoSeeder.php), [rute](../routes/web.php), [survei](../resources/views/public/partials/home/surveys.blade.php), [statistik web](../resources/views/public/partials/footer.blade.php).
