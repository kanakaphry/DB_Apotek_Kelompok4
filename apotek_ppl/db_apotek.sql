-- Login contoh:  apoteker -> Kanaka / KanakaPM
--                pembeli  -> budi   / budi123

--   - "apoteker": akun di tb_login (terhubung ke tb_karyawan), akses penuh.
--   - "pembeli": registrasi & login sendiri (kolom username/password di
--     tb_pembeli); hanya melihat katalog dan membeli lewat halaman miliknya.

DROP TABLE IF EXISTS tb_detail_trx CASCADE;
DROP TABLE IF EXISTS tb_transaksi CASCADE;
DROP TABLE IF EXISTS tb_obat CASCADE;
DROP TABLE IF EXISTS tb_pembeli CASCADE;
DROP TABLE IF EXISTS tb_login CASCADE;
DROP TABLE IF EXISTS tb_karyawan CASCADE;

-- tabel apoteker
CREATE TABLE tb_karyawan (
    id_karyawan   SERIAL PRIMARY KEY,
    nama_karyawan VARCHAR(50)  NOT NULL,
    alamat        VARCHAR(100) NOT NULL,
    tefon         VARCHAR(15)  NOT NULL
);

-- tabel login
CREATE TABLE tb_login (
    username    VARCHAR(50) PRIMARY KEY,
    password    VARCHAR(30) NOT NULL,
    id_karyawan INT NOT NULL REFERENCES tb_karyawan (id_karyawan)
);

-- tabel obat
CREATE TABLE tb_obat (
    id_obat       SERIAL PRIMARY KEY,
    namaobat      VARCHAR(100) NOT NULL,
    kategoriobat  VARCHAR(100) NOT NULL,
    harga_jual    NUMERIC NOT NULL,
    harga_beli    NUMERIC NOT NULL,
    stock_obat    INT NOT NULL CHECK (stock_obat >= 0),
    keterangan    TEXT NOT NULL DEFAULT ''
);

-- tabel pembeli
CREATE TABLE tb_pembeli (
    id_pembeli   SERIAL PRIMARY KEY,
    nama_lengkap VARCHAR(50)  NOT NULL,
    alamat       VARCHAR(100) NOT NULL,
    telfon       VARCHAR(15)  NOT NULL,
    usia         INT NOT NULL,
    username     VARCHAR(50) UNIQUE,
    password     VARCHAR(30)
);

-- table transaksi
CREATE TABLE tb_transaksi (
    id_transaksi     SERIAL PRIMARY KEY,
    id_pembeli       INT NOT NULL REFERENCES tb_pembeli (id_pembeli),
    id_karyawan      INT REFERENCES tb_karyawan (id_karyawan),
    tgl_transaksi    DATE NOT NULL,
    kategori_pembeli VARCHAR(20) NOT NULL,
    status           VARCHAR(15) NOT NULL DEFAULT 'keranjang'
                     CHECK (status IN ('keranjang', 'lunas')),
    total_bayar      NUMERIC NOT NULL DEFAULT 0,
    bayar            NUMERIC NOT NULL DEFAULT 0,
    kembali          NUMERIC NOT NULL DEFAULT 0
);

-- tabel detail transaksi
CREATE TABLE tb_detail_trx (
    id_detail_trx SERIAL PRIMARY KEY,
    id_trx        INT NOT NULL REFERENCES tb_transaksi (id_transaksi),
    id_obat       INT NOT NULL REFERENCES tb_obat (id_obat),
    jumlah        NUMERIC NOT NULL CHECK (jumlah > 0),
    harga_satuan  NUMERIC NOT NULL,
    total         NUMERIC NOT NULL,
    UNIQUE (id_trx, id_obat)
);

-- 1 akun apoteker: username "Kanaka", password "KanakaPM"
INSERT INTO tb_karyawan (nama_karyawan, alamat, tefon) VALUES
    ('Kanaka PM', 'Jl. Igusti', '08223531213');
INSERT INTO tb_login (username, password, id_karyawan) VALUES
    ('Kanaka', 'KanakaPM', 1);

-- Obat contoh
INSERT INTO tb_obat (namaobat, kategoriobat, harga_jual, harga_beli, stock_obat, keterangan) VALUES
    ('Panadol Extra', 'Pusing', 5000, 10000, 500, 'Pakai kalau sakit kepala'),
    ('C-1000', 'Vitamin C', 10000, 13000, 100, 'Pakai kalau sariawan'),
    ('Oralit', 'Obat pencernaan', 40000, 50000, 5, 'Coba dulu');

-- Pembeli contoh: id_pembeli 1 sengaja dipakai sebagai baris "Umum" default
-- (lihat konstanta ID_PEMBELI_UMUM_DEFAULT di tambah/proses_tambah_transaksi.php)
INSERT INTO tb_pembeli (nama_lengkap, alamat, telfon, usia) VALUES
    ('Umum', '-', '-', 0),
    ('Tera Baba', 'Jl. Kita yang dulu', '083234569876', 18);

-- Contoh akun pembeli yang bisa login sendiri (username "budi", password "budi123")
INSERT INTO tb_pembeli (nama_lengkap, alamat, telfon, usia, username, password) VALUES
    ('Budi Santoso', 'Jl. Merdeka No. 1', '081234567890', 25, 'budi', 'budi123');
