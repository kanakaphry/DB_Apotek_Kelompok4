<?php
class Karyawan
{
    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAll(): array
    {
        $resp = $this->db->send_query("SELECT * FROM tb_karyawan ORDER BY id_karyawan");
        return $resp->status ? $resp->data : [];
    }

    public function getById(int $id): ?array
    {
        $resp = $this->db->send_query("SELECT * FROM tb_karyawan WHERE id_karyawan = $1", [$id]);
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function create(string $nama, string $alamat, string $tefon): int
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_karyawan (nama_karyawan, alamat, tefon) VALUES ($1, $2, $3) RETURNING id_karyawan",
            [$nama, $alamat, $tefon]
        );
        if (!$resp->status || empty($resp->data)) {
            throw new DatabaseException("Gagal menambah karyawan: " . $resp->message);
        }
        return (int) $resp->data[0]['id_karyawan'];
    }

    public function update(int $id, string $nama, string $alamat, string $tefon): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_karyawan SET nama_karyawan = $1, alamat = $2, tefon = $3 WHERE id_karyawan = $4",
            [$nama, $alamat, $tefon, $id]
        );
        return $resp->status;
    }

    public function delete(int $id): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_karyawan WHERE id_karyawan = $1", [$id]);
        return $resp->status;
    }
}
