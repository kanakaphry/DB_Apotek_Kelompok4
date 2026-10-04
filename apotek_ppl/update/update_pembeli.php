<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$pembeliModel = new Pembeli();

$id_pembeli  = (int) $_GET['id_pembeli'];
$data_update = $pembeliModel->getById($id_pembeli);

if (isset($_POST['tombol_update'])) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat       = $_POST['alamat'];
    $telfon       = $_POST['telfon'];
    $usia         = $_POST['usia'];

    $hasil = $pembeliModel->update($id_pembeli, $nama_lengkap, $alamat, $telfon, (int) $usia);

    if ($hasil) {
        echo "<script>alert('Berhasil update data pembeli'); window.location.href='dashboard.php?page=pembeli';</script>";
    } else {
        echo "<script>alert('Gagal update data pembeli'); window.location.href='dashboard.php?page=update_pembeli';</script>";
    }
}
?>

<form action="" method="post">
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6 col-sm-12">
                <input type="hidden" name="id_pembeli" value="<?= $data_update['id_pembeli']; ?>">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_lengkap" class="form-control" id="floatingInput"
                        placeholder="Nama Lengkap" value="<?= htmlspecialchars($data_update['nama_lengkap']); ?>">
                    <label for="floatingInput">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="alamat" class="form-control" id="floatingInput" placeholder="Alamat"
                        value="<?= htmlspecialchars($data_update['alamat']); ?>">
                    <label for="floatingInput">Alamat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="telfon" class="form-control" id="floatingInput" placeholder="No. Telepon"
                        value="<?= htmlspecialchars($data_update['telfon']); ?>">
                    <label for="floatingInput">No. Telepon</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="usia" class="form-control" id="floatingInput" placeholder="Usia"
                        value="<?= htmlspecialchars($data_update['usia']); ?>">
                    <label for="floatingInput">Usia</label>
                </div>
                <input type="submit" name="tombol_update" value="Simpan" class="btn btn-success float-end">
            </div>
            <div class="col"></div>
        </div>
    </div>
</form>
