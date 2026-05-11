# AUDIT DFD BALANCING

## Entitas Luar
- Admin
- Pegawai

## Proses Level 0
- P1 Autentikasi dan Profil
- P2 Kelola Pengguna
- P3 Kelola Produk dan Harga
- P4 Kelola Template dan Mode Limit
- P5 Proses Transaksi Penjualan
- P6 Dashboard, Laporan, dan Grafik

## Mapping Parent-Child Level 1
- P1 -> Level 1 P1: validasi login dan pembaruan profil
- P2 -> Level 1 P2: simpan dan tampilkan data pengguna
- P3 -> Level 1 P3: simpan/tampilkan produk dan harga
- P4 -> Level 1 P4: simpan/tampilkan template serta limit penjualan
- P5 -> Level 1 P5: terima, validasi, dan simpan transaksi
- P6 -> Level 1 P6: susun laporan, grafik, dan dashboard

## Rename Arus Data Lama ke Standar Final
- informasi produk -> info_produk
- informasi harga -> info_harga_produk
- data limit -> data_limit_penjualan
- informasi laporan -> info_laporan_penjualan
- informasi dashboard -> info_dashboard_admin / info_dashboard_pegawai

## Tabel Balancing Data Store

| Database | Input (data_) | Output (info_) | Balance |
|---|---|---|---|
| D1 users | data_profil_akun (×1), data_pengguna (×1) | info_akun (×1), info_pengguna (×1) | Seimbang |
| D2 products | data_produk (×1) | info_produk (×1) | Seimbang |
| D3 product_prices | data_harga_produk (×1) | info_harga_produk (×1) | Seimbang |
| D4 quick_templates | data_template (×1) | info_template (×1) | Seimbang |
| D5 quick_template_items | data_item_template (×1) | info_item_template (×1) | Seimbang |
| D6 sale_limit_settings | data_limit_penjualan (×1) | info_limit_penjualan (×1) | Seimbang |
| D7 sales_transactions | data_transaksi (×1) | info_transaksi (×1) | Seimbang |
| D8 sales_transaction_items | data_detail_transaksi (×1) | info_detail_transaksi (×1) | Seimbang |

## Konsistensi Antar Diagram
- Diagram konteks selaras dengan DFD Level 0 karena seluruh alur utama admin dan pegawai dipertahankan.
- DFD Level 0 selaras dengan seluruh DFD Level 1 karena setiap parent process memiliki child diagram tersendiri.
- Data store pada Level 0 dijaga seimbang secara count 1:1.

## Hal yang Direvisi
- Akses laporan dan grafik ditempatkan pada proses P6 agar tetap konsisten dengan controller Reports dan Charts.
- Akses data pendukung transaksi dialirkan sebagai arus antar proses dari P3 dan P4 ke P5 untuk menjaga disiplin balancing store.
- Proses dashboard dipisahkan dari autentikasi agar tidak mencampur fungsi login dengan pelaporan operasional.