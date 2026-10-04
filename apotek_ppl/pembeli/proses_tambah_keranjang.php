<?php
require_once __DIR__ . '/../koneksi.php';

if (!Auth::checkPembeli()) {
    echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
    exit;
}

if (!isset($_POST['tombol_beli'])) {
    header("Location: ../view/dashboard_pembeli.php?page=katalog");
    exit;
}

function kembali_katalog(string $pesan, string $page = 'katalog'): void
{
    echo "<script>alert(" . json_encode($pesan) . "); window.location.href = '../view/dashboard_pembeli.php?page=" . $page . "'</script>";
    exit;
}

$id_pembeli = Auth::idPembeli();
$id_obat    = filter_var($_POST['id_obat'] ?? null, FILTER_VALIDATE_INT);
$jumlah     = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);

if ($id_obat === false || $jumlah === false || $jumlah < 1) {
    kembali_katalog('Data tidak valid. Jumlah harus berupa angka bulat minimal 1.');
}

$obatModel      = new Obat();
$transaksiModel = new Transaksi();
$detailModel    = new DetailTransaksi();

$data_obat = $obatModel->getById($id_obat);
if (!$data_obat) {
    kembali_katalog('Obat tidak ditemukan.');
}

$stok = (int) $data_obat['stock_obat'];

// Keranjang aktif (transaksi mandiri yang belum lunas) - belum tentu sudah ada.
$pending      = $transaksiModel->getPendingByPembeli($id_pembeli);
$id_transaksi = $pending ? (int) $pending['id_transaksi'] : 0;

// Cek stok terhadap TOTAL obat ini di keranjang (yang sudah ada + yang baru),
// bukan hanya jumlah yang baru diklik. Stok sendiri BELUM dikurangi di sini;
// stok baru berkurang saat pembayaran (Transaksi::checkout).
$sudah_di_keranjang = $id_transaksi ? $detailModel->getQtyInTransaksi($id_transaksi, $id_obat) : 0;

if ($sudah_di_keranjang + $jumlah > $stok) {
    $sisa = max(0, $stok - $sudah_di_keranjang);
    kembali_katalog(
        "Stok {$data_obat['namaobat']} tidak mencukupi. Stok tersedia $stok"
        . ($sudah_di_keranjang > 0 ? ", di keranjang Anda sudah ada $sudah_di_keranjang (sisa yang bisa ditambah: $sisa)" : "")
        . "."
    );
}

if (!$id_transaksi) {
    $id_transaksi = $transaksiModel->createSelfCheckout($id_pembeli, date("Y-m-d"));
    if (!$id_transaksi) {
        kembali_katalog('Gagal membuat keranjang.');
    }
}

// Obat yang sama ditambah lagi -> qty digabung di baris yang sama.
if (!$detailModel->addOrIncrease($id_transaksi, $id_obat, $jumlah, (float) $data_obat['harga_jual'])) {
    kembali_katalog('Gagal menambahkan ke keranjang.');
}

kembali_katalog('Obat ditambahkan ke keranjang', 'keranjang');
