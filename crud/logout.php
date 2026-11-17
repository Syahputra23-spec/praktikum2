<?php
require_once __DIR__ . '/config.php';

// Menghapus data sesi
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
    session_write_close();
}

header("Location: login.php?status=logout");
exit;
?>