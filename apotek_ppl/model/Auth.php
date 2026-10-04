<?php
/**
 * Auth -> pengelola sesi login.
 * Ada 2 jenis akun yang bisa login:
 *  - "apoteker" (staf, tb_login + tb_karyawan) -> akses penuh ke halaman kasir/admin.
 *  - "pembeli"  (tb_pembeli, punya username & password sendiri) -> hanya bisa
 *    lihat katalog obat & membeli obat lewat halaman miliknya sendiri.
 */
class Auth
{
    private const ROLE_APOTEKER = 'apoteker';
    private const ROLE_PEMBELI  = 'pembeli';

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ===== Akun apoteker (staf) =====
    public static function login(string $username, int $id_karyawan): void
    {
        self::ensureSession();
        $_SESSION['username']    = $username;
        $_SESSION['id_karyawan'] = $id_karyawan;
        $_SESSION['level_user']  = self::ROLE_APOTEKER;
        unset($_SESSION['id_pembeli']);
    }

    // ===== Akun pembeli =====
    public static function loginPembeli(string $username, int $id_pembeli): void
    {
        self::ensureSession();
        $_SESSION['username']   = $username;
        $_SESSION['id_pembeli'] = $id_pembeli;
        $_SESSION['level_user'] = self::ROLE_PEMBELI;
        unset($_SESSION['id_karyawan']);
    }

    public static function logout(): void
    {
        self::ensureSession();
        $_SESSION = [];
        session_destroy();
    }

    // true jika akun manapun (apoteker ATAU pembeli) sedang login
    public static function check(): bool
    {
        self::ensureSession();
        return isset($_SESSION['username'], $_SESSION['level_user']);
    }

    public static function checkApoteker(): bool
    {
        self::ensureSession();
        return self::check() && $_SESSION['level_user'] === self::ROLE_APOTEKER;
    }

    public static function checkPembeli(): bool
    {
        self::ensureSession();
        return self::check() && $_SESSION['level_user'] === self::ROLE_PEMBELI;
    }

    public static function username(): string
    {
        self::ensureSession();
        return $_SESSION['username'] ?? '';
    }

    public static function levelUser(): string
    {
        self::ensureSession();
        return $_SESSION['level_user'] ?? '';
    }

    public static function idKaryawan(): int
    {
        self::ensureSession();
        return (int) ($_SESSION['id_karyawan'] ?? 0);
    }

    public static function idPembeli(): int
    {
        self::ensureSession();
        return (int) ($_SESSION['id_pembeli'] ?? 0);
    }
}
