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
