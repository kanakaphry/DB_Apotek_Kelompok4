<?php
class Transaksi
{
    public const STATUS_KERANJANG = 'keranjang';
    public const STATUS_LUNAS     = 'lunas';

    private DBconnection $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAllJoined(): array
    {
        $resp = $this->db->send_query(
            "SELECT id_transaksi, id_karyawan, nama_lengkap, nama_karyawan, tgl_transaksi,
                    kategori_pembeli, status, bayar, kembali,
                    CASE WHEN status = 'lunas' THEN total_bayar
                         ELSE (SELECT COALESCE(SUM(d.total), 0) FROM tb_detail_trx d
                               WHERE d.id_trx = tb_transaksi.id_transaksi)
                    END AS total_bayar
             FROM tb_transaksi
             INNER JOIN tb_pembeli USING(id_pembeli)
             LEFT JOIN tb_karyawan USING(id_karyawan)
             ORDER BY id_transaksi DESC"
        );
        return $resp->status ? $resp->data : [];
    }

    public function getByIdJoined(int $id): ?array
    {
        $resp = $this->db->send_query(
            "SELECT * FROM tb_transaksi
             JOIN tb_pembeli USING(id_pembeli)
             LEFT JOIN tb_karyawan USING(id_karyawan)
             WHERE id_transaksi = $1",
            [$id]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function create(int $id_pembeli, int $id_karyawan, string $tgl, string $kategori): int
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_transaksi (id_pembeli, id_karyawan, tgl_transaksi, kategori_pembeli, status, total_bayar, bayar, kembali)
             VALUES ($1, $2, $3, $4, 'keranjang', 0, 0, 0) RETURNING id_transaksi",
            [$id_pembeli, $id_karyawan, $tgl, $kategori]
        );
        if (!$resp->status || empty($resp->data)) {
            return 0;
        }
        return (int) $resp->data[0]['id_transaksi'];
    }

    public function createSelfCheckout(int $id_pembeli, string $tgl): int
    {
        $resp = $this->db->send_query(
            "INSERT INTO tb_transaksi (id_pembeli, id_karyawan, tgl_transaksi, kategori_pembeli, status, total_bayar, bayar, kembali)
             VALUES ($1, NULL, $2, 'member', 'keranjang', 0, 0, 0) RETURNING id_transaksi",
            [$id_pembeli, $tgl]
        );
        if (!$resp->status || empty($resp->data)) {
            return 0;
        }
        return (int) $resp->data[0]['id_transaksi'];
    }

