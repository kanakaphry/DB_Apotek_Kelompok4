<?php
class Pembeli
{
    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAll(): array
    {
        $resp = $this->db->send_query("SELECT * FROM tb_pembeli ORDER BY id_pembeli");
        return $resp->status ? $resp->data : [];
    }

    public function getById(int $id): ?array
    {
        $resp = $this->db->send_query("SELECT * FROM tb_pembeli WHERE id_pembeli = $1", [$id]);
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function getIdByName(string $nama): ?array
    {
        $resp = $this->db->send_query("SELECT id_pembeli FROM tb_pembeli WHERE nama_lengkap = $1", [$nama]);
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function getAllNames(): array
    {
        $resp = $this->db->send_query("SELECT nama_lengkap FROM tb_pembeli ORDER BY nama_lengkap");
        return $resp->status ? $resp->data : [];
    }

    // Dipakai apoteker menambah pembeli manual di kasir (tanpa akun login).
    public function create(string $nama, string $alamat, string $telfon, int $usia): bool
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_pembeli (nama_lengkap, alamat, telfon, usia) VALUES ($1, $2, $3, $4)",
            [$nama, $alamat, $telfon, $usia]
        );
        return $resp->status;
    }

    public function update(int $id, string $nama, string $alamat, string $telfon, int $usia): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_pembeli SET nama_lengkap = $1, alamat = $2, telfon = $3, usia = $4 WHERE id_pembeli = $5",
            [$nama, $alamat, $telfon, $usia, $id]
        );
        return $resp->status;
    }

    public function delete(int $id): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_pembeli WHERE id_pembeli = $1", [$id]);
        return $resp->status;
    }

    // ===== Akun login pembeli (registrasi mandiri) =====

    public function usernameExists(string $username): bool
    {
        $resp = $this->db->send_query("SELECT id_pembeli FROM tb_pembeli WHERE username = $1", [$username]);
        return $resp->status && !empty($resp->data);
    }

    // Registrasi mandiri: pembeli baru sekaligus punya akun login.
    public function registerAccount(
        string $nama,
        string $alamat,
        string $telfon,
        int $usia,
        string $username,
        string $password
    ): ?array {
        $resp = $this->db->send_query(
            "INSERT INTO tb_pembeli (nama_lengkap, alamat, telfon, usia, username, password)
             VALUES ($1, $2, $3, $4, $5, $6) RETURNING id_pembeli",
            [$nama, $alamat, $telfon, $usia, $username, $password]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    // Login pembeli: hanya baris yang punya username (bukan pembeli input manual) yang bisa login.
    public function attempt(string $username, string $password): ?array
    {
        $resp = $this->db->send_query(
            "SELECT id_pembeli, nama_lengkap, username
             FROM tb_pembeli
             WHERE username = $1 AND password = $2",
            [$username, $password]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }
}
