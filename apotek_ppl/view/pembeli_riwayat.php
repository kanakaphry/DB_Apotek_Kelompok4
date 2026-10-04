<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkPembeli()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$id_pembeli     = Auth::idPembeli();
$transaksiModel = new Transaksi();
$riwayat        = $transaksiModel->getPaidByPembeli($id_pembeli);
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-3">
            <h4>Riwayat Pembelian</h4>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Dilayani</th>
                        <th>Total Bayar</th>
                        <th>Bayar</th>
                        <th>Kembalian</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($riwayat as $data): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($data['tgl_transaksi']) ?></td>
                        <td><?= htmlspecialchars($data['nama_karyawan'] ?? 'Beli mandiri') ?></td>
                        <td>Rp<?= number_format((float) $data['total_bayar'], 0, ",", ".") ?></td>
                        <td>Rp<?= number_format((float) $data['bayar'], 0, ",", ".") ?></td>
                        <td>Rp<?= number_format((float) $data['kembali'], 0, ",", ".") ?></td>
                        <td>
                            <a class="btn btn-info btn-sm"
                                href="dashboard_pembeli.php?page=riwayat_detail&id_transaksi=<?= $data['id_transaksi'] ?>">Lihat</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="7">Belum ada riwayat pembelian.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col"></div>
    </div>
</div>
