# Ringkasan Requirement dan Arsitektur

## 1. Scope Final yang Dipatuhi

Project ini dibangun khusus untuk proposal:

**Sistem Informasi Penjualan Beras Berbasis Website – Studi Kasus UD Tulus Sari Merta**

### Aktor Sistem
- **Admin**
- **Pegawai**

### Modul yang Dibangun
- Autentikasi login/logout berbasis session.
- Dashboard admin dan dashboard pegawai.
- CRUD pengguna.
- CRUD produk beras.
- Pengaturan harga produk dengan histori sederhana dan harga aktif.
- Transaksi penjualan dengan banyak item.
- Perhitungan otomatis subtotal, total kilogram, dan grand total.
- Template transaksi cepat.
- Mode pembatasan penjualan berdasarkan total kilogram maksimum per transaksi.
- Riwayat transaksi.
- Laporan penjualan dengan filter.
- Grafik penjualan.
- Profil dan ubah password sederhana.

### Scope yang Sengaja Tidak Dibuat
- Stok gabah, produksi, penggilingan.
- Payment gateway atau pembayaran digital.
- Notifikasi email/SMS.
- Marketplace/customer checkout publik.
- Pengiriman, ongkir, kurir.
- Multi-cabang atau multi-gudang.
- Customer/member portal.
- Akuntansi penuh, laba rugi, pembelian bahan baku.
- Fitur bisnis lain di luar proposal.

## 2. Keputusan Teknis

- Framework: **CodeIgniter 4**.
- Database: **MySQL**.
- UI: **Bootstrap 5** + JavaScript vanilla.
- Chart: **Chart.js** via CDN.
- Password disimpan dengan `password_hash()`.
- Proteksi dasar: CSRF, session auth, role-based authorization, query builder/model, escaping output.
- Harga transaksi disimpan sebagai **snapshot** di detail transaksi agar histori tetap konsisten.
- Template transaksi cepat menyimpan **kombinasi item dan qty default**, sedangkan estimasi total ditampilkan secara **dinamis** dari harga aktif saat ini agar tidak usang ketika harga diperbarui.

## 3. Arsitektur Modul

### Config
- `app/Config/Routes.php` untuk routing eksplisit.
- `app/Config/Filters.php` untuk auth, role, dan CSRF.

### Filters
- `AuthFilter`: memastikan user login.
- `RoleFilter`: membatasi akses admin.

### Controllers
- `AuthController`
- `DashboardController`
- `UsersController`
- `ProductsController`
- `PricesController`
- `QuickTemplatesController`
- `SaleLimitController`
- `SalesController`
- `ReportsController`
- `ProfileController`

### Models
- `UserModel`
- `ProductModel`
- `ProductPriceModel`
- `QuickTemplateModel`
- `QuickTemplateItemModel`
- `SaleLimitSettingModel`
- `SalesTransactionModel`
- `SalesTransactionItemModel`

### Library/Service
- `SaleTransactionService` untuk logika penyimpanan transaksi, perhitungan, invoice, snapshot harga, dan validasi limit.

### Views
- Layout auth.
- Layout dashboard/internal app.
- Halaman per modul sesuai ruang lingkup proposal.

## 4. Struktur Database

### users
- Menyimpan akun admin dan pegawai.
- Soft delete digunakan agar histori transaksi tidak kehilangan referensi logis user.

### products
- Menyimpan produk beras dan berat kemasan.
- Fokus pada kemasan 5 kg, 10 kg, dan 25 kg.

### product_prices
- Menyimpan histori harga.
- Satu harga aktif per produk.

### quick_templates
- Menyimpan header template transaksi cepat.

### quick_template_items
- Menyimpan detail item template dan qty default.

### sale_limit_settings
- Menyimpan konfigurasi mode limit penjualan.
- Disederhanakan menjadi satu konfigurasi aktif yang dapat diperbarui.

### sales_transactions
- Menyimpan header transaksi penjualan.
- Field utama: invoice, tanggal, user pencatat, total item, total kg, grand total, catatan, pelanggan opsional.

### sales_transaction_items
- Menyimpan detail item transaksi.
- Menyimpan snapshot nama produk, berat, dan harga untuk menjaga histori.

## 5. Relasi Utama

- `products.id -> product_prices.product_id`
- `quick_templates.id -> quick_template_items.template_id`
- `products.id -> quick_template_items.product_id`
- `sales_transactions.id -> sales_transaction_items.transaction_id`
- `products.id -> sales_transaction_items.product_id`
- `users.id -> sales_transactions.created_by`
- `users.id -> product_prices.updated_by`
- `users.id -> quick_templates.created_by`
- `users.id -> sale_limit_settings.updated_by`

## 6. Alur Proses Utama

### Login
1. User memasukkan username dan password.
2. Sistem validasi akun aktif.
3. Password diverifikasi.
4. Session dibuat.
5. Redirect ke dashboard sesuai role.

### Input Transaksi
1. User memilih produk manual atau template cepat.
2. Sistem menghitung subtotal item, total kg item, total kg transaksi, dan grand total.
3. Saat simpan, sistem mengambil harga aktif terbaru dari database.
4. Sistem mengecek mode limit penjualan.
5. Jika melampaui batas, transaksi ditolak.
6. Jika valid, transaksi dan detail transaksi disimpan dalam DB transaction.

### Update Harga
1. Admin memilih produk.
2. Admin memasukkan harga baru.
3. Harga lama ditandai tidak aktif.
4. Harga baru disimpan sebagai record baru yang aktif.

## 7. Catatan untuk ERD dan DFD

Struktur relasi dan alur proses sengaja dibuat sederhana dan eksplisit agar mudah diturunkan menjadi:
- ERD tabel inti.
- DFD proses login, kelola master data, transaksi, laporan, dan pengaturan limit.
