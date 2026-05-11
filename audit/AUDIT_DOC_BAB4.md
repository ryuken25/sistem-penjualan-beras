# AUDIT DOC BAB4

## Sumber Isi Tiap Subbab
- BAB 4.1 disusun dari README, route, controller, filter, helper, migration, model, dan view.
- BAB 4.2 disusun dari hasil audit proses bisnis, role, dan tabel yang kemudian dimodelkan menjadi Diagram Konteks, DFD Level 0, dan DFD Level 1.
- BAB 4.3 disusun dari migration utama, model, foreign key, dan service transaksi.
- BAB 4.4 disusun dari halaman view yang benar-benar ada pada folder app/Views.
- BAB 4.5 disusun dari screenshot implementasi nyata yang diambil dari halaman yang berjalan.

## Halaman yang Dipakai dari Program
- auth/login
- dashboard/index
- users/index dan users/form
- products/index dan products/form
- prices/index
- templates/index dan templates/form
- sale_limit/index
- sales/create dan sales/index
- reports/index
- charts/index
- profile/index

## Halaman yang Tidak Dimasukkan karena Tidak Ada di Program
- register publik
- halaman checkout customer
- halaman pembayaran digital
- halaman stok gudang
- halaman produksi/penggilingan
- halaman notifikasi pihak ketiga

## Alasan Penyusunan 4.4 dan 4.5
- Antarmuka dibagi berdasarkan struktur menu admin dan pegawai yang benar-benar tampil pada sidebar.
- Implementasi disusun mengikuti urutan akses yang paling logis: login, dashboard, modul master, modul transaksi, modul laporan, dan profil.
- Struktur penulisan 4.4 dan 4.5 diselaraskan dengan pola contoh pada folder `/contoh`, terutama bentuk subbab, item huruf, dan gaya uraian akademik per halaman.
