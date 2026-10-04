<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$obatModel = new Obat();

if (isset($_GET['id_obat'])) {
    $id = (int) $_GET['id_obat'];
    $hasil = $obatModel->delete($id);

    if (!$hasil) {
        echo "Gagal menghapus data obat";
    } else {
        echo "<script>alert('Data Berhasil Hapus'); window.location.href = 'dashboard.php?page=obat'</script>";
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-3">
            <a href="dashboard.php?page=tambah_obat" class="btn btn-success float-end">+ Tambah</a>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Obat</th>
                        <th scope="col">Kategori Obat</th>
                        <th scope="col">Harga Jual</th>
                        <th scope="col">Harga Beli</th>
                        <th scope="col">Stok Obat</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($obatModel->getAll() as $data) {
                    ?>
                    <tr>
                        <th scope="row"><?= $no++ ?></th>
                        <td><?= htmlspecialchars($data['namaobat']) ?></td>
                        <td><?= htmlspecialchars($data['kategoriobat']) ?></td>
                        <td><?= htmlspecialchars($data['harga_jual']) ?></td>
                        <td><?= htmlspecialchars($data['harga_beli']) ?></td>
                        <td><?= htmlspecialchars($data['stock_obat']) ?></td>
                        <td><?= htmlspecialchars($data['keterangan']) ?></td>
                        <td colspan="2">
                            <a class="btn btn-warning rounded-pill"
                                href="dashboard.php?page=update_obat&id_obat=<?= $data['id_obat'] ?>">Edit
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
