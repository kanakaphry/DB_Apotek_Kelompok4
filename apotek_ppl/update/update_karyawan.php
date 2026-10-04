<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$karyawanModel = new Karyawan();
$loginModel    = new Login();

$id_karyawan        = (int) $_GET['id_karyawan'];
$dataUpdateLogin    = $loginModel->getByKaryawanId($id_karyawan);
$dataUpdateKaryawan = $karyawanModel->getById($id_karyawan);

if (isset($_POST["tombol_update"])) {
    $nama_lengkap    = $_POST['nama_lengkap'];
    $username        = $_POST['username'];
    $password        = $_POST['pass'];
    $konfirmasi_pass = $_POST['konfirmasi_pass'];
    $alamat          = $_POST['alamat'];
    $tefon           = $_POST['tefon'];

    $usernameDipakaiOrangLain =
        $loginModel->usernameExists($username)
        && $dataUpdateLogin['username'] !== $username;

    if ($password !== '' && $password !== $konfirmasi_pass) {
        echo "<script>alert('Password dan Konfirmasi password tidak sama');window.location.href='dashboard.php?page=update_karyawan&id_karyawan=" . $id_karyawan . "'</script>";
    } elseif ($usernameDipakaiOrangLain) {
        echo "<script>alert('Username sudah ada, gunakan username lainnya!');window.location.href='dashboard.php?page=update_karyawan&id_karyawan=" . $id_karyawan . "'</script>";
    } else {
        $hasil_karyawan = $karyawanModel->update($id_karyawan, $nama_lengkap, $alamat, $tefon);

        if ($password !== '') {
            $hasil_login = $loginModel->updateAccountWithPassword($id_karyawan, $username, $password);
        } else {
            $hasil_login = $loginModel->updateAccount($id_karyawan, $username);
        }

        if (Auth::idKaryawan() == $id_karyawan) {
            Auth::login($username, $id_karyawan);
        }

        if (!$hasil_karyawan || !$hasil_login) {
            echo "<script>alert('Gagal Update Karyawan');window.location.href='dashboard.php?page=update_karyawan&id_karyawan=" . $id_karyawan . "'</script>";
        } else {
            echo "<script>alert('Berhasil Update Karyawan');window.location.href='dashboard.php?page=karyawan'</script>";
        }
    }
}

?>
<form action="" method="post">
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6 col-sm-12">
                <input type="hidden" name="id_karyawan" value="<?= $dataUpdateKaryawan['id_karyawan']; ?>">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_lengkap" class="form-control" id="floatingInput"
                        placeholder="Nama Lengkap" autocomplete="off"
                        value="<?= htmlspecialchars($dataUpdateKaryawan['nama_karyawan']) ?>">
                    <label for=" floatingInput">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="floatingInput"
                        placeholder="Username" autocomplete="off" value="<?= htmlspecialchars($dataUpdateLogin['username']) ?>">
                    <label for=" floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="pass" class="form-control" id="floatingInput"
                        placeholder="Ganti Password">
                    <label for="floatingInput">Ganti Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="konfirmasi_pass" class="form-control" id="floatingInput"
                        placeholder="Konfirmasi Password">
                    <label for="floatingInput">konfirmasi Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="alamat" class="form-control" id="floatingInput"
                        placeholder="Alamat" value="<?= htmlspecialchars($dataUpdateKaryawan['alamat']) ?>">
                    <label for="floatingInput">Alamat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="tefon" class="form-control" id="floatingInput"
                        placeholder="Telepon" value="<?= htmlspecialchars($dataUpdateKaryawan['tefon']) ?>">
                    <label for="floatingInput">Telepon</label>
                </div>
                <input type="submit" name="tombol_update" value="Update Data" class="btn btn-success mb-3 float-end">
            </div>
            <div class="col">
            </div>
        </div>
    </div>

</form>
