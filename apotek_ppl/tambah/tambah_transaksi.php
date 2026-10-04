<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}
$pembeliModel = new Pembeli();
?>
<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-md-6 mt-5">
            <?php
            if (@$_POST['kategori_pembeli'] == 'member') {
                $_SESSION['kategori'] = $_POST['kategori_pembeli'];
            ?>
            <form action="../tambah/proses_tambah_transaksi.php" method="post">
                <datalist id="id_pembeli">
                    <?php foreach ($pembeliModel->getAllNames() as $data): ?>
                    <option value="<?= htmlspecialchars($data['nama_lengkap']) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="id_pembeli" list="id_pembeli" class="form-control" id="floatingInput"
                        placeholder="Nama Pembeli" autocomplete="off" required>
                    <label for="floatingInput">Nama Pembeli</label>
                </div>

                <input type="submit" name="tombol_tambah" value="Kirim" class="btn btn-success float-end">
            </form>
            <?php
            } elseif (@$_POST['kategori_pembeli'] == 'umum') {
                $_SESSION['kategori'] = $_POST['kategori_pembeli'];
                echo "<script>window.location.href='../tambah/proses_tambah_transaksi.php'</script>";
            } else {
            ?>
            <form method="post">

                <select name="kategori_pembeli" class="form-select form-select-lg mb-3 mt-5"
                    aria-label="Large select example">
                    <option selected value="umum">Umum</option>
                    <option value="member">Member</option>
                </select>

                <input type="submit" name="kirim" value="Simpan" class="btn btn-success float-end mb-4">
            </form>
            <?php } ?>
        </div>
        <div class="col">
        </div>
    </div>
</div>
