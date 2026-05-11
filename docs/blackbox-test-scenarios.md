# Skenario Pengujian Black Box

## 1. Login Valid
- Input username dan password admin valid.
- Ekspektasi: sistem login berhasil dan masuk ke dashboard.

## 2. Login Invalid
- Input username valid dengan password salah.
- Ekspektasi: sistem menolak login dan menampilkan pesan error.

## 3. Hak Akses Admin vs Pegawai
- Login sebagai pegawai lalu akses URL manajemen user.
- Ekspektasi: pegawai ditolak dan diarahkan kembali.

## 4. CRUD Pengguna
- Admin menambah user pegawai baru.
- Admin mengedit data user.
- Admin menghapus user yang bukan admin terakhir.
- Ekspektasi: semua aksi berhasil sesuai validasi.

## 5. Validasi Admin Terakhir
- Admin mencoba mengubah satu-satunya admin aktif menjadi pegawai atau nonaktif.
- Ekspektasi: sistem menolak perubahan.

## 6. CRUD Produk
- Admin menambah produk 5/10/25 kg.
- Admin mengedit produk.
- Admin menghapus produk.
- Ekspektasi: data produk tersimpan dan tampil benar.

## 7. Update Harga
- Admin mengubah harga produk.
- Ekspektasi: harga aktif berubah, riwayat harga bertambah.

## 8. Buat Transaksi Manual
- Pegawai memilih produk dan qty manual lalu simpan.
- Ekspektasi: transaksi berhasil tersimpan dengan invoice unik.

## 9. Buat Transaksi dengan Template Cepat
- Pegawai memilih template cepat.
- Ekspektasi: item otomatis terisi dan total otomatis dihitung.

## 10. Edit Manual Setelah Memilih Template
- Pegawai mengubah qty setelah template dipilih.
- Ekspektasi: subtotal, total kg, dan grand total ikut berubah otomatis.

## 11. Validasi Limit Pembelian Aktif
- Admin aktifkan mode limit dan isi batas 20 kg.
- Pegawai input transaksi 25 kg.
- Ekspektasi: transaksi ditolak saat simpan dengan pesan jelas.

## 12. Validasi Limit Pembelian Nonaktif
- Admin nonaktifkan mode limit.
- Pegawai input transaksi di atas 20 kg.
- Ekspektasi: transaksi tetap bisa disimpan.

## 13. Laporan Penjualan
- Admin buka laporan dan filter berdasarkan rentang tanggal.
- Ekspektasi: tabel laporan dan ringkasan hanya menampilkan data sesuai filter.

## 14. Filter Produk pada Laporan
- Admin pilih satu produk pada filter laporan.
- Ekspektasi: transaksi yang ditampilkan hanya yang mengandung produk tersebut.

## 15. Grafik Tampil
- Admin buka dashboard/laporan.
- Ekspektasi: grafik penjualan harian dan bulanan tampil tanpa error.

## 16. Validasi Input Kosong
- User submit form login/produk/transaksi dengan field wajib kosong.
- Ekspektasi: sistem menolak dan menampilkan pesan validasi.

## 17. Validasi Format Salah
- Admin input harga negatif atau tanggal tidak valid.
- Ekspektasi: sistem menolak penyimpanan.

## 18. Profil dan Ubah Password
- User mengubah profil dan password dengan password lama benar.
- Ekspektasi: profil diperbarui dan password baru bisa dipakai login.
