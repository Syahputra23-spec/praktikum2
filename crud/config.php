<?php
/**
 * Konfigurasi Database & Autentikasi SIMRS
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$databaseHost = '127.0.0.1';
$databaseName = 'simrs';
$databaseUsername = 'root';
$databasePassword = '';

// 1. Coba koneksi langsung ke database 'simrs'
$mysqli = @mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (!$mysqli) {
    // Fallback coba ke localhost
    $mysqli = @mysqli_connect('localhost', $databaseUsername, $databasePassword, $databaseName);
}

// 2. Jika database 'simrs' belum ada di MySQL, coba buat otomatis
if (!$mysqli) {
    $connRoot = @mysqli_connect($databaseHost, $databaseUsername, $databasePassword);
    if (!$connRoot) {
        $connRoot = @mysqli_connect('localhost', $databaseUsername, $databasePassword);
    }

    if ($connRoot) {
        mysqli_query($connRoot, "CREATE DATABASE IF NOT EXISTS `$databaseName` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        mysqli_close($connRoot);

        // Konek kembali ke simrs
        $mysqli = @mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);
        if (!$mysqli) {
            $mysqli = @mysqli_connect('localhost', $databaseUsername, $databasePassword, $databaseName);
        }
    }
}

// 3. Jika tetap gagal koneksi
if (!$mysqli) {
    die("
    <div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;padding:30px;max-width:550px;margin:60px auto;border:1px solid #fecaca;background:#fff5f5;border-radius:12px;color:#991b1b;box-shadow:0 4px 15px rgba(0,0,0,0.06);'>
        <h3 style='margin-top:0;font-size:18px;'>⚠️ Tidak Dapat Terhubung ke MySQL</h3>
        <p style='font-size:14px;color:#7f1d1d;line-height:1.6;'>
            Koneksi ke MySQL terputus. Pastikan modul <strong>MySQL</strong> pada <strong>XAMPP Control Panel</strong> sudah dalam status <strong>Running (Start)</strong>.
        </p>
        <div style='font-size:12px;background:#fee2e2;padding:10px;border-radius:6px;word-break:break-all;color:#b91c1c;'>
            Detail Error: " . htmlspecialchars(mysqli_connect_error()) . "
        </div>
        <div style='margin-top:15px;text-align:right;'>
            <button onclick='location.reload()' style='background:#ef4444;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;'>Coba Lagi</button>
        </div>
    </div>
    ");
}

mysqli_set_charset($mysqli, 'utf8mb4');

// Pastikan tabel users & alat selalu ada dan kolomnya sesuai
ensure_database_schema($mysqli);

/**
 * Memastikan tabel users dan alat ada serta kolomnya lengkap
 */
function ensure_database_schema($conn) {
    // 1. Tabel Users
    $sqlUsers = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama` VARCHAR(100) NOT NULL DEFAULT 'Administrator',
        `email` VARCHAR(100) NOT NULL DEFAULT '',
        `password` VARCHAR(255) NOT NULL DEFAULT '',
        `role` VARCHAR(50) NOT NULL DEFAULT 'Administrator',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $sqlUsers);

    // Periksa kolom tabel users bila tabel sudah ada dari proyek sebelumnya
    $cols = [];
    $colRes = mysqli_query($conn, "SHOW COLUMNS FROM `users`");
    if ($colRes) {
        while ($c = mysqli_fetch_assoc($colRes)) {
            $cols[] = strtolower($c['Field']);
        }
    }

    if (!in_array('email', $cols)) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) NOT NULL DEFAULT ''");
    }
    if (!in_array('nama', $cols)) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `nama` VARCHAR(100) NOT NULL DEFAULT 'Administrator'");
    }
    if (!in_array('password', $cols)) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `password` VARCHAR(255) NOT NULL DEFAULT ''");
    }
    if (!in_array('role', $cols)) {
        @mysqli_query($conn, "ALTER TABLE `users` ADD COLUMN `role` VARCHAR(50) NOT NULL DEFAULT 'Administrator'");
    }

    // 2. Tabel Alat
    $sqlAlat = "CREATE TABLE IF NOT EXISTS `alat` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_alat` VARCHAR(150) NOT NULL,
        `tahun` INT NOT NULL,
        `merek` VARCHAR(100) NOT NULL,
        `lokasi` VARCHAR(100) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $sqlAlat);

    // 3. Pastikan user default admin ada
    $checkAdmin = mysqli_query($conn, "SELECT `id` FROM `users` WHERE `email` = 'admin@simrs.com' LIMIT 1");
    if ($checkAdmin && mysqli_num_rows($checkAdmin) === 0) {
        $hash = password_hash("admin123", PASSWORD_DEFAULT);
        $insertAdmin = "INSERT INTO `users` (`nama`, `email`, `password`, `role`) 
                        VALUES ('Administrator SIMRS', 'admin@simrs.com', '$hash', 'Super Admin')";
        @mysqli_query($conn, $insertAdmin);
    }
}

/**
 * Helper untuk proteksi halaman yang butuh login
 */
function auth_check() {
    if (empty($_SESSION['user'])) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header("Location: login.php?status=need_login");
        exit;
    }
}

/**
 * Helper status login
 */
function is_logged_in() {
    return !empty($_SESSION['user']);
}

/**
 * Helper data user yang sedang login
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}
?>