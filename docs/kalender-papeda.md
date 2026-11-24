# Agenda Pimpinan dan Kalender Papeda

Beranda mengambil berita terbit kategori **Pimpinan** atau **Agenda Pimpinan** langsung dari database WordPress. Kalender bulanan mengikuti jadwal yang dipublikasikan dari Kalender Terpadu Papeda (Super3).

## Aktivasi

1. Rilis perubahan Super3 dan website PTA, termasuk ke subdomain tes. Cadangkan database, lalu jalankan `php artisan migrate --force` di masing-masing proyek dengan versi PHP yang sesuai. Jangan gunakan `migrate:fresh`.
2. Di `.env` website PTA, isi `WORDPRESS_DB_HOST`, `WORDPRESS_DB_DATABASE`, `WORDPRESS_DB_USERNAME`, dan `WORDPRESS_DB_PASSWORD` dengan akun WordPress yang hanya punya izin SELECT. Pakai `WORDPRESS_DB_PREFIX` sesuai tabel WordPress. `WORDPRESS_MEDIA_URL` tetap menunjuk ke folder unggahan lama.
3. Atur `PAPEDA_PUBLIC_CALENDAR_URL=https://papeda.pta-papuabarat.go.id/api/kalender-publik` di `.env` website PTA. Gunakan hostname Papeda yang aktif, lalu jalankan `php artisan config:cache` setelah konfigurasi selesai.
4. Di Papeda, administrator membuka **Kalender Terpadu**, memilih kegiatan dari modul mana pun, mengisi **Judul untuk kalender publik**, lalu menekan **Simpan publikasi**. Kosongkan judul dan simpan untuk menarik publikasi.

Berita WordPress tidak diimpor atau disalin. Beranda, arsip Agenda Pimpinan, dan halaman detail membaca database sumber. Jika koneksi WordPress tidak tersedia, beranda dan arsip memakai berita Agenda Pimpinan lokal sebagai cadangan.

Papeda hanya mengirim judul yang disetujui, tanggal, dan waktu WIT. Semua modul bisa dipilih, tetapi kegiatan internal tidak dipublikasikan otomatis. Publikasi Agenda Pimpinan yang sudah ada tetap berlaku. Perubahan jadwal terlihat setelah cache lima menit kedaluwarsa.
