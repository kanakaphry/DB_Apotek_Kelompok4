<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$transaksiModel       = new Transaksi();
$obatModel            = new Obat();
$detailTransaksiModel = new DetailTransaksi();

if (!empty($_GET['id_transaksi'])) {
    $id_transaksi = (int) $_GET['id_transaksi'];
} else {
    $id_transaksi = (int) ($_SESSION['id_transaksi'] ?? 0);
}

$data_transaksi = $transaksiModel->getByIdJoined($id_transaksi);

if (!$data_transaksi) {
    echo "<div class='container mt-5'><p>Transaksi tidak ditemukan.</p>"
        . "<a href='dashboard.php?page=transaksi' class='btn btn-secondary'>Kembali</a></div>";
    return;
}

$lunas         = $data_transaksi['status'] === Transaksi::STATUS_LUNAS;
$milik_pembeli = empty($data_transaksi['id_karyawan']);
$bisa_edit     = !$lunas && !$milik_pembeli;

$url_self = "dashboard.php?page=detail_transaksi&id_transaksi=" . $id_transaksi;

$redirect = function (string $pesan = '') use ($url_self): void {
    echo "<script>" . ($pesan !== '' ? "alert(" . json_encode($pesan) . ");" : "")
        . " window.location.href = " . json_encode($url_self) . "</script>";
    exit;
};

// ---- Tambah obat ----
if (isset($_POST['tombol_tambah_obat'])) {
    if (!$bisa_edit) {
        $redirect('Transaksi ini tidak bisa diubah.');
    }

    $nama_obat = trim($_POST['nama_obat'] ?? '');
    $jumlah    = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
    $data_obat = $obatModel->getByName($nama_obat);

    if (!$data_obat) {
        $redirect('Obat "' . $nama_obat . '" tidak ditemukan.');
    }
    if ($jumlah === false || $jumlah < 1) {
        $redirect('Jumlah harus berupa angka bulat minimal 1.');
    }

    $idobat = (int) $data_obat['id_obat'];
    $stok   = (int) $data_obat['stock_obat'];
    $sudah  = $detailTransaksiModel->getQtyInTransaksi($id_transaksi, $idobat);

    // Stok dicek terhadap total obat ini di transaksi (yang sudah ada + yang baru).
    if ($sudah + $jumlah > $stok) {
        $redirect("Stok {$data_obat['namaobat']} tidak mencukupi. Stok tersedia $stok"
            . ($sudah > 0 ? ", di transaksi ini sudah ada $sudah" : "") . ".");
    }

    // Obat yang sama ditambah lagi -> qty digabung di baris yang sama.
    // jumlah = qty murni, total = qty x harga_satuan (sama seperti alur pembeli).
    if (!$detailTransaksiModel->addOrIncrease($id_transaksi, $idobat, $jumlah, (float) $data_obat['harga_jual'])) {
        $redirect('Gagal menambahkan obat.');
    }
    $redirect();
}

// hapus obat
if (!empty($_GET['delete_obat'])) {
    if ($bisa_edit) {
        $detailTransaksiModel->deleteFromTransaksi((int) $_GET['delete_obat'], $id_transaksi);
    }
    $redirect();
}
// bayar
if (!empty($_POST['tombol_bayar_total'])) {
    if (!$bisa_edit) {
        $redirect('Transaksi ini tidak bisa dibayar dari kasir.');
    }

    $hasil = $transaksiModel->checkout($id_transaksi, (float) ($_POST['bayar'] ?? 0));

    if ($hasil['ok']) {
        $redirect('Pembayaran berhasil. Kembalian Rp' . number_format($hasil['kembali'], 0, ',', '.'));
    }
    $redirect($hasil['message']);
}

$total_detail          = $detailTransaksiModel->sumByTransaksi($id_transaksi);
$jumlah_item_transaksi = $detailTransaksiModel->countByTransaksi($id_transaksi);
?>

