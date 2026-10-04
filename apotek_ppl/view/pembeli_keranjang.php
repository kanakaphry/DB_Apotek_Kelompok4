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
$detailModel    = new DetailTransaksi();

$pending = $transaksiModel->getPendingByPembeli($id_pembeli);
$id_transaksi = $pending ? (int) $pending['id_transaksi'] : 0;

$redirect = function (string $pesan, string $page = 'keranjang'): void {
    echo "<script>" . ($pesan !== '' ? "alert(" . json_encode($pesan) . ");" : "")
        . " window.location.href = 'dashboard_pembeli.php?page=" . $page . "'</script>";
    exit;
};

// Hapus 1 item dari keranjang. Stok tidak perlu dikembalikan karena stok
// baru dikurangi saat pembayaran.
if ($id_transaksi && !empty($_GET['hapus_item'])) {
    $detailModel->deleteFromTransaksi((int) $_GET['hapus_item'], $id_transaksi);
    $redirect('');
}

// Proses pembayaran: seluruh pengecekan (uang, stok, status) & pengurangan stok
// dilakukan di Transaksi::checkout() dalam satu transaksi database.
if ($id_transaksi && !empty($_POST['tombol_bayar'])) {
    $hasil = $transaksiModel->checkout($id_transaksi, (float) ($_POST['bayar'] ?? 0));

    if ($hasil['ok']) {
        $redirect('Pembayaran berhasil, terima kasih! Kembalian Rp' . number_format($hasil['kembali'], 0, ',', '.'), 'riwayat');
    }
    $redirect($hasil['message']);
}

$total = $id_transaksi ? $detailModel->sumByTransaksi($id_transaksi) : 0;
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-6 mt-5">
            <h4>Keranjang Belanja</h4>

            <?php if (!$id_transaksi || $total <= 0): ?>
            <p>Keranjang Anda masih kosong. Silakan pilih obat di
                <a href="dashboard_pembeli.php?page=katalog">Katalog Obat</a>.</p>
            <?php else: ?>
            <table class="table table-striped">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                    <th></th>
                </tr>
                <?php
                    $no = 1;
                    foreach ($detailModel->getByTransaksi($id_transaksi) as $data) {
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($data['namaobat']) ?></td>
                    <td>
                        <?= htmlspecialchars($data['jumlah']) ?>
                        <?php if ((float) $data['jumlah'] > (int) $data['stock_obat']): ?>
                        <br><span class="badge text-bg-danger">Stok tersisa <?= (int) $data['stock_obat'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format((float) $data['harga_satuan'], 0, ",", ".") ?></td>
                    <td><?= number_format((float) $data['total'], 0, ",", ".") ?></td>
                    <td>
                        <a onclick="return confirm('Hapus obat ini dari keranjang?')"
                            href="dashboard_pembeli.php?page=keranjang&hapus_item=<?= $data['id_detail_trx'] ?>"
                            class="btn btn-danger btn-sm">X</a>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th colspan="4">Total Bayar</th>
                    <th colspan="2">Rp<?= number_format($total, 0, ",", ".") ?></th>
                </tr>
            </table>

            <p class="text-muted small">Stok obat baru dikurangi setelah pembayaran berhasil.</p>
            <form action="" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text">Bayar</span>
                    <input type="number" name="bayar" class="form-control" placeholder="Jumlah uang" required>
                </div>
                <input type="submit" name="tombol_bayar" value="Bayar Sekarang" class="btn btn-warning float-end">
            </form>
            <?php endif; ?>
        </div>
        <div class="col"></div>
    </div>
</div>
