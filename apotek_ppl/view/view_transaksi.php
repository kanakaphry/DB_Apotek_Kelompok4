<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}
$transaksiModel = new Transaksi();
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-5">
            <a href="dashboard.php?page=tambah_transaksi" class="btn btn-success float-end">+ Tambah transaksi</a>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Pembeli</th>
                        <th scope="col">Nama Apoteker</th>
                        <th scope="col">Tgl Transaksi</th>
                        <th scope="col">Kategori Pembeli</th>
                        <th scope="col">Status</th>
                        <th scope="col">Total Bayar</th>
                        <th scope="col">Bayar</th>
                        <th scope="col">Kembali</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($transaksiModel->getAllJoined() as $data) {
                    ?>
                    <tr>
                        <th scope="row"><?= $no++ ?></th>
                        <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                        <td>
                            <?php if ($data['nama_karyawan'] === null): ?>
                            <span class="badge text-bg-info">Mandiri (pembeli)</span>
                            <?php else: ?>
                            <?= htmlspecialchars($data['nama_karyawan']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($data['tgl_transaksi']) ?></td>
                        <td><?= htmlspecialchars($data['kategori_pembeli']) ?></td>
                        <td>
                            <?php if ($data['status'] === Transaksi::STATUS_LUNAS): ?>
                            <span class="badge text-bg-success">Lunas</span>
                            <?php else: ?>
                            <span class="badge text-bg-warning">Belum dibayar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($data['total_bayar'], 0, ",", ".") ?></td>
                        <td><?= number_format($data['bayar'], 0, ",", ".") ?></td>
                        <td><?= number_format($data['kembali'], 0, ",", ".") ?></td>
                        <td>
                            <a class="btn btn-warning rounded-pill" id="edit-button"
                                href="dashboard.php?page=detail_transaksi&id_transaksi=<?= $data['id_transaksi'] ?>">Lihat
                                Transaksi
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    ?>

                </tbody>
            </table>
        </div>
        <div class="col">
        </div>
    </div>
</div>