<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-4 mt-5">
            <table class="table table-striped">
                <tr>
                    <td>Status</td>
                    <td>
                        <?php if ($lunas): ?>
                        <span class="badge text-bg-success">Lunas</span>
                        <?php else: ?>
                        <span class="badge text-bg-warning">Belum dibayar</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Kategori Pembeli</td>
                    <td><?= htmlspecialchars($data_transaksi['kategori_pembeli']) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Transaksi</td>
                    <td><?= htmlspecialchars($data_transaksi['tgl_transaksi']) ?></td>
                </tr>
                <tr>
                    <td>Nama Pembeli</td>
                    <td><?= htmlspecialchars($data_transaksi['nama_lengkap']) ?></td>
                </tr>
                <tr>
                    <td>Nama Apoteker</td>
                    <td>
                        <?php if ($milik_pembeli): ?>
                        <span class="badge text-bg-info">Mandiri (pembeli)</span>
                        <?php else: ?>
                        <?= htmlspecialchars($data_transaksi['nama_karyawan']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col"></div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col-6 mt-5">
            <?php if ($milik_pembeli && !$lunas): ?>
            <div class="alert alert-info">
                Ini keranjang belanja mandiri milik pembeli. Pembeli mengisi dan membayarnya sendiri,
                jadi apoteker hanya bisa memantau isinya.
            </div>
            <?php endif; ?>

            <?php if ($bisa_edit): ?>
            <form action="" method="post">
                <?php if (!empty($_POST['tombol_bayar'])): ?>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="addon-wrapping">Total Bayar :
                        Rp<?= number_format($total_detail, 0, ",", ".") ?></span>
                    <input type="number" name="bayar" class="form-control" id="" placeholder="Bayar" min="0"
                        aria-label="Bayar" required>
                </div>
                <input type="submit" name="tombol_bayar_total" class="btn btn-warning float-end mb-5" value="Bayar">
                <?php else: ?>
                <datalist id="nama_obat">
                    <?php foreach ($obatModel->getAllNames() as $data): ?>
                    <option value="<?= htmlspecialchars($data['namaobat']) ?>"
                        label="Stok: <?= (int) $data['stock_obat'] ?>"></option>
                    <?php endforeach; ?>
                </datalist>

                <div class="input-group mb-3">
                    <input type="text" name="nama_obat" list="nama_obat" class="form-control" placeholder="Nama Obat"
                        aria-label="Nama Obat" autocomplete="off">
                    <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" min="1" step="1"
                        aria-label="Jumlah">
                </div>

                <input type="submit" name="tombol_tambah_obat" class="btn btn-success float-end mb-5" value="Tambah">
                <?php if ($jumlah_item_transaksi > 0): ?>
                <input type="submit" name="tombol_bayar" class="btn btn-warning mb-5" id="" value="Bayar">
                <?php endif; ?>
                <?php endif; ?>
            </form>
            <?php endif; ?>

            <table class="table table-striped">
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th colspan="2">Sub Total</th>
                </tr>

                <?php
                    $no = 1;
                    foreach ($detailTransaksiModel->getByTransaksi($id_transaksi) as $data) {
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($data['namaobat']) ?></td>
                    <td>
                        <?= htmlspecialchars($data['jumlah']) ?>
                        <?php if (!$lunas && (float) $data['jumlah'] > (int) $data['stock_obat']): ?>
                        <br><span class="badge text-bg-danger">Stok tersisa <?= (int) $data['stock_obat'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format((float) $data['harga_satuan'], 0, ",", ".") ?></td>
                    <td><?= number_format((float) $data['total'], 0, ",", ".") ?></td>
                    <td>
                        <?php if ($bisa_edit): ?>
                        <a onclick="return confirm('Yakin mau hapus?')"
                            href="<?= $url_self ?>&delete_obat=<?= $data['id_detail_trx'] ?>"
                            class="btn btn-danger">X</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr>
                    <th colspan="4">Total Bayar</th>
                    <th colspan="2">
                        <?= number_format($total_detail, 0, ",", ".") ?>
                    </th>
                </tr>
                <?php if ($lunas): ?>
                <tr>
                    <th colspan="4">Bayar</th>
                    <th colspan="2">
                        <?= number_format((float) $data_transaksi['bayar'], 0, ",", ".") ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">Kembalian</th>
                    <th colspan="2">
                        <?= number_format((float) $data_transaksi['kembali'], 0, ",", ".") ?>
                    </th>
                </tr>
                <?php endif; ?>
            </table>
            <a href="dashboard.php?page=transaksi" class="btn btn-secondary mb-5">Kembali</a>
        </div>

        <div class="col"></div>
    </div>
</div>
