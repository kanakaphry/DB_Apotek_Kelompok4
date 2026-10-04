<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkPembeli()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$id_pembeli     = Auth::idPembeli();
$id_transaksi   = (int) ($_GET['id_transaksi'] ?? 0);
$transaksiModel = new Transaksi();
$detailModel    = new DetailTransaksi();

// getByIdForPembeli memastikan pembeli hanya bisa melihat transaksinya sendiri.
$data_transaksi = $transaksiModel->getByIdForPembeli($id_transaksi, $id_pembeli);

if (!$data_transaksi) {
    echo "<p class='container mt-5'>Transaksi tidak ditemukan.</p>";
    return;
}

$items = $detailModel->getByTransaksi($id_transaksi);
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-6 mt-5">
            <h4>Detail Pembelian #<?= $id_transaksi ?></h4>
            <table class="table table-striped">
                <tr>
                    <td>Tanggal</td>
                    <td><?= htmlspecialchars($data_transaksi['tgl_transaksi']) ?></td>
                </tr>
                <tr>
                    <td>Dilayani</td>
                    <td><?= htmlspecialchars($data_transaksi['nama_karyawan'] ?? 'Beli mandiri') ?></td>
                </tr>
            </table>
            <table class="table table-striped">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                </tr>
                <?php $no = 1; foreach ($items as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($data['namaobat']) ?></td>
                    <td><?= htmlspecialchars($data['jumlah']) ?></td>
                    <td><?= number_format((float) $data['harga_satuan'], 0, ",", ".") ?></td>
                    <td><?= number_format((float) $data['total'], 0, ",", ".") ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="4">Total Bayar</th>
                    <th>Rp<?= number_format((float) $data_transaksi['total_bayar'], 0, ",", ".") ?></th>
                </tr>
                <tr>
                    <th colspan="4">Bayar</th>
                    <th>Rp<?= number_format((float) $data_transaksi['bayar'], 0, ",", ".") ?></th>
                </tr>
                <tr>
                    <th colspan="4">Kembalian</th>
                    <th>Rp<?= number_format((float) $data_transaksi['kembali'], 0, ",", ".") ?></th>
                </tr>
            </table>
            <a href="dashboard_pembeli.php?page=riwayat" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="col"></div>
    </div>
</div>
