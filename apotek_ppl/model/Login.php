<?php
class Login
{
    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function attempt(string $username, string $password): ?array
    {
        $resp = $this->db->send_query(
            "SELECT id_karyawan, username, password FROM tb_login WHERE username = $1 AND password = $2",
            [$username, $password]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function usernameExists(string $username): bool
    {
        $resp = $this->db->send_query("SELECT username FROM tb_login WHERE username = $1", [$username]);
        return $resp->status && !empty($resp->data);
    }

    public function getByKaryawanId(int $id_karyawan): ?array
    {
        $resp = $this->db->send_query("SELECT * FROM tb_login WHERE id_karyawan = $1", [$id_karyawan]);
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function register(string $username, string $password, int $id_karyawan): bool
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_login (username, password, id_karyawan) VALUES ($1, $2, $3)",
            [$username, $password, $id_karyawan]
        );
        return $resp->status;
    }

    // Update akun tanpa mengganti password
    public function updateAccount(int $id_karyawan, string $username): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_login SET username = $1 WHERE id_karyawan = $2",
            [$username, $id_karyawan]
        );
        return $resp->status;
    }

    // Update akun sekaligus ganti password
    public function updateAccountWithPassword(int $id_karyawan, string $username, string $password): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_login SET username = $1, password = $2 WHERE id_karyawan = $3",
            [$username, $password, $id_karyawan]
        );
        return $resp->status;
    }

    public function deleteByKaryawanId(int $id_karyawan): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_login WHERE id_karyawan = $1", [$id_karyawan]);
        return $resp->status;
    }

    public function getAllWithKaryawan(): array
    {
        $resp = $this->db->send_query(
            "SELECT id_karyawan, nama_karyawan, username, alamat, tefon
             FROM tb_login JOIN tb_karyawan USING(id_karyawan)
             ORDER BY id_karyawan"
        );
        return $resp->status ? $resp->data : [];
    }
}
