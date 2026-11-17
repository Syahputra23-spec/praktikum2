<?php
require_once __DIR__ . '/config.php';
auth_check();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($mysqli, "DELETE FROM alat WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header("Location: index.php?status=deleted");
        exit;
    }
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}
header("Location: index.php");
exit;
?>