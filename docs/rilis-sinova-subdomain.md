# Pemasangan Arsip SINOVA di Subdomain Tes

Paket `pta-sinova-pdf-resmi-2026-11-24.zip` berisi 30 PDF yang sudah dipublikasikan pada website resmi sebelumnya. Tidak ada data dummy, database, atau kredensial di dalam ZIP. Sumber dan SHA-256 tiap berkas ada di `config/sinova-archive.php`. Berkas akan disajikan Laravel dari subdomain yang sama dan tidak memerlukan WordPress, Google Drive, atau `php artisan storage:link` saat dibuka.

## 1. Siapkan hosting

- Cadangkan database dan folder `storage/app` subdomain tes. Pastikan document root adalah `web_baru/public`; jangan taruh ZIP di direktori publik.
- Unggah perubahan aplikasi dan pasang dependensi sesuai `composer.lock` menggunakan PHP 8.5. Jika hosting memerlukan Composer `--no-scripts`, jalankan `php artisan package:discover` sendiri setelahnya.
- Pastikan `APP_URL=https://tes.pta-papuabarat.go.id`, `APP_DEBUG=false`, dan database yang aktif benar-benar database tes. URL laporan survei dibentuk dari `APP_URL` saat impor; jalankan impor terpisah pada tiap lingkungan jika domainnya berbeda.

## 2. Impor dan periksa PDF

Ekstrak ZIP ke folder nonpublik `~/arsip-sinova`. Dari folder proyek, contoh dengan PHP CLI hosting:

```bash
cd ~/public_html/web_baru
/opt/alt/php85/usr/bin/php artisan config:clear
/opt/alt/php85/usr/bin/php artisan migrate:status
/opt/alt/php85/usr/bin/php artisan sinova:import-archive "$HOME/arsip-sinova" --dry-run
/opt/alt/php85/usr/bin/php artisan sinova:import-archive "$HOME/arsip-sinova" --publish
```

Pastikan migrasi tabel dokumen dan kolom laporan survei sudah berstatus **Ran**. Jika belum, jalankan migrasi aditif yang masih tertunda setelah mengecek `migrate:status` dan backup. Jangan jalankan `migrate:fresh`. Tanpa `--publish`, dokumen impor baru menjadi draf. Draf yang sudah ada diterbitkan dari admin bila sudah siap; impor ulang tidak memaksa publikasi ulang. Perintah tidak menimpa berkas berbeda dan tidak mengaktifkan paksa halaman yang dinonaktifkan.

Buka `/halaman/renstra`, `/halaman/dipa`, dan `/hasil-survei-pelayanan` dalam jendela privat. Klik masing-masing satu tautan PDF. Untuk cek teknis, pakai **salah satu URL PDF yang dicetak perintah impor**, bukan URL WordPress lama:

```bash
curl -fsS -D - -r 0-4 'URL_PDF_DARI_HASIL_IMPOR'
```

Hasil yang diharapkan: `206 Partial Content`, `Content-Type: application/pdf`, dan isi `%PDF-`. Akses normal di browser menampilkan PDF langsung. Simpan folder `storage/app/transparency-documents` dalam backup dan migrasikan folder tersebut bersama database bila pindah hosting.

## 3. Aktifkan MFA admin

Setelah cadangan dan instalasi dependensi:

```bash
/opt/alt/php85/usr/bin/php artisan migrate --path=database/migrations/2026_11_24_000001_add_admin_mfa_to_users.php --force
/opt/alt/php85/usr/bin/php artisan admin:mfa EMAIL_ADMIN
```

Tambahkan kunci yang tampil hanya sekali ke aplikasi autentikator secara manual (jenis berbasis waktu, 6 digit, 30 detik). Jangan bagikan kunci di tiket/chat. Setelah itu masuk lewat jendela privat dan pastikan login meminta kode. Ulangi untuk semua akun dengan peran Admin, lalu set `ADMIN_MFA_REQUIRED=true` dan jalankan `artisan config:clear` lagi. Akun Author tidak terdampak. Simpan akses CLI untuk pemulihan: `artisan admin:mfa EMAIL_ADMIN --reset` mengganti kunci dan mencabut verifikasi MFA pada sesi lama. Jangan merotasi `APP_KEY` tanpa rencana pemulihan kunci terenkripsi.
