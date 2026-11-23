# Klinik (modul Rawat Jalan, CakePHP 5)

Hasil pemangkasan SIMRS: hanya pendaftaran, rawat jalan, rekam medis, farmasi, penunjang (lab/radiologi), kasir, master data, dan user.

## Pasang
1. `composer install`
2. Salin folder `webroot/` (css, js, plugins, img, fonts) dari proyek SIMRS lama ke `webroot/`. Zip kode tidak menyertakan aset.
3. Impor database: `mysql < db_klinik.sql` (membuat `db_klinik`, struktur saja + 1 user).
4. Atur `config/app_local.php` (kredensial DB, `Security.salt`).
5. Login: code `admin`, password `admin123`. Segera ganti.

Daftar modul, menu sidebar, dan hak akses ada di `config/klinik_modules.php`.
