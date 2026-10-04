# DB_Apotek_Kelompok4
Sistem Informasi Manajemen Apotek berbasis Web dengan PHP Native (OOP &amp; MVC) dan PostgreSQL. Dilengkapi fitur pengelolaan obat, transaksi kasir, serta layanan self-checkout untuk pembeli.

# Sistem Informasi Manajemen Apotek

Sistem Informasi Manajemen Apotek adalah aplikasi berbasis web yang dirancang untuk mengelola operasional penjualan obat, transaksi kasir, inventaris stok, serta layanan belanja mandiri (*self-checkout*) bagi pelanggan.

## Fitur Utama

### Role Apoteker / Admin
* **Manajemen Data Obat (CRUD & Stok):** Menambah, memperbarui, melihat daftar obat, serta pemantauan stok obat secara otomatis
* **Manajemen Data Pembeli & Member:** Mengelola data pelanggan umum maupun member untuk keperluan pencatatan transaksi
* **Manajemen Karyawan / Staf:** Pengelolaan data akun apoteker, registrasi akun staf baru, dan pembaruan kredensial login
* **Sistem Transaksi Kasir:** 
  * Pembuatan transaksi baru untuk kategori pembeli Umum maupun Member
  * Penambahan item obat ke transaksi beserta validasi ketersediaan stok
  * Kalkulasi total bayar, pembayaran, dan kembalian otomatis
* **Keamanan & Autentikasi:** Autentikasi sesi berbasis role (`Auth::checkApoteker()`) dan proteksi query menggunakan *Prepared Statements* SQL untuk mencegah ancaman *SQL Injection*

### Role Pembeli / Pelanggan
* **Registrasi & Login Mandiri:** Pelanggan dapat mendaftarkan akun baru dan login ke dalam sistem
* **Katalog Obat:** Menampilkan daftar obat yang tersedia lengkap dengan harga dan sisa stok
* **Keranjang Belanja & Self-Checkout:** Menambahkan obat ke keranjang dan melakukan transaksi pembayaran secara mandiri.
* **Riwayat & Detail Pembelian:** Menampilkan riwayat transaksi yang telah dibayar beserta rincian item obat yang dibeli.

---

## Akun Demo / Login Pertama

Untuk melakukan pengujian atau penggunaan pertama kali, Anda dapat menggunakan kredensial bawaan (*default*) berikut:

* **Apoteker / Admin:**
  * **Username:** `Kanaka`[cite: 2]
  * **Password:** `KanakaPM`[cite: 2]
* **Pembeli / Pelanggan:**
  * **Username:** `budi`[cite: 2]
  * **Password:** `budi123`[cite: 2]
  *(Atau dapat membuat akun baru melalui halaman registrasi pembeli).*

---

## Skema Basis Data (PostgreSQL)

Aplikasi ini menggunakan 6 tabel utama pada database `apotekppl`:
* `tb_karyawan`: Menyimpan informasi profil staf/apoteker.
* `tb_login`: Menyimpan kredensial akun login staf.
* `tb_obat`: Menyimpan data master obat, harga jual/beli, dan stok.
* `tb_pembeli`: Menyimpan data profil pelanggan (pembeli umum/member).
* `tb_transaksi`: Menyimpan header data transaksi penjualan.
* `tb_detail_trx`: Menyimpan rincian item obat pada setiap transaksi.

---

## Struktur Direktori

```text
apotek_ppl/
├── model/                     # Komponen Model (Query & Data Access)
│   ├── Auth.php               # Manajemen Sesi & Autentikasi Role
│   ├── DBconnection.php       # Driver Koneksi PostgreSQL & Prepared Statements
│   ├── Obat.php               # Model Pengelolaan Data Obat
│   ├── Pembeli.php            # Model Pengelolaan Data Pembeli
│   ├── Karyawan.php           # Model Pengelolaan Data Karyawan
│   ├── Login.php              # Model Autentikasi Staf
│   ├── Transaksi.php          # Model Transaksi & Checkout
│   ├── DetailTransaksi.php    # Model Rincian Item Transaksi
│   ├── DatabaseException.php  # Class Handling Exception Database
│   └── Respon.php             # Object Wrapper Response Query
├── view/                      # Komponen View (Tampilan Antarmuka)
│   ├── dashboard.php          # Main Frame Dashboard Apoteker
│   ├── dashboard_pembeli.php  # Main Frame Dashboard Pembeli
│   ├── view_obat.php          # Tabel Daftar Obat
│   ├── view_pembeli.php       # Tabel Daftar Pembeli
│   ├── view_karyawan.php      # Tabel Daftar Karyawan
│   ├── view_transaksi.php     # Tabel Daftar Transaksi
│   ├── view_detail_transaksi.php # Detail Transaksi Kasir
│   ├── pembeli_katalog.php    # Katalog Obat Pembeli
│   ├── pembeli_keranjang.php  # Keranjang Belanja Pembeli
│   ├── pembeli_riwayat.php    # Riwayat Pembelian
│   └── register.php           # Form Registrasi Staf/Apoteker
├── tambah/                    # Controller Handling Tambah Data
│   ├── tambah_obat.php
│   ├── tambah_pembeli.php
│   ├── tambah_transaksi.php
│   └── proses_tambah_transaksi.php
├── update/                    # Controller Handling Update Data
│   ├── update_obat.php
│   ├── update_pembeli.php
│   └── update_karyawan.php
├── style/                     # Asset CSS Kustom
├── script/                    # Asset JavaScript
├── koneksi.php                # Instansiasi Koneksi Database Global
├── login.php                  # Halaman Login Utama
├── register_pembeli.php       # Form Registrasi Mandiri Pembeli
└── logout.php                 # Handler Logout Session
```
