# Kesiapan Rilis Portal PTA Papua Barat

Dokumen ini mencatat pekerjaan tahap layanan hukum, aksesibilitas, dan persiapan perpindahan WordPress. Rujukan konten: [Matriks SINOVA](matriks-kepatuhan-sinova.md) dan [Inventaris dokumen](inventaris-dokumen-sinova.md).

## Yang siap divalidasi di pratinjau

- Enam kelompok utama: Beranda, Profil Pengadilan, Layanan Hukum, Transparansi, Layanan Publik & Pengaduan, serta Berita dan Publikasi. URL halaman lama tetap tersedia; URL jadwal sidang dan statistik perkara lama diarahkan ke halaman khusus.
- Admin: **Jadwal Sidang Banding** menerima tanggal, jam WIT, keterangan publik, dan lokasi. **Statistik Perkara** menerima satu data per bulan, dengan angka sisa awal, masuk, putus, dan sisa akhir. Edit atau simpan tanpa centang Terbitkan untuk mempertahankan data sebagai draf.
- Admin: **Survei & Indeks** menerima periode dan tautan HTTPS laporan. Tautan resmi survei WordPress yang lama perlu diperiksa kembali sebelum menyediakan pintasan pengisian survei publik.
- Menu dan sub-menu dapat dijelajahi dengan tombol keyboard. Kontrol Aksesibilitas menyediakan kontras tinggi, perbesar konten hingga 150%, dan pembacaan suara bila browser mendukungnya. Fitur ini belum merupakan sertifikasi WCAG; tetap uji dengan screen reader yang sebenarnya.
- Unduhan lampiran layanan publik kini melalui admin. Berkas **baru** disimpan privat. Inventaris file lama di disk publik: salin ke penyimpanan privat, ubah referensi secara terkontrol, uji akses admin, baru blokir URL lama setelah verifikasi. Jangan menampilkan lampiran atau tiket mentah pada laporan publik.

## Data yang harus disahkan

| Pemilik data | Kebutuhan sebelum publikasi |
|---|---|
| Kepaniteraan | Contoh jadwal banding harian dan rekap bulanan yang disetujui untuk publik. Konfirmasi URL koleksi putusan PTA pada host Direktori Putusan MA dan isi `COURT_VERDICTS_URL`. Jangan menebak slug pengadilan. |
| Keuangan / Perencanaan | Verifikasi draf dokumen dari WordPress, pilih versi final, lengkapi tahun berjalan, dan arsipkan periode yang diwajibkan. |
| Kepegawaian / Umum / PTSP | Verifikasi profil aparatur, struktur, peta yurisdiksi, foto dan alamat kantor. Beranda resmi sudah menampilkan Katebu; cocokkan alamat dan titik peta sebelum menerapkannya di pengaturan situs. Peta footer mengikuti alamat yang tersimpan. |
| PPID / Pengawasan / Tim Survei | Isi halaman standar, maklumat, PPID dan laporan yang disetujui. Masukkan periode, nilai, dan tautan publik untuk laporan survei resmi. |

Jangan menyalin database preview ke produksi. Hapus atau ganti data demo melalui pengelolaan konten pada database yang benar sebelum tayang.

## Keamanan dan perpindahan

