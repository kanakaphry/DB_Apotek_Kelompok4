<?php
require_once "../koneksi.php";

if (!Auth::checkApoteker()) {
    echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
} else {
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Apotek</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">APOTEKER</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mt-1 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php?page=obat">Obat</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?page=karyawan">Apoteker</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?page=pembeli">Pembeli</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?page=transaksi">Transaksi</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <?= htmlspecialchars(Auth::username()) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item text-danger" href="../logout.php"
                                    onclick="return confirm('Apakah anda yakin ingin logout?')">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <?php
        switch ($_GET['page'] ?? '') {

            case 'obat':
                include "view_obat.php";
                break;
            case 'tambah_obat':
                include "../tambah/tambah_obat.php";
                break;
            case 'update_obat':
                include "../update/update_obat.php";
                break;


            case 'karyawan':
                include "view_karyawan.php";
                break;
            case 'register':
                include "register.php";
                break;
            case 'update_karyawan':
                include "../update/update_karyawan.php";
                break;


            case 'pembeli':
                include "view_pembeli.php";
                break;
            case 'tambah_pembeli':
                include "../tambah/tambah_pembeli.php";
                break;
            case 'update_pembeli':
                include "../update/update_pembeli.php";
                break;


            case 'transaksi':
                include "view_transaksi.php";
                break;
            case 'tambah_transaksi':
                include "../tambah/tambah_transaksi.php";
                break;
            case 'detail_transaksi':
                include "view_detail_transaksi.php";
                break;

        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/script.js"></script>
</body>

</html>

<?php
}
?>
