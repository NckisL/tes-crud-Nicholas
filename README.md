# Inventaris Barang Gudang (CRUD)

## Versi PHP
PHP 5.6.

## Fitur
- CRUD barang (tambah, ubah, hapus dengan konfirmasi JavaScript)
- Tampil data barang dengan query JOIN dan pencarian langsung
- Transaksi barang masuk/keluar (stok otomatis bertambah/berkurang)
- Daftar transaksi dengan filter rentang tanggal dan rekap
- Validasi input dan aman dari SQL Injection

## Struktur Database
Database: `inventory`

| Tabel | Keterangan |
|---|---|
| `category` | Master kategori (`cat_id` PK, `cat_name` unik) |
| `item` | Data barang (`item_id` PK, `item_cat_id` FK ke `category`) |
| `transactions` | Riwayat masuk/keluar (`trx_item_id` FK ke `item`, `trx_status`: Y = masuk, N = keluar) |

## Struktur File
| File | Fungsi |
|---|---|
| `koneksi.php` | Koneksi database dan fungsi `create_id()` |
| `index.php` | Daftar barang, New / Edit / Delete, pencarian |
| `tambah.php` | Form tambah barang |
| `simpan.php` | Proses simpan (tambah dan edit) |
| `edit.php` | Form ubah barang |
| `hapus.php` | Hapus barang |
| `transaksi.php` | Daftar transaksi, filter tanggal, rekap |
| `transaksi_tambah.php` | Form transaksi |
| `transaksi_simpan.php` | Proses transaksi dan update stok |
| `inventory.sql` | Export database |

## Alur Penggunaan
1. **Kelola barang** di `index.php`: klik **New** untuk tambah, klik sebuah baris lalu **Edit** atau **Delete** (ada konfirmasi dulu).
2. Ketik di kolom pencarian untuk memfilter barang secara langsung.
3. Klik tombol **Transaksi** untuk membuka `transaksi.php`.
4. Klik **+ Transaksi Baru**, pilih barang, jenis (masuk/keluar), jumlah, dan tanggal, lalu **Save**. Stok barang otomatis berubah.
5. Di `transaksi.php`, gunakan filter **Dari / Sampai** untuk menyaring riwayat. Rekap di bawah tabel ikut menyesuaikan.