1. **Siapkan klona staging terpisah.** Cadangkan database, storage publik dan privat, serta file konfigurasi dengan akses terbatas. Pulihkan backup ke lingkungan uji terpisah dan periksa bahwa login, dokumen, berita, dan lampiran berfungsi. Tentukan penanggung jawab rollback domain. Tidak ada `migrate:fresh`.
2. **Gunakan PHP 8.5 dan Laravel 12 di staging.** Kode dan lockfile telah diperbarui mengikuti [panduan Laravel 12](https://laravel.com/docs/12.x/upgrade). Instal dari lockfile dengan `composer install --no-dev`, jalankan `composer check-platform-reqs --no-dev` dan `composer audit --locked`, lalu uji dengan konfigurasi hosting sebelum alih domain. PHP Apache XAMPP dan runtime hosting tidak diubah.
3. **Aktifkan MFA pada akun panel** melalui sistem autentikasi yang sudah menyokong MFA setelah upgrade, atau gateway identitas institusi pada /login dan /admin yang benar-benar tidak dapat dilewati dari origin. Uji login tanpa faktor kedua, pemulihan akun, dan pemisahan hak admin/author. MFA belum aktif di pratinjau ini.
4. **Pasang TLS dan validasi origin.** Gunakan document root Laravel `public`, sertifikat sah dan TLS 1.2+, user database non-root, serta WAF dan backup offsite terjadwal. Opsi `SITE_ENFORCE_HTTPS=true` hanya diaktifkan dengan `APP_URL` HTTPS dan trusted proxy yang sudah diverifikasi; jika tidak, redirect dapat berulang. Endpoint form memiliki rate limit, tetapi ini bukan pengganti MFA, audit, atau WAF.
5. **Lindungi URL sumber dan uji sebelum alih domain.** `SITE_WORDPRESS_REDIRECTS=true` mengaktifkan pemetaan awal di `config/site-deployment.php` untuk URL yang sudah diketahui. Simpan atau alihkan berkas `/wp-content/uploads/` dan `/pengumuman/wp-content/uploads/` secara spesifik di server. Lengkapi pemetaan URL dan media dari ekspor WordPress resmi; jangan arahkan semua 404 ke beranda. Uji data, URL lama, respons PDF tanpa login, formulir, MFA, HTTPS, dan aksesibilitas sebelum mengubah DNS.

Perubahan DNS, konfigurasi hosting, MFA, runtime produksi, dan impor ke database produksi belum dijalankan.

## Hosting Tanpa Symlink

Jika hosting tidak mengizinkan `php artisan storage:link`, lewati perintah itu. Aplikasi dapat melayani URL `/storage/...` yang tidak dilayani langsung oleh web server dari `storage/app/public`. Unggah atau pulihkan folder ini ke direktori Laravel dengan struktur file yang sama. Jangan salin seluruh `storage/app` ke folder publik.

Pastikan document root tetap `public`, URL `/storage/...` yang tidak ditemukan diteruskan ke `index.php`, dan tidak ada symlink rusak atau aturan server yang menolak URL sebelum Laravel menerimanya. Jika rute sebelumnya di-cache, jalankan `php artisan route:clear` setelah deploy. Uji gambar unggahan Laravel dan satu PDF publik. Arsip WordPress tetap memakai URL unggahan WordPress dan tidak memerlukan symlink ini.

Untuk optimasi gambar beranda, aktifkan ekstensi `gd` dengan dukungan WebP pada PHP 8.5 yang dipakai web server. Ekstensi `exif` mempertahankan orientasi foto JPEG dari kamera. Pastikan `storage/framework/cache` bisa ditulis; versi gambar yang diperkecil dibuat saat pertama diminta dan disimpan di `storage/framework/cache/image-previews`. Cache ini bisa dibersihkan jika ruang penyimpanan menipis. File asli tidak diubah. Gambar dari URL WordPress atau YouTube tetap dimuat dari asalnya, tanpa diproses ulang oleh Laravel.

## Arsip WordPress Tanpa Sinkronisasi

Konten berita, pengumuman, artikel, dan data slider yang sudah diimpor dimiliki Laravel. Situs tidak membutuhkan aplikasi, API, database, atau jadwal sinkronisasi WordPress. Foto dan dokumen tetap disajikan sebagai file statis melalui URL asal. Bagian **Dokumentasi** memakai lampiran yang tercatat untuk posting itu, referensi galeri, serta gambar dan PDF yang ditautkan di dalam isi berita.

1. Pada saat menonaktifkan WordPress, pertahankan akses HTTPS ke `/wp-content/uploads/` dan jalur unggahan lama lain yang masih digunakan. Jika folder disajikan di lokasi lain (misalnya `/wp-uploads/`), pertahankan struktur tahunnya dan atur `WORDPRESS_MEDIA_URL` ke URL HTTPS folder tersebut. Jangan arahkan URL gambar/PDF ke beranda Laravel.
2. Arsipkan hanya berkas yang boleh dipublikasikan. Layani folder unggahan sebagai file statis, tanpa eksekusi PHP atau directory listing. Folder `wp-admin`, `wp-includes`, tema, dan plugin tidak diperlukan.
3. Deploy data Laravel yang sudah diverifikasi, termasuk `post_photos`. Jika database hosting belum memiliki arsip, lakukan impor dump satu kali pada database Laravel yang dituju. Cadangkan database sebelum mengimpor. Jangan memasukkan dump ke folder publik atau menyalin database demo pratinjau ke produksi.

Impor satu kali membaca data dari dump tanpa menjalankan pernyataan SQL di dalamnya. `--all` memasukkan berita, pengumuman, artikel, dokumentasi, dan gambar Smart Slider 3 jika `WORDPRESS_SLIDER_ID=3`. Impor ulang menambah data atau rujukan yang belum ada tanpa menimpa berita atau membuat duplikat.

```powershell
php artisan migrate
php artisan wordpress:import-news "C:\lokasi\backup-wordpress.sql" --all
php artisan wordpress:import-news "C:\lokasi\backup-wordpress.sql" --all --apply --expect-database=NAMA_DATABASE_LARAVEL
```

Perintah kedua adalah pratinjau tanpa perubahan data. Setelah migrasi, periksa URL gambar dan PDF tanpa login, lalu buka detail berita lama untuk menguji Dokumentasi dan lightbox. Tidak perlu menjalankan sinkronisasi atau cron WordPress.

**Verifikasi pratinjau:** kedua migrasi tahap ini dijalankan secara aditif pada `web_pta_preview`. Dump database pratinjau berhasil dipulihkan ke database uji terpisah, dan jumlah rekaman halaman, berita, dokumen, jadwal, statistik, survei, serta riwayat migrasi cocok. Pemulihan media dan konfigurasi server produksi belum diuji.
