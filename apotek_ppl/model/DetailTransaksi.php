<?php
class DetailTransaksi
{
    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getByTransaksi(int $id_trx): array
    {
        $resp = $this->db->send_query(
            "SELECT * FROM tb_detail_trx
             JOIN tb_obat ON tb_detail_trx.id_obat = tb_obat.id_obat
             WHERE id_trx = $1
             ORDER BY id_detail_trx",
            [$id_trx]
        );
        return $resp->status ? $resp->data : [];
    }

    public function getItemsForCheckout(int $id_trx): array
    {
        $resp = $this->db->send_query(
            "SELECT d.id_obat, o.namaobat, SUM(d.jumlah) AS qty, SUM(d.total) AS subtotal
             FROM tb_detail_trx d
             JOIN tb_obat o ON o.id_obat = d.id_obat
             WHERE d.id_trx = $1
             GROUP BY d.id_obat, o.namaobat
             ORDER BY d.id_obat",
            [$id_trx]
        );
        return $resp->status ? $resp->data : [];
    }

    // Total uang transaksi = jumlah kolom "total" (BUKAN kolom "jumlah").
    public function sumByTransaksi(int $id_trx): float
    {
        $resp = $this->db->send_query("SELECT COALESCE(SUM(total), 0) AS total FROM tb_detail_trx WHERE id_trx = $1", [$id_trx]);
        if (!$resp->status || empty($resp->data)) {
            return 0.0;
        }
        return (float) ($resp->data[0]['total'] ?? 0);
    }

    public function countByTransaksi(int $id_trx): int
    {
        $resp = $this->db->send_query("SELECT id_obat FROM tb_detail_trx WHERE id_trx = $1", [$id_trx]);
        return $resp->status ? count($resp->data) : 0;
    }

    // Qty obat tertentu yang sudah ada di transaksi (0 kalau belum ada).
    public function getQtyInTransaksi(int $id_trx, int $id_obat): int
    {
        $resp = $this->db->send_query(
            "SELECT COALESCE(SUM(jumlah), 0) AS qty FROM tb_detail_trx WHERE id_trx = $1 AND id_obat = $2",
            [$id_trx, $id_obat]
        );
        return ($resp->status && !empty($resp->data)) ? (int) $resp->data[0]['qty'] : 0;
    }

    // Tambah obat ke transaksi. Kalau obat yang sama sudah ada, qty-nya
    // DITAMBAH (bukan membuat baris baru) dan subtotal dihitung ulang.
    public function addOrIncrease(int $id_trx, int $id_obat, int $qty, float $harga_satuan): bool
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_detail_trx (id_trx, id_obat, jumlah, harga_satuan, total)
             VALUES ($1, $2, $3, $4, $5)
             ON CONFLICT (id_trx, id_obat) DO UPDATE SET
                 jumlah       = tb_detail_trx.jumlah + EXCLUDED.jumlah,
                 harga_satuan = EXCLUDED.harga_satuan,
                 total        = (tb_detail_trx.jumlah + EXCLUDED.jumlah) * EXCLUDED.harga_satuan",
            [$id_trx, $id_obat, $qty, $harga_satuan, $qty * $harga_satuan]
        );
        return $resp->status;
    }

    public function delete(int $id_detail_trx): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_detail_trx WHERE id_detail_trx = $1", [$id_detail_trx]);
        return $resp->status;
    }

    // Hapus 1 item HANYA jika benar milik transaksi tsb (mencegah menghapus
    // item transaksi lain lewat id di URL).
    public function deleteFromTransaksi(int $id_detail_trx, int $id_trx): bool
    {
        $resp = $this->db->send_query(
            "DELETE FROM tb_detail_trx WHERE id_detail_trx = $1 AND id_trx = $2",
            [$id_detail_trx, $id_trx]
        );
        return $resp->status;
    }

    public function deleteByTransaksi(int $id_trx): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_detail_trx WHERE id_trx = $1", [$id_trx]);
        return $resp->status;
    }
}
