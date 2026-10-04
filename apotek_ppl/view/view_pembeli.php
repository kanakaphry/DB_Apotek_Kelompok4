<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}
// PROSES DELETE

$pembeliModel         = new Pembeli();
$transaksiModel       = new Transaksi();
$detailTransaksiModel = new DetailTransaksi();

if (isset($_GET['id_pembeli'])) {
    $id_pembeli = (int) $_GET['id_pembeli'];

    $daftar_transaksi = $transaksiModel->getIdsByPembeli($id_pembeli);

    if (empty($daftar_transaksi)) {
        // Pembeli belum pernah bertransaksi sama sekali -> aman dihapus
        $bisa_dihapus = true;
    } else {
        // Cek apakah ada transaksi yang sudah "nyata" (sudah punya item / sudah dibayar)
        $bisa_dihapus = true;
        foreach ($daftar_transaksi as $trx) {
            $punya_detail = $detailTransaksiModel->countByTransaksi((int) $trx['id_transaksi']) > 0;
            if ($punya_detail || (float) $trx['bayar'] > 0) {
                $bisa_dihapus = false;
                break;
            }
        }
    }

    if (!$bisa_dihapus) {
        echo "<script>alert('Anda tidak bisa menghapus pembeli ini karena sudah melakukan transaksi');window.location.href='dashboard.php?page=pembeli'</script>";
    } else {
        global $koneksi;
        $hapus_pembeli = false;

        try {
            $koneksi->mulai_transaksi();

            // Hapus transaksi kosong (draft) beserta detailnya (jika ada), lalu hapus pembeli
            foreach ($daftar_transaksi as $trx) {
                $detailTransaksiModel->deleteByTransaksi((int) $trx['id_transaksi']);
            }
            $transaksiModel->deleteByPembeli($id_pembeli);

            $hapus_pembeli = $pembeliModel->delete($id_pembeli);

            if ($hapus_pembeli) {
                $koneksi->commit();
            } else {
                $koneksi->rollback();
            }
        } catch (DatabaseException $e) {
            $koneksi->rollback();
            $hapus_pembeli = false;
        }

        if (!$hapus_pembeli) {
            echo "<script>alert('Data Gagal dihapus');window.location.href='dashboard.php?page=pembeli'</script>";
        } else {
            echo "<script>alert('Data Berhasil Dihapus');window.location.href='dashboard.php?page=pembeli'</script>";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-10 mt-3">
            <a href="dashboard.php?page=tambah_pembeli" class="btn btn-success float-end">+ Tambah Pembeli</a>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Telepon</th>
                        <th scope="col">Usia</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($pembeliModel->getAll() as $data) {
                    ?>
                    <tr>
                        <th scope="row"><?= $no++ ?></th>
                        <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($data['alamat']) ?></td>
                        <td><?= htmlspecialchars($data['telfon']) ?></td>
                        <td><?= htmlspecialchars($data['usia']) ?></td>
                        <td colspan="2">
                            <a class="btn btn-warning rounded-pill"
                                href="dashboard.php?page=update_pembeli&id_pembeli=<?= $data['id_pembeli'] ?>">Edit
                            </a>
                        </td>
                        <td class="ps-0">
                            <a class="btn btn-danger rounded-pill"
                                href="dashboard.php?page=pembeli&id_pembeli=<?= $data['id_pembeli'] ?>"
                                onclick="return confirm('Apakah anda yakin untuk menghapus data ini ?')">Hapus
                            </a>
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
