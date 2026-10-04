<?php
if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

if (isset($_POST["tombol_register"])) {
    $nama_lengkap        = $_POST['nama_lengkap'];
    $username            = $_POST['username'];
    $password            = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_pass'];
    $alamat              = $_POST['alamat'];
    $tefon               = $_POST['tefon'];

    $loginModel    = new Login();
    $karyawanModel = new Karyawan();

    if ($password !== $konfirmasi_password) {
        echo "<script>alert('Password dan Konfirmasi password tidak sama'); window.location.href = 'dashboard.php?page=register'</script>";
    } elseif ($loginModel->usernameExists($username)) {
        echo "<script>alert('Username sudah ada, gunakan username lainnya!'); window.location.href = 'dashboard.php?page=register'</script>";
    } else {
        global $koneksi;
        $berhasil = true;

        // Insert karyawan + login dijalankan sebagai satu transaksi:
        // kalau salah satu gagal, keduanya dibatalkan (tidak ada akun "yatim").
        try {
            $koneksi->mulai_transaksi();

            $id_karyawan = $karyawanModel->create($nama_lengkap, $alamat, $tefon);
            $input_login = $loginModel->register($username, $password, $id_karyawan);

            if (!$id_karyawan || !$input_login) {
                $berhasil = false;
                $koneksi->rollback();
            } else {
                $koneksi->commit();
            }
        } catch (DatabaseException $e) {
            $berhasil = false;
            $koneksi->rollback();
        }

        if (!$berhasil) {
            echo "<script>alert('Data Gagal Register'); window.location.href = 'dashboard.php?page=register' </script>";
        } else {
            echo "<script>alert('Berhasil Register'); window.location.href = '../login.php' </script>";
        }
    }
}
?>
<form action="" method="post">
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_lengkap" class="form-control" id="floatingInput"
                        placeholder="Nama Lengkap" autocomplete="off">
                    <label for="floatingInput">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="floatingInput"
                        placeholder="Username" autocomplete="off">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="password" class="form-control" id="floatingInput"
                        placeholder="Password" autocomplete="off">
                    <label for="floatingInput">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="konfirmasi_pass" class="form-control" id="floatingInput"
                        placeholder="Konfirmasi Password" autocomplete="off">
                    <label for="floatingInput">Konfirmasi Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="alamat" class="form-control" id="floatingInput"
                        placeholder="Alamat" autocomplete="off">
                    <label for="floatingInput">Alamat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="tefon" class="form-control" id="floatingInput"
                        placeholder="Telfon" autocomplete="off">
                    <label for="floatingInput">telfon</label>
                </div>

                <!-- Hanya ada satu role staf, jadi tidak perlu lagi pilihan level user. -->
                <input type="submit" value="Register" name="tombol_register" class="btn btn-success mb-3 float-end" />
            </div>
            <div class="col"></div>
        </div>
    </div>
</form>
