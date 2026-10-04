<?php

require_once __DIR__ . '/autoload.php';

try {
    $koneksi = new DBconnection();
} catch (DatabaseException $e) {
    die("Gagal terhubung ke database: " . htmlspecialchars($e->getMessage()));
}
