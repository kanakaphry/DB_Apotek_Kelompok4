<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$pembeliModel = new Pembeli();

if (isset($_POST['kirim'])) {

    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat       = $_POST['alamat'];
    $telfon       = $_POST['telfon'];
    $usia         = $_POST['usia'];

    $hasil = $pembeliModel->create($nama_lengkap, $alamat, $telfon, (int) $usia);

    if (!$hasil) {
        echo "<script>alert('Data Gagal Masuk'); window.location.href = 'dashboard.php?page=tambah_pembeli' </script>";
    } else {
        echo "<script>alert('Data Berhasil Masuk'); window.location.href = 'dashboard.php?page=pembeli'</script>";
    }
}

?>

<form action="" method="post">
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6 col-sm-12">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_lengkap" class="form-control" id="floatingInput"
                        placeholder="Nama Pembeli" required>
                    <label for="floatingInput">Nama Pembeli</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="alamat" class="form-control" id="floatingInput"
                        placeholder="Alamat Pembeli" required>
                    <label for="floatingInput">Alamat Pembeli</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="telfon" class="form-control" id="floatingInput"
                        placeholder="Nomor Telepon" required>
                    <label for="floatingInput">Nomor Telepon</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="usia" class="form-control" id="floatingInput"
                        placeholder="usia Pembeli" required>
                    <label for="floatingInput">usia</label>
                </div>
                <input type="submit" name="kirim" value="Simpan" class="btn btn-success float-end">
            </div>
            <div class="col">
            </div>
        </div>
    </div>
</form>
