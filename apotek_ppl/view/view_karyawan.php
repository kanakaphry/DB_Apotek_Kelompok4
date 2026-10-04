<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}
// delete

$loginModel           = new Login();
$karyawanModel        = new Karyawan();
$transaksiModel       = new Transaksi();
$detailTransaksiModel = new DetailTransaksi();

if (isset($_GET['id_karyawan'])) {
    $id_karyawan = (int) $_GET['id_karyawan'];

    global $koneksi;
    $hapus_karyawan = false;

    try {
        $koneksi->mulai_transaksi();

        $loginModel->deleteByKaryawanId($id_karyawan);

        // Detail transaksi ikut dihapus lewat tabel yang benar (tb_detail_trx),
        // supaya tidak ada data "yatim" saat karyawan dihapus.
        foreach ($transaksiModel->getIdsByKaryawan($id_karyawan) as $id_transaksi) {
            $detailTransaksiModel->deleteByTransaksi((int) $id_transaksi);
        }

        $transaksiModel->deleteByKaryawan($id_karyawan);
        $hapus_karyawan = $karyawanModel->delete($id_karyawan);

        if ($hapus_karyawan) {
            $koneksi->commit();
        } else {
            $koneksi->rollback();
        }
    } catch (DatabaseException $e) {
        $koneksi->rollback();
        $hapus_karyawan = false;
    }

    if (!$hapus_karyawan) {
        echo "<script>alert('Data Gagal dihapus');window.location.href='dashboard.php?page=karyawan'</script>";
    } else {
        echo "<script>alert('Data Berhasil Dihapus');window.location.href='dashboard.php?page=karyawan'</script>";
    }
}
?>

<!-- TABEL -->
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-5">
            <a href="dashboard.php?page=register" class="btn btn-success float-end m-3">+ Register</a>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Username</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">telfon</th>
                        <th scope="col" colspan="2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($loginModel->getAllWithKaryawan() as $data) {
                    ?>
                    <tr>
                        <th scope="row"><?= $no++ ?></th>
                        <td><?= htmlspecialchars($data['nama_karyawan']) ?></td>
                        <td><?= htmlspecialchars($data['username']) ?></td>
                        <td><?= htmlspecialchars($data['alamat']) ?></td>
                        <td><?= htmlspecialchars($data['tefon']) ?></td>

                        <td>
                            <a href="dashboard.php?page=update_karyawan&id_karyawan=<?= $data['id_karyawan'] ?>"
                                class="btn btn-warning">Update</a>
                        </td>
                        <td>
                            <?php if (Auth::idKaryawan() != $data['id_karyawan']): ?>
                            <a onclick="return confirm('apakah benar ingin menghapus data?')"
                                href="dashboard.php?page=karyawan&id_karyawan=<?= $data['id_karyawan'] ?>"
                                class="btn btn-danger">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col"></div>
    </div>
</div>
