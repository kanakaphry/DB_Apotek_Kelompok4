<?php
require_once __DIR__ . '/koneksi.php';

if (isset($_POST['tombol_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Coba sebagai akun apoteker (staf) dulu...
    $loginModel = new Login();
    $data = $loginModel->attempt($username, $password);

    if ($data) {
        Auth::login($data['username'], (int) $data['id_karyawan']);
        echo "<script>alert('Berhasil Login'); window.location.href = 'view/dashboard.php?page=obat'</script>";
        exit;
    }

    // ...kalau bukan apoteker, coba sebagai akun pembeli.
    $pembeliModel = new Pembeli();
    $dataPembeli  = $pembeliModel->attempt($username, $password);

    if ($dataPembeli) {
        Auth::loginPembeli($dataPembeli['username'], (int) $dataPembeli['id_pembeli']);
        echo "<script>alert('Berhasil Login'); window.location.href = 'view/dashboard_pembeli.php?page=katalog'</script>";
        exit;
    }

    echo "<script>alert('Gagal Login, username/password salah'); window.location.href = 'login.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Apotek</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <form action="" method="post">
        <div class="container">
            <div class="row">
                <div class="col"></div>
                <div class="col">
                    <div class="form-floating mb-3 mt-5">
                        <input type="text" name="username" class="form-control" id="floatingInput"
                            placeholder="Username" autocomplete="off">
                        <label for="floatingInput">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingInput"
                            placeholder="Password" autocomplete="off">
                        <label for="floatingInput">Password</label>
                    </div>
                    <input type="submit" value="Login" name="tombol_login" class="btn btn-success float-end" />
                    <p class="mt-3">
                        Belum punya akun pembeli?
                        <a href="register_pembeli.php">Daftar di sini</a>
                    </p>
                </div>
                <div class="col"></div>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/script.js"></script>
</body>

</html>
