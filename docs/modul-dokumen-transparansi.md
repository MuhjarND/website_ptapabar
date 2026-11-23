# Modul Dokumen Transparansi

Modul ini mengisi halaman Transparansi yang sudah ada. Tidak ada katalog unduhan atau menu publik baru. Untuk daftar sumber dan data yang masih kurang, lihat [inventaris dokumen SINOVA](inventaris-dokumen-sinova.md).

## Aktivasi di staging

1. Cadangkan database staging, pastikan `php artisan env` dan koneksi database yang aktif bukan lingkungan atau database produksi, lalu jalankan migrasi modul saja:

   ```powershell
   php artisan migrate --path=database/migrations/2026_11_19_000001_create_transparency_documents_table.php
   ```

   Migrasi hanya membuat tabel `transparency_documents` dan membutuhkan tabel `pages` yang sudah ada. Jangan gunakan `migrate:fresh`. Jangan rollback tabel ini jika sudah berisi dokumen, kecuali ada prosedur pemulihan yang disetujui.
2. Arahkan document root web server ke folder Laravel `public`, bukan root proyek. Apache dengan `AllowOverride` yang mengizinkan aturan akses juga mendapat lapisan tambahan dari `storage/app/transparency-documents/.htaccess`. Aturan tersebut tidak berlaku untuk Nginx. Pastikan URL langsung ke folder penyimpanan privat ditolak dan unduhan resmi membaca berkas melalui aplikasi.
3. Akses **Admin > Dokumen Transparansi**. Tambahkan dokumen dan pilih **Renstra** atau **DIPA** pada kategori. Jika salah satu tidak ada, periksa kelompok menu Transparansi di admin Halaman; jangan jalankan ulang seeder utama hanya untuk menambahnya.
4. Masukkan tahun, periode dan versi yang tertera pada berkas, kode anggaran bila relevan, serta catatan sumber internal. Pilih satu sumber: PDF lokal sampai 10 MB atau tautan HTTPS. Batas PHP/web server perlu mengizinkan unggahan tersebut. Gunakan berkas atau tautan yang disetujui unit pemilik, bukan konten dummy.
5. Simpan draf, pilih **Cek Sumber**, lalu **Terbitkan**. Buka halaman Renstra/DIPA tanpa login untuk menguji daftar dan unduhan. **Tarik ke Draf** menyembunyikan entri tanpa menghapus dokumen. Setiap edit mengembalikan entri ke draf. Tautan eksternal yang sudah publik di server asal tidak bisa dicabut dari server tersebut melalui modul ini.

PDF unggahan disimpan di `storage/app/transparency-documents`, di luar disk Laravel `public`. Sertakan folder tersebut dalam backup. Jika halaman tujuan dihapus, dokumen dipertahankan dan tidak dapat diakses publik sampai dipindahkan ke halaman Transparansi yang aktif dan diterbitkan kembali.

## Pengujian lokal

```powershell
php vendor/phpunit/phpunit/phpunit --do-not-cache-result --filter TransparencyDocumentTest
```

Tes modul memakai SQLite in-memory, bukan database lokal atau produksi.
