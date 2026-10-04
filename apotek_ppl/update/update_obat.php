<?php

if (!isset($koneksi)) {
    require_once __DIR__ . '/../koneksi.php';
    if (!Auth::checkApoteker()) {
        echo "<script>alert('Anda Belum Login'); window.location.href = '../login.php'</script>";
        exit;
    }
}

$obatModel = new Obat();

$id_obat   = (int) $_GET['id_obat'];
$data_obat = $obatModel->getById($id_obat);

if (isset($_POST['update'])) {
    $id_obat_post  = (int) $_POST['id_obat'];
    $nama_obat     = $_POST['nama_obat'];
    $kategori_obat = $_POST['kategori_obat'];
    $harga_jual    = $_POST['harga_jual'];
    $harga_beli    = $_POST['harga_beli'];
    $stock_obat    = $_POST['stock_obat'];
    $keterangan    = $_POST['keterangan'];

    $hasil = $obatModel->update($id_obat_post, $nama_obat, $kategori_obat, (float) $harga_jual, (float) $harga_beli, (int) $stock_obat, $keterangan);

    if (!$hasil) {
        echo "<script>alert('Data Gagal Masuk'); window.location.href = 'dashboard.php?page=update_obat&id_obat=" . $id_obat_post . "' </script>";
    } else {
        echo "<script>alert('Data Berhasil Diubah'); window.location.href = 'dashboard.php?page=obat'</script>";
    }
}

?>
<form action="" method="post">

    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="col-md-6 col-sm-12">
                <input type="hidden" name="id_obat" value="<?= $data_obat['id_obat']; ?>">
                <div class="form-floating mb-3 mt-5">
                    <input type="text" name="nama_obat" class="form-control" id="floatingInput"
                        placeholder="Nama Obat" value="<?= htmlspecialchars($data_obat['namaobat']) ?>">
                    <label for="floatingInput">Nama Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="kategori_obat" class="form-control" id="floatingInput"
                        placeholder="Kategori Obat" value="<?= htmlspecialchars($data_obat['kategoriobat']) ?>">
                    <label for="floatingInput">Kategori Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="harga_jual" class="form-control" id="floatingInput"
                        placeholder="Harga Jual" value="<?= htmlspecialchars($data_obat['harga_jual']) ?>">
                    <label for="floatingInput">Harga Jual</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="harga_beli" class="form-control" id="floatingInput"
                        placeholder="Harga Beli" value="<?= htmlspecialchars($data_obat['harga_beli']) ?>">
                    <label for="floatingInput">Harga Beli</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" name="stock_obat" class="form-control" id="floatingInput"
                        placeholder="Stok Obat" value="<?= htmlspecialchars($data_obat['stock_obat']) ?>">
                    <label for="floatingInput">Stok Obat</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="keterangan" class="form-control" id="floatingInput"
                        placeholder="Keterangan" value="<?= htmlspecialchars($data_obat['keterangan']) ?>">
                    <label for="floatingInput">Keterangan</label>
                </div>
                <input type="submit" name="update" value="Simpan" class="btn btn-success float-end">
            </div>
            <div class="col">
            </div>
        </div>
    </div>

</form>
