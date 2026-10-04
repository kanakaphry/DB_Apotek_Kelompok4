<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkPembeli()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$obatModel = new Obat();
$daftar_obat = $obatModel->getAll();
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-3">
            <h4>Katalog Obat</h4>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Obat</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Jumlah Beli</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($daftar_obat as $data): ?>
                    <tr>
                        <th scope="row"><?= $no++ ?></th>
                        <td><?= htmlspecialchars($data['namaobat']) ?></td>
                        <td><?= htmlspecialchars($data['kategoriobat']) ?></td>
                        <td>Rp<?= number_format((float) $data['harga_jual'], 0, ",", ".") ?></td>
                        <td><?= htmlspecialchars($data['stock_obat']) ?></td>
                        <td><?= htmlspecialchars($data['keterangan']) ?></td>
                        <td>
                            <?php if ((int) $data['stock_obat'] > 0): ?>
                            <form action="../pembeli/proses_tambah_keranjang.php" method="post" class="d-flex">
                                <input type="hidden" name="id_obat" value="<?= $data['id_obat'] ?>">
                                <input type="number" name="jumlah" class="form-control form-control-sm" min="1"
                                    max="<?= $data['stock_obat'] ?>" value="1" style="width: 80px;" required>
                                <input type="submit" name="tombol_beli" value="Beli"
                                    class="btn btn-success btn-sm ms-2">
                            </form>
                            <?php else: ?>
                            <span class="badge text-bg-secondary">Stok habis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col"></div>
    </div>
</div>