    public function getPendingByPembeli(int $id_pembeli): ?array
    {
        $resp = $this->db->send_query(
            "SELECT id_transaksi FROM tb_transaksi
             WHERE id_pembeli = $1 AND id_karyawan IS NULL AND status = 'keranjang'
             ORDER BY id_transaksi DESC LIMIT 1",
            [$id_pembeli]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    public function getPaidByPembeli(int $id_pembeli): array
    {
        $resp = $this->db->send_query(
            "SELECT t.id_transaksi, t.tgl_transaksi, t.total_bayar, t.bayar, t.kembali, k.nama_karyawan
             FROM tb_transaksi t
             LEFT JOIN tb_karyawan k ON k.id_karyawan = t.id_karyawan
             WHERE t.id_pembeli = $1 AND t.status = 'lunas'
             ORDER BY t.id_transaksi DESC",
            [$id_pembeli]
        );
        return $resp->status ? $resp->data : [];
    }

    public function getByIdForPembeli(int $id_transaksi, int $id_pembeli): ?array
    {
        $resp = $this->db->send_query(
            "SELECT t.*, k.nama_karyawan
             FROM tb_transaksi t
             LEFT JOIN tb_karyawan k ON k.id_karyawan = t.id_karyawan
             WHERE t.id_transaksi = $1 AND t.id_pembeli = $2 AND t.status = 'lunas'",
            [$id_transaksi, $id_pembeli]
        );
        return ($resp->status && !empty($resp->data)) ? $resp->data[0] : null;
    }

    // Catat pembayaran + tandai lunas. Jangan dipanggil langsung dari halaman;
    // pakai checkout() supaya stok ikut dikurangi.
    public function updatePayment(int $id, float $total, float $bayar, float $kembali): bool
    {
        $resp = $this->db->send_query(
            "UPDATE tb_transaksi SET total_bayar = $1, bayar = $2, kembali = $3, status = 'lunas' WHERE id_transaksi = $4",
            [$total, $bayar, $kembali, $id]
        );
        return $resp->status;
    }

    public function checkout(int $id_transaksi, float $bayar): array
    {
        $gagal = fn(string $pesan) => ['ok' => false, 'message' => $pesan, 'total' => 0.0, 'kembali' => 0.0];

        $obatModel   = new Obat();
        $detailModel = new DetailTransaksi();

        try {
            $this->db->mulai_transaksi();

            $lock = $this->db->send_query(
                "SELECT status FROM tb_transaksi WHERE id_transaksi = $1 FOR UPDATE",
                [$id_transaksi]
            );
            if (!$lock->status || empty($lock->data)) {
                $this->db->rollback();
                return $gagal('Transaksi tidak ditemukan.');
            }
            if ($lock->data[0]['status'] !== self::STATUS_KERANJANG) {
                $this->db->rollback();
                return $gagal('Transaksi ini sudah dibayar.');
            }

            $items = $detailModel->getItemsForCheckout($id_transaksi);
            if (empty($items)) {
                $this->db->rollback();
                return $gagal('Belum ada obat di transaksi ini.');
            }

            $total = 0.0;
            foreach ($items as $item) {
                $total += (float) $item['subtotal'];
            }

            if ($bayar < $total) {
                $this->db->rollback();
                return $gagal('Uang tidak cukup. Total yang harus dibayar Rp' . number_format($total, 0, ',', '.') . '.');
            }

            foreach ($items as $item) {
                if (!$obatModel->decreaseStock((int) $item['id_obat'], (int) $item['qty'])) {
                    $this->db->rollback();
                    return $gagal("Stok {$item['namaobat']} tidak mencukupi untuk {$item['qty']} item. Kurangi jumlahnya lalu bayar lagi.");
                }
            }

            $kembali = $bayar - $total;
            if (!$this->updatePayment($id_transaksi, $total, $bayar, $kembali)) {
                $this->db->rollback();
                return $gagal('Gagal menyimpan pembayaran.');
            }

            $this->db->commit();
            return ['ok' => true, 'message' => 'Pembayaran berhasil.', 'total' => $total, 'kembali' => $kembali];
        } catch (DatabaseException $e) {
            $this->db->rollback();
            return $gagal('Terjadi kesalahan database: ' . $e->getMessage());
        }
    }

    // Dipakai saat menghapus karyawan, supaya detail transaksi terkait ikut terhapus.
    public function getIdsByKaryawan(int $id_karyawan): array
    {
        $resp = $this->db->send_query("SELECT id_transaksi FROM tb_transaksi WHERE id_karyawan = $1", [$id_karyawan]);
        return $resp->status ? array_column($resp->data, 'id_transaksi') : [];
    }

    public function getIdsByPembeli(int $id_pembeli): array
    {
        $resp = $this->db->send_query("SELECT id_transaksi, bayar FROM tb_transaksi WHERE id_pembeli = $1", [$id_pembeli]);
        return $resp->status ? $resp->data : [];
    }

    public function deleteByPembeli(int $id_pembeli): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_transaksi WHERE id_pembeli = $1", [$id_pembeli]);
        return $resp->status;
    }

    public function deleteByKaryawan(int $id_karyawan): bool
    {
        $resp = $this->db->send_query("DELETE FROM tb_transaksi WHERE id_karyawan = $1", [$id_karyawan]);
        return $resp->status;
    }
}
