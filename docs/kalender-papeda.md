# Agenda Pimpinan dan Kalender Papeda

Beranda mengambil berita terbit kategori **Pimpinan** atau **Agenda Pimpinan** langsung dari database WordPress. Kalender bulanan mengikuti jadwal yang dipublikasikan dari Kalender Terpadu Papeda (Super3).

## Aktivasi

1. Rilis perubahan Super3 dan website PTA, termasuk ke subdomain tes. Cadangkan database, lalu jalankan `php artisan migrate --force` di masing-masing proyek dengan versi PHP yang sesuai. Jangan gunakan `migrate:fresh`.
2. Di `.env` website PTA, isi `WORDPRESS_DB_HOST`, `WORDPRESS_DB_DATABASE`, `WORDPRESS_DB_USERNAME`, dan `WORDPRESS_DB_PASSWORD` dengan akun WordPress yang hanya punya izin SELECT. Pakai `WORDPRESS_DB_PREFIX` sesuai tabel WordPress. `WORDPRESS_MEDIA_URL` tetap menunjuk ke folder unggahan lama.
3. Atur `PAPEDA_PUBLIC_CALENDAR_URL=https://papeda.pta-papuabarat.go.id/api/kalender-publik` di `.env` website PTA. Gunakan hostname Papeda yang aktif, lalu jalankan `php artisan config:cache` setelah konfigurasi selesai.
4. Di Papeda, administrator membuka **Kalender Terpadu**, memilih kegiatan dari modul mana pun, mengisi **Judul untuk kalender publik**, lalu menekan **Simpan publikasi**. Kosongkan judul dan simpan untuk menarik publikasi.

Berita WordPress tidak diimpor atau disalin. Beranda, arsip Agenda Pimpinan, dan halaman detail membaca database sumber. Jika koneksi WordPress tidak tersedia, beranda dan arsip memakai berita Agenda Pimpinan lokal sebagai cadangan.

Papeda hanya mengirim judul yang disetujui, tanggal, dan waktu WIT. Semua modul bisa dipilih, tetapi kegiatan internal tidak dipublikasikan otomatis. Publikasi Agenda Pimpinan yang sudah ada tetap berlaku. Perubahan jadwal terlihat setelah cache lima menit kedaluwarsa.

Untuk publikasi massal, administrator dapat memilih rentang atau filter di Kalender Terpadu, lalu klik **Publikasikan kegiatan yang tampil**. Tinjau daftar judul asli yang sudah tercentang dan klik **Simpan publikasi terpilih**. Rapat, Agenda Pimpinan, Virtual Meeting, dan Progress ZI dapat dipilih. Cuti, surat internal, dan kegiatan yang sudah memiliki judul publik tidak disertakan. Gunakan publikasi satu per satu jika perlu memakai judul yang berbeda.

## Hostname dan contoh konfigurasi

Hostname Papeda adalah nama domain di bilah alamat, tanpa `https://` atau path: `papeda.pta-papuabarat.go.id`. Gunakan URL penuh untuk API kalender. Untuk `WORDPRESS_DB_HOST`, lihat `DB_HOST` di `wp-config.php` WordPress lama atau cek panel hosting; ini adalah alamat server MySQL, bukan hostname Papeda.

Contoh `.env` website PTA jika MySQL WordPress berada pada server hosting yang sama:

```dotenv
PAPEDA_PUBLIC_CALENDAR_URL=https://papeda.pta-papuabarat.go.id/api/kalender-publik

WORDPRESS_DB_HOST=localhost
WORDPRESS_DB_PORT=3306
WORDPRESS_DB_DATABASE=u1483703_dbptapapuabarat
WORDPRESS_DB_USERNAME=u1483703_wp_read
WORDPRESS_DB_PASSWORD="GANTI_DENGAN_PASSWORD_AKUN_BACA"
WORDPRESS_DB_PREFIX=wp_
WORDPRESS_MEDIA_URL=https://pta-papuabarat.go.id/wp-content/uploads
```

Gunakan nama database sesuai panel hosting. Contoh nama database di atas berasal dari dump WordPress; nama pengguna hanya contoh, bukan akun yang sudah dibuat. Beri akun baru izin SELECT saja. Jangan ubah pengaturan `DB_*` yang dipakai database Laravel. Pada hosting berbeda, gunakan alamat MySQL dan akses jaringan yang disediakan penyedia hosting.

## Jika kalender tidak muncul

- Login di Papeda, lalu buka `/kalender-terpadu` langsung. Jika terbuka tetapi menu tidak terlihat, cek nama modul di **Pengaturan Modul** dan pastikan versi terbaru sudah dirilis. Untuk pengguna selain admin, cek juga status **Published** serta visibilitas desktop dan mobile.
- Jika halaman menampilkan 403, cek role akun Papeda di **Administrasi Pengguna**. Role `admin` dan `super_admin` dikenali oleh Super3. Jika malah diarahkan ke login, masuk kembali di domain Papeda tersebut.
- Jika `/api/kalender-publik` menampilkan 500, cek pesan terbaru di `storage/logs/laravel.log` milik Super3 dan jalankan `php artisan migrate:status`. Pesan `Unknown column calendar_public_title` berarti database aktif belum memiliki kolom itu. Pastikan Artisan dan website memakai database Super3 yang sama; setelah pencadangan, jalankan migrasi biasa jika migrasi kolom atau tabel `calendar_publications` masih pending. Gunakan `APP_DEBUG=false` di hosting agar detail SQL tidak ditampilkan ke publik.
- API yang sehat mengembalikan HTTP 200 dan JSON dengan `events`, misalnya `{"events":[]}` jika belum ada kegiatan publik pada bulan itu. API baca ini tidak memerlukan token.
- Jika API berisi kegiatan tetapi web PTA tidak menampilkannya, periksa bulan yang sama dan `PAPEDA_PUBLIC_CALENDAR_URL` di `.env` **website PTA**, bukan `.env` Papeda. Bangun ulang cache dengan `php artisan config:cache` menggunakan PHP 8.5 yang dipakai website PTA. Kalender menyimpan respons sukses selama lima menit.
- Jika masih kosong, cari `PAPEDA public calendar` di `storage/logs` website PTA. Kode cURL `28` berarti waktu koneksi habis; `60` berarti verifikasi sertifikat gagal. Minta penyedia hosting memeriksa akses HTTPS keluar dan CA bundle PHP atau rantai sertifikat Papeda. Jangan menonaktifkan verifikasi SSL.
