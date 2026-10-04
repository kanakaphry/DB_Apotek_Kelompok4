<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$obatModel = new Obat();

if (isset($_POST['kirim'])) {

    $nama_obat     = $_POST['nama_obat'];
    $kategori_obat = $_POST['kategori_obat'];
    $harga_jual    = $_POST['harga_jual'];
    $harga_beli    = $_POST['harga_beli'];
    $stock_obat    = $_POST['stock_obat'];
    $keterangan    = $_POST['keterangan'];

    $hasil = $obatModel->create($nama_obat, $kategori_obat, (float) $harga_jual, (float) $harga_beli, (int) $stock_obat, $keterangan);

    if (!$hasil) {
        echo "<script>alert('Data Gagal Masuk'); window.location.href = 'dashboard.php?page=tambah_obat' </script>";
    } else {
        echo "<script>alert('Data Berhasil Masuk'); window.location.href = 'dashboard.php?page=obat'</script>";
    }
}
?>

<form action="" method="post">

    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6 col-sm-12">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_obat" class="form-control" id="floatingInput"
                        placeholder="Nama Obat">
                    <label for="floatingInput">Nama Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="kategori_obat" class="form-control" id="floatingInput"
                        placeholder="Kategori Obat">
                    <label for="floatingInput">Kategori Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="harga_jual" class="form-control" id="floatingInput"
                        placeholder="Harga Jual">
                    <label for="floatingInput">Harga Jual</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="harga_beli" class="form-control" id="floatingInput"
                        placeholder="Harga Beli">
                    <label for="floatingInput">Harga Beli</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="stock_obat" class="form-control" id="floatingInput"
                        placeholder="Stok Obat">
                    <label for="floatingInput">Stok Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="keterangan" class="form-control" id="floatingInput"
                        placeholder="Keterangan">
                    <label for="floatingInput">Keterangan</label>
                </div>
                <input type="submit" name="kirim" value="Simpan" class="btn btn-success float-end">
            </div>
            <div class="col">
            </div>
        </div>
    </div>

</form>
