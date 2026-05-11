# Mapping Fitur ke Proposal

## A. Autentikasi
- Login: tersedia pada halaman login.
- Logout: tersedia dari sidebar.
- Session management: menggunakan session CodeIgniter.
- Redirect sesuai role: semua user ke dashboard, hak akses dibatasi filter.
- Password di-hash: menggunakan `password_hash()`.

## B. Dashboard
- Dashboard admin: ringkasan transaksi, penjualan hari ini, bulan ini, grafik, status limit, jumlah user/produk/template.
- Dashboard pegawai: ringkasan transaksi operasional, shortcut transaksi, info limit, template cepat.

## C. Manajemen Pengguna
- CRUD user admin/pegawai.
- Field: nama, username, password, role, status aktif.
- Validasi username unik.
- Proteksi admin terakhir agar sistem tidak kehilangan admin aktif.

## D. Produk Beras
- CRUD produk beras.
- Fokus kemasan: 5 kg, 10 kg, 25 kg.
- Tanpa stok, supplier, gudang.

## E. Pengaturan Harga
- Harga dapat diperbarui admin.
- Riwayat harga sederhana tersedia.
- Saat transaksi disimpan, harga dicatat sebagai snapshot pada detail transaksi.

## F. Transaksi Penjualan
- Admin dan pegawai dapat input transaksi.
- Transaksi multi-item.
- Invoice otomatis unik.
- Menyimpan header dan detail transaksi.
- Customer hanya teks opsional untuk pencatatan internal.

## G. Perhitungan Otomatis
- Subtotal item, total kg item, total kg transaksi, grand total dihitung otomatis di form transaksi.

## H. Template Transaksi Cepat
- Admin mengelola template.
- Pegawai/admin memilih template saat input transaksi.
- Item dan qty otomatis terisi namun tetap bisa diedit manual.

## I. Mode Pembatasan Penjualan
- Admin dapat mengaktifkan/nonaktifkan mode limit.
- Batas maksimum total kilogram per transaksi dapat diatur.
- Sistem menolak penyimpanan jika transaksi melebihi batas saat mode aktif.

## J. Laporan Penjualan
- Filter tanggal awal/akhir, produk, pencatat transaksi.
- Tabel laporan dan ringkasan total transaksi, kilogram, dan pendapatan.

## K. Grafik Penjualan
- Grafik penjualan harian dan bulanan menggunakan Chart.js.

## Scope yang Sengaja Tidak Dibuat
- Tidak ada stok, produksi, penggilingan.
- Tidak ada pembayaran digital.
- Tidak ada notifikasi email/SMS.
- Tidak ada customer portal publik.
- Tidak ada kurir, ongkir, pengiriman.
- Tidak ada multi-cabang/multi-gudang.
- Tidak ada akuntansi penuh.
