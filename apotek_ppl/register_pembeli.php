<?php
require_once __DIR__ . '/koneksi.php';

$error = '';

if (isset($_POST['tombol_register'])) {
    $nama_lengkap        = trim($_POST['nama_lengkap']);
    $alamat              = trim($_POST['alamat']);
    $telfon              = trim($_POST['telfon']);
    $usia                = (int) $_POST['usia'];
    $username            = trim($_POST['username']);
    $password            = trim($_POST['password']);
    $konfirmasi_password = trim($_POST['konfirmasi_pass']);

    $pembeliModel = new Pembeli();

    if ($nama_lengkap === '' || $alamat === '' || $telfon === '' || $username === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif ($password !== $konfirmasi_password) {
        $error = 'Password dan konfirmasi password tidak sama.';
    } elseif ($pembeliModel->usernameExists($username)) {
        $error = 'Username sudah dipakai, gunakan username lain.';
    } else {
        $hasil = $pembeliModel->registerAccount($nama_lengkap, $alamat, $telfon, $usia, $username, $password);

        if ($hasil) {
            echo "<script>alert('Registrasi berhasil, silakan login'); window.location.href = 'login.php'</script>";
            exit;
        } else {
            $error = 'Registrasi gagal, silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pembeli - Apotek</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6">
                <h3 class="mt-5">Registrasi Akun Pembeli</h3>
                <?php if ($error !== ''): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form action="" method="post">
                    <div class="form-floating mb-3 mt-3">
                        <input type="text" name="nama_lengkap" class="form-control" id="floatingInput"
                            placeholder="Nama Lengkap" autocomplete="off"
                            value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                        <label for="floatingInput">Nama Lengkap</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="alamat" class="form-control" id="floatingInput2"
                            placeholder="Alamat" autocomplete="off"
                            value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>" required>
                        <label for="floatingInput2">Alamat</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="telfon" class="form-control" id="floatingInput3"
                            placeholder="Telfon" autocomplete="off"
                            value="<?= htmlspecialchars($_POST['telfon'] ?? '') ?>" required>
                        <label for="floatingInput3">Telfon</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" name="usia" class="form-control" id="floatingInput4"
                            placeholder="Usia" autocomplete="off"
                            value="<?= htmlspecialchars($_POST['usia'] ?? '') ?>" required>
                        <label for="floatingInput4">Usia</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" id="floatingInput5"
                            placeholder="Username" autocomplete="off"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        <label for="floatingInput5">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingInput6"
                            placeholder="Password" autocomplete="off" required>
                        <label for="floatingInput6">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="konfirmasi_pass" class="form-control" id="floatingInput7"
                            placeholder="Konfirmasi Password" autocomplete="off" required>
                        <label for="floatingInput7">Konfirmasi Password</label>
                    </div>
                    <input type="submit" value="Daftar" name="tombol_register" class="btn btn-success float-end mb-3" />
                </form>
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/script.js"></script>
</body>

</html>
