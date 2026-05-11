# Sistem Informasi Penjualan Beras Berbasis Website

Studi kasus: **UD Tulus Sari Merta**

Project ini dibuat menggunakan **PHP + CodeIgniter 4 + MySQL + Bootstrap** dan dirancang agar tetap ketat mengikuti ruang lingkup proposal skripsi.

## 1. Requirement

### Software
- PHP 8.2 atau lebih baru
- Composer
- MySQL / MariaDB
- XAMPP disarankan untuk development lokal
- Visual Studio Code

### Stack yang Digunakan
- Backend: PHP + CodeIgniter 4
- Database: MySQL
- Frontend: HTML, CSS, JavaScript
- UI: Bootstrap 5
- Grafik: Chart.js

## 2. Fitur Utama
- Login/logout dengan session.
- Dashboard admin dan pegawai.
- CRUD pengguna.
- CRUD produk beras.
- Pengaturan harga produk.
- Transaksi penjualan multi-item.
- Perhitungan otomatis subtotal, total kg, grand total.
- Template transaksi cepat.
- Mode pembatasan penjualan berdasarkan maksimum kg per transaksi.
- Riwayat transaksi.
- Laporan penjualan.
- Grafik penjualan.
- Profil dan ubah password.

## 3. Batasan Scope yang Dipatuhi

Fitur berikut **sengaja tidak dibuat** karena di luar proposal:
- Manajemen stok gabah.
- Produksi / penggilingan.
- Payment gateway / pembayaran digital.
- Notifikasi email / SMS.
- Marketplace / customer checkout publik.
- Pengiriman / ongkir / kurir.
- Multi-cabang / multi-gudang.
- Customer portal / member area.
- Akuntansi penuh / laba rugi.

## 4. Struktur Folder Inti

```text
sistem-penjualan-beras/
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Database/
│   │   ├── Migrations/
│   │   └── Seeds/
│   ├── Filters/
│   ├── Helpers/
│   ├── Libraries/
│   ├── Models/
│   └── Views/
├── database_sql/
├── docs/
├── public/
│   └── assets/
└── spark
```

## 5. Cara Setup di XAMPP

### Langkah 1 - Siapkan File Environment
Copy file `env` menjadi `.env`, lalu ubah konfigurasi utama berikut:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = sistem_penjualan_beras
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Jika memakai Apache XAMPP langsung ke folder `public`, sesuaikan `app.baseURL`, misalnya:

```ini
app.baseURL = 'http://localhost/sistem-penjualan-beras/public/'
```

### Langkah 2 - Buat Database
Buka phpMyAdmin dan buat database:

