# AUDIT PROGRAM

## Domain Sistem
- sistem informasi penjualan beras berbasis web untuk administrasi penjualan internal
- Studi kasus: UD Tulus Sari Merta

## Role Nyata
- admin
- pegawai

## Modul Nyata
### Halaman Publik
- login

### Modul Auth dan Profil
- login/logout berbasis session
- ubah profil, ubah username, upload foto profil, ubah password

### Modul Admin
- dashboard admin
- kelola pengguna
- kelola produk
- kelola harga
- kelola template transaksi cepat
- mode pembatasan penjualan
- laporan penjualan
- grafik penjualan

### Modul Pegawai
- dashboard pegawai
- transaksi manual
- transaksi template cepat
- riwayat transaksi hari berjalan
- profil

## Fitur Nyata
- kelola pengguna
- kelola produk beras
- kelola harga beras
- kelola template transaksi cepat
- kelola mode pembatasan penjualan
- transaksi manual berdasarkan qty 5 kg, 10 kg, dan 25 kg
- transaksi berbasis template cepat
- riwayat transaksi
- laporan penjualan dengan filter tanggal, produk, dan pencatat
- grafik penjualan harian dan bulanan
- ubah nama lengkap
- ubah username
- upload foto profil
- ubah password

## Tabel Nyata
- users: menyimpan akun admin dan pegawai
- products: menyimpan master produk beras
- product_prices: menyimpan histori dan harga aktif produk
- quick_templates: menyimpan header template transaksi cepat
- quick_template_items: menyimpan detail produk per template
- sale_limit_settings: menyimpan konfigurasi mode pembatasan penjualan
- sales_transactions: menyimpan header transaksi penjualan
- sales_transaction_items: menyimpan detail item transaksi dan snapshot harga

## Relasi Utama
- users -> product_prices (1:N, fk: updated_by)
- users -> quick_templates (1:N, fk: created_by)
- users -> sale_limit_settings (1:N, fk: updated_by)
- users -> sales_transactions (1:N, fk: created_by)
- products -> product_prices (1:N, fk: product_id)
- products -> quick_template_items (1:N, fk: product_id)
- products -> sales_transaction_items (1:N, fk: product_id)
- quick_templates -> quick_template_items (1:N, fk: template_id)
- quick_templates -> sales_transactions (1:N, fk: template_id)
- sales_transactions -> sales_transaction_items (1:N, fk: transaction_id)

## Alur Bisnis Utama
1. Pengguna login ke sistem.
2. Sistem memvalidasi akun aktif berdasarkan tabel users.
3. Admin menyiapkan data master produk, harga, template, pengguna, dan limit penjualan.
4. Admin atau pegawai melakukan input transaksi manual atau dari template.
5. Sistem mengambil harga aktif, menghitung subtotal, total kg, grand total, dan memvalidasi limit penjualan.
6. Sistem menyimpan header transaksi dan detail transaksi dengan snapshot harga.
7. Dashboard, laporan, dan grafik mengambil data transaksi yang telah tersimpan.

## Catatan Integrasi atau Otomatisasi
- Nomor invoice digenerate otomatis oleh service transaksi.
- Grafik dibentuk dengan Chart.js dari agregasi penjualan harian dan bulanan.
- Folder `/contoh` berisi acuan format BAB 4, terutama pada dokumen desain DFD, perancangan basis data, dan pola penulisan 4.4 serta 4.5.
