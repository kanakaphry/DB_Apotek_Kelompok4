<?php
class Obat
{
    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAll(): array
    {
        $resp = $this->db->send_query(
            "SELECT id_obat, namaobat, kategoriobat, harga_jual, harga_beli, stock_obat, keterangan
             FROM tb_obat
             ORDER BY id_obat"
        );
        return $resp->status ? $resp->data : [];
    }

    public function getById(int $id): ?array
    {
        $resp = $this->db->send_query(
            "SELECT id_obat, namaobat, kategoriobat, harga_jual, harga_beli, stock_obat, keterangan
             FROM tb_obat WHERE id_obat = $1",
            [$id]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function getByName(string $nama): ?array
    {
        $resp = $this->db->send_query("SELECT id_obat, namaobat, harga_jual, stock_obat FROM tb_obat WHERE namaobat = $1", [$nama]);
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function getAllNames(): array
    {
        $resp = $this->db->send_query("SELECT namaobat, stock_obat FROM tb_obat ORDER BY namaobat");
        return $resp->status ? $resp->data : [];
    }

    public function create(string $nama, string $kategori, float $harga_jual, float $harga_beli, int $stok, string $keterangan): bool
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_obat (namaobat, kategoriobat, harga_jual, harga_beli, stock_obat, keterangan)
             VALUES ($1, $2, $3, $4, $5, $6)",
            [$nama, $kategori, $harga_jual, $harga_beli, $stok, $keterangan]
        );
        return $resp->status;
    }

    public function update(int $id_obat, string $nama, string $kategori, float $harga_jual, float $harga_beli, int $stok, string $keterangan): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_obat SET namaobat = $1, kategoriobat = $2, harga_jual = $3, harga_beli = $4, stock_obat = $5, keterangan = $6
             WHERE id_obat = $7",
            [$nama, $kategori, $harga_jual, $harga_beli, $stok, $keterangan, $id_obat]
        );
        return $resp->status;
    }

    public function updateStock(int $id_obat, int $stok_baru): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_obat SET stock_obat = $1 WHERE id_obat = $2",
            [$stok_baru, $id_obat]
        );
        return $resp->status;
    }

    public function delete(int $id): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_obat WHERE id_obat = $1", [$id]);
        return $resp->status;
    }
    
    public function decreaseStock(int $id_obat, int $qty): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_obat SET stock_obat = stock_obat - $1
             WHERE id_obat = $2 AND stock_obat >= $1
             RETURNING id_obat",
            [$qty, $id_obat]
        );
        return $resp->status && !empty($resp->data);
    }

    public function increaseStock(int $id_obat, int $qty): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_obat SET stock_obat = stock_obat + $1 WHERE id_obat = $2",
            [$qty, $id_obat]
        );
        return $resp->status;
    }
}
