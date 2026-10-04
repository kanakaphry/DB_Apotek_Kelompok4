<?php
require_once "../koneksi.php";

if (!Auth::checkPembeli()) {
    echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
} else {
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Pembeli - Apotek</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_pembeli.php?page=katalog">APOTEK</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mt-1 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_pembeli.php?page=katalog">Katalog Obat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_pembeli.php?page=keranjang">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_pembeli.php?page=riwayat">Riwayat Pembelian</a>
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
            case 'katalog':
                include "pembeli_katalog.php";
                break;
            case 'keranjang':
                include "pembeli_keranjang.php";
                break;
            case 'riwayat':
                include "pembeli_riwayat.php";
                break;
            case 'riwayat_detail':
                include "pembeli_riwayat_detail.php";
                break;
            default:
                include "pembeli_katalog.php";
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
