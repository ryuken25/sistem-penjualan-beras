# Cara Install Sistem Informasi Penjualan Beras

## 1. Persyaratan
Kebutuhan untuk menjalankan project:
- XAMPP / Apache.
- PHP 8.2 atau lebih baru sesuai `composer.json`.
- MySQL / MariaDB.
- Composer, karena project memakai `composer.json`.
- Browser.
- Visual Studio Code opsional.

## 2. Lokasi Project
Project berada di folder:

`sistem-penjualan-beras`

Jika memakai XAMPP, letakkan di:

`C:/xampp/htdocs/sistem-penjualan-beras`

## 3. Install Dependency
Karena ada `composer.json`, jalankan perintah berikut dari folder `sistem-penjualan-beras`:

```bash
composer install
```

Project membutuhkan PHP `^8.2` dan CodeIgniter Framework `^4.7`.

## 4. Buat Database
Nama database dicek dari `.env` dan file SQL, yaitu:

`sistem_penjualan_beras`

Cara membuat database lewat phpMyAdmin:
- Nyalakan Apache dan MySQL dari XAMPP Control Panel.
- Buka browser.
- Akses `http://localhost/phpmyadmin`.
- Klik menu **Databases**.
- Buat database dengan nama `sistem_penjualan_beras`.
- Gunakan collation `utf8mb4_general_ci` jika tersedia.

## 5. Import Database / Migration
Project memiliki file SQL di:

`database_sql/penjualan_beras.sql`

Jika memakai import SQL:
- Buka phpMyAdmin.
- Pilih database `sistem_penjualan_beras`.
- Klik **Import**.
- Pilih file `database_sql/penjualan_beras.sql`.
- Klik **Go/Kirim**.

Jika memakai migration, jalankan dari folder project:

```bash
php spark migrate
```

Project juga memiliki seeder di `app/Database/Seeds`. Jika diperlukan, jalankan seeder sesuai kebutuhan setelah migration selesai.

## 6. Konfigurasi Database
Konfigurasi utama database berada di `.env`.

Contoh konfigurasi yang sesuai project:

```ini
database.default.hostname = 127.0.0.1
database.default.database = sistem_penjualan_beras
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Jika memakai XAMPP default, username database biasanya `root` dan password dikosongkan.

File `app/Config/Database.php` tetap memakai konfigurasi default CodeIgniter, dan nilai database dioverride oleh `.env`.

## 7. Jalankan Project
Opsi XAMPP:
- Nyalakan Apache dan MySQL.
- Pastikan folder project berada di `C:/xampp/htdocs/sistem-penjualan-beras`.
- Buka browser.
- Akses:

`http://localhost/sistem-penjualan-beras/public`

Opsi Spark:

```bash
php spark serve
```

Lalu akses:

`http://localhost:8080`

Catatan: `.env` project saat audit memakai `app.baseURL = 'http://localhost:8080/'`, sehingga opsi Spark sesuai konfigurasi tersebut.

## 8. Akun Login Awal
Akun login awal ditemukan dari `database_sql/penjualan_beras.sql` dan `app/Database/Seeds/UserSeeder.php`.

Akun admin:
- Nama pengguna: `admin`
- Kata sandi: `admin12345`

Akun pegawai:
- Nama pengguna: `pegawai`
- Kata sandi: `pegawai12345`

## 9. Alur Penggunaan Singkat
Alur penggunaan sistem:
- Login sebagai admin.
- Kelola pengguna.
- Kelola data beras.
- Atur harga beras.
- Atur template transaksi cepat jika tersedia.
- Atur mode pembatasan penjualan jika diperlukan.
- Login sebagai pegawai.
- Input transaksi penjualan.
- Cek laporan dan grafik/statistik penjualan.

## 10. Troubleshooting

### 404 Not Found
- Pastikan URL mengarah ke folder `public`, misalnya `http://localhost/sistem-penjualan-beras/public`.
- Jika memakai Spark, pastikan `php spark serve` masih berjalan.
- Jika memakai virtual host Apache, pastikan rewrite module aktif.

### Database connection error
- Pastikan MySQL/MariaDB menyala.
- Pastikan database `sistem_penjualan_beras` sudah dibuat.
- Cek konfigurasi database di `.env`.
- Pastikan hostname, username, password, driver, dan port database benar.

### Base URL salah
- Cek nilai `app.baseURL` di `.env`.
- Untuk Spark, gunakan `http://localhost:8080/`.
- Untuk XAMPP tanpa virtual host, akses `http://localhost/sistem-penjualan-beras/public`.

### Composer belum terinstall
- Install Composer terlebih dahulu.
- Setelah Composer tersedia, jalankan `composer install` dari folder project.

### Folder writable bermasalah
- Pastikan folder `writable` dapat ditulis oleh server web.
- Folder penting meliputi `writable/cache`, `writable/logs`, dan `writable/session`.

### Import database gagal
- Pastikan database sudah dibuat sebelum import.
- Pastikan file yang diimport adalah `database_sql/penjualan_beras.sql`.
- Jika tabel sudah ada, gunakan database kosong atau hapus tabel lama sebelum import ulang.

### Versi PHP tidak sesuai
- Project membutuhkan PHP 8.2 atau lebih baru.
- Cek versi PHP dengan perintah `php -v`.
- Jika versi PHP terminal berbeda dari XAMPP, sesuaikan PATH atau gunakan PHP dari XAMPP.

## 11. Catatan Batasan Sistem
Sistem hanya mencakup:
- penjualan beras,
- data pengguna admin dan pegawai,
- data produk beras,
- harga beras,
- transaksi,
- laporan/grafik penjualan,
- template transaksi cepat,
- mode pembatasan penjualan.

Sistem tidak mencakup:
- stok gabah,
- produksi/penggilingan,
- pembayaran digital,
- notifikasi email/SMS otomatis.
