<?php
require_once "../koneksi.php";
session_start();

if (!Auth::checkApoteker()) {
    echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
    exit;
}

const ID_PEMBELI_UMUM_DEFAULT = 1;

$pembeliModel   = new Pembeli();
$transaksiModel = new Transaksi();

if (isset($_SESSION['kategori']) && $_SESSION['kategori'] == 'member') {
    $namapembeli  = $_POST['id_pembeli'];
    $dataPembeli  = $pembeliModel->getIdByName($namapembeli);

    if ($dataPembeli) {
        $idpembeli = $dataPembeli['id_pembeli'];
    } else {
        echo "<script>alert('Nama pembeli tidak ditemukan!'); window.location.href = '../view/dashboard.php?page=tambah_transaksi';</script>";
        exit;
    }
} else {
    $idpembeli = ID_PEMBELI_UMUM_DEFAULT;
}

$idkaryawan = Auth::idKaryawan();
if (!$idkaryawan) {
    echo "<script>alert('ID apoteker tidak ditemukan!'); window.location.href = '../view/dashboard.php?page=tambah_transaksi';</script>";
    exit;
}

$tgl_transaksi   = date("Y-m-d");
$kategori_pembeli = $_SESSION['kategori'] ?? 'umum';

$hasil_id = $transaksiModel->create((int) $idpembeli, (int) $idkaryawan, $tgl_transaksi, $kategori_pembeli);

if ($hasil_id) {
    $_SESSION['id_transaksi'] = $hasil_id;
    echo "<script>alert('Berhasil Menambahkan Data'); window.location.href = '../view/dashboard.php?page=detail_transaksi';</script>";
} else {
    echo "<script>alert('Gagal Menambahkan Data'); window.location.href = '../view/dashboard.php?page=tambah_transaksi';</script>";
}