```sql
CREATE DATABASE sistem_penjualan_beras CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

## 6. Migration dan Seeder

Jalankan perintah berikut dari terminal VS Code pada root project:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Seeder akan membuat:
- akun admin
- akun pegawai
- produk dummy 5 kg, 10 kg, dan 25 kg
- harga awal produk
- template transaksi cepat
- setting limit penjualan default
- contoh data transaksi untuk dashboard dan laporan

## 7. SQL Alternatif Jika Tidak Memakai Migration

Jika migration tidak dipakai, gunakan file berikut:

```text
database_sql/penjualan_beras.sql
```

Import melalui phpMyAdmin atau MySQL client. File SQL ini juga sudah berisi data demo dasar.

## 8. Menjalankan Project

### Opsi A - Server Bawaan CodeIgniter
```bash
php spark serve
```

Buka di browser:

```text
http://localhost:8080
```

### Opsi B - XAMPP Apache
- Nyalakan Apache dan MySQL pada XAMPP.
- Arahkan document root ke folder `public`, atau akses URL melalui `/public`.

## 9. Akun Default Demo

### Admin
- Username: `admin`
- Password: `admin12345`

### Pegawai
- Username: `pegawai`
- Password: `pegawai12345`

## 10. Penjelasan Modul
- **Autentikasi**: login/logout dan session.
- **Dashboard**: ringkasan data dan grafik.
- **Kelola Pengguna**: CRUD user admin/pegawai.
- **Kelola Produk**: CRUD produk beras 5/10/25 kg.
- **Kelola Harga**: update harga aktif dan histori harga.
- **Template Cepat**: paket transaksi default.
- **Mode Limit**: pembatasan maksimum kilogram per transaksi.
- **Transaksi**: input transaksi manual atau dari template.
- **Laporan**: filter transaksi, ringkasan, dan grafik.
- **Grafik Penjualan**: halaman khusus visualisasi grafik harian, bulanan, dan tren sederhana.
- **Profil**: ubah data akun dan password.

## 11. Keputusan Desain Penting
- Harga transaksi disimpan sebagai **snapshot** di detail transaksi agar histori tidak berubah walaupun harga aktif diubah kemudian.
- Template transaksi menyimpan kombinasi item dan qty default, sedangkan estimasi total dihitung **dinamis** dari harga aktif saat ini.
- Hak akses hanya ada **2 role**: `admin` dan `pegawai`.

## 12. Dokumentasi Tambahan
- Arsitektur dan desain: `docs/architecture.md`
- Mapping fitur ke proposal: `docs/feature-mapping.md`
- Black-box testing: `docs/blackbox-test-scenarios.md`
- Template SUS: `docs/sus-template.md`

## 13. Catatan Pengembangan
Project ini sengaja dibuat sederhana, stabil, mudah dijelaskan saat sidang, dan tidak keluar dari ruang lingkup proposal.

## 14. Penjelasan Grafik Penjualan

Bagian ini bisa dipakai untuk menjawab pertanyaan dosen tentang batas grafik pada dashboard dan laporan.

### Grafik Penjualan Harian

- Grafik harian menampilkan total penjualan per hari untuk 7 hari terakhir pada dashboard dan 14 hari terakhir pada laporan.
- Nilai sumbu Y, misalnya Rp 450.000, bukan limit transaksi dan bukan batas maksimal penjualan.
- Sumbu Y memakai skala otomatis dari Chart.js berdasarkan nilai terbesar pada data yang sedang tampil.
- Jika data terbesar Rp 450.000, maka grafik akan menyesuaikan skala sekitar angka tersebut.
- Jika nanti ada transaksi Rp 1.000.000, Rp 5.000.000, atau lebih besar, grafik tetap bisa naik mengikuti data tersebut.
- Jadi, batas nominal grafik tidak dikunci di Rp 450.000 atau Rp 1.000.000.

### Grafik Penjualan Bulanan

- Grafik bulanan menampilkan perbandingan 6 bulan terakhir sampai bulan transaksi terbaru yang tersimpan di database.
- Bulan dan tahun pada grafik tidak dibatasi hanya sampai tahun tertentu.
- Jika data transaksi dibuat sampai tahun 2027, grafik dapat menampilkan bulan di tahun 2027.
- Jika data transaksi dibuat sampai tahun setelahnya, grafik juga tetap mengikuti data transaksi terbaru.
- Yang dibatasi hanya jumlah bulan yang ditampilkan, yaitu 6 bulan terakhir agar dashboard tetap ringkas dan mudah dibaca.

Kesimpulan singkat untuk bimbingan: grafik tidak membatasi jumlah penjualan dan tidak membatasi tahun data. Grafik hanya mengambil rentang data tertentu agar tampilannya rapi, sedangkan nilai Y dan tahun mengikuti isi transaksi yang tersimpan di database.

## 15. Tutorial Git Clone, Pull, dan Menjalankan Project

Repository GitHub:

```text
https://github.com/ryuken25/sistem-penjualan-beras
```

### A. Clone Project Pertama Kali

Jalankan di folder tempat ingin menyimpan project, misalnya folder `htdocs` XAMPP atau folder kerja biasa:

```bash
git clone https://github.com/ryuken25/sistem-penjualan-beras.git
cd sistem-penjualan-beras
```

### B. Install Dependency PHP

Pastikan Composer sudah terinstall, lalu jalankan:

```bash
composer install
```

### C. Siapkan File Environment

Jika file `.env` sudah ikut dari repository, cukup cek isinya dan sesuaikan database lokal. Jika belum ada, copy dari file `env`:

```bash
copy env .env
```

Contoh konfigurasi database lokal:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = sistem_penjualan_beras
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

### D. Buat Database

Buka phpMyAdmin, lalu buat database:

```sql
CREATE DATABASE sistem_penjualan_beras CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### E. Jalankan Migration dan Seeder

Jalankan dari root project:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Jika memilih import manual, gunakan file:

```text
database_sql/penjualan_beras.sql
```

### F. Jalankan Website

Opsi server bawaan CodeIgniter:

```bash
php spark serve
```

Buka browser:

```text
http://localhost:8080
```

Opsi XAMPP Apache:

- Nyalakan Apache dan MySQL.
- Simpan project di folder `htdocs` atau atur document root ke folder `public`.
- Akses melalui browser sesuai lokasi project, misalnya `http://localhost/sistem-penjualan-beras/public/`.

### G. Pull Update Terbaru

Jika project sudah pernah di-clone dan ingin mengambil update terbaru dari GitHub:

```bash
cd sistem-penjualan-beras
git pull origin main
composer install
php spark migrate
```

### H. Akun Login Demo

Admin:

```text
Username: admin
Password: admin12345
```

Pegawai:

```text
Username: pegawai
Password: pegawai12345
```
