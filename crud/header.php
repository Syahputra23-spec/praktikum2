<?php
require_once __DIR__ . '/config.php';
auth_check();

$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';

$user = current_user();
$userName = $user['nama'] ?? 'Administrator';
$userEmail = $user['email'] ?? 'admin@simrs.com';
$userRole = $user['role'] ?? 'Pengelola Inventaris';

// Menghasilkan 2 huruf inisial nama
$nameParts = explode(' ', trim($userName));
$initials = strtoupper(substr($nameParts[0], 0, 1));
if (isset($nameParts[1])) {
    $initials .= strtoupper(substr($nameParts[1], 0, 1));
} elseif (strlen($nameParts[0]) > 1) {
    $initials .= strtoupper(substr($nameParts[0], 1, 1));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | SIMRS Elektromedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php">
            <div class="brand-mark">✚</div>
            <div>
                <strong>SIMRS</strong>
                <small>Elektromedis</small>
            </div>
        </a>

        <div class="sidebar-user-pill">
            <div class="avatar avatar-sm"><?= htmlspecialchars($initials) ?></div>
            <div class="sidebar-user-info">
                <span class="user-name"><?= htmlspecialchars($userName) ?></span>
                <span class="user-badge"><?= htmlspecialchars($userRole) ?></span>
            </div>
        </div>

        <nav>
            <div class="nav-label">MENU UTAMA</div>
            <a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php">
                <span class="nav-icon">▦</span>Dashboard
            </a>
            <a class="nav-link <?= $activePage === 'add' ? 'active' : '' ?>" href="add.php">
                <span class="nav-icon">＋</span>Tambah Alat
            </a>

            <div class="nav-label" style="margin-top:24px">SISTEM & INVENTARIS</div>
            <a class="nav-link <?= $activePage === 'inventory' ? 'active' : '' ?>" href="index.php#alatTable">
                <span class="nav-icon">▤</span>Daftar Inventaris
            </a>

            <div class="nav-label" style="margin-top:24px">AKUN</div>
            <a class="nav-link nav-logout" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                <span class="nav-icon">⎋</span>Logout
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="system-status">
                <span class="status-dot"></span> Sistem Online
            </div>
            Sistem Informasi SIMRS<br>© <?= date('Y') ?> Rumah Sakit
        </div>
    </aside>

    <!-- Overlay untuk mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content Area -->
    <main class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle" aria-label="Buka Menu">☰</button>
                <div>
                    <div class="topbar-title"><?= htmlspecialchars($pageTitle) ?></div>
                    <div class="topbar-sub">Sistem Informasi Manajemen Rumah Sakit - Unit Elektromedis</div>
                </div>
            </div>

            <div class="topbar-right">
                <div class="date-chip">
                    🗓 <?= date('d M Y') ?>
                </div>

                <div class="profile">
                    <div class="avatar"><?= htmlspecialchars($initials) ?></div>
                    <div class="profile-details">
                        <div class="profile-name"><?= htmlspecialchars($userName) ?></div>
                        <div class="profile-role"><?= htmlspecialchars($userEmail) ?></div>
                    </div>
                </div>

                <a href="logout.php" class="btn-topbar-logout" title="Keluar dari sistem" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    <span>⎋</span> Keluar
                </a>
            </div>
        </header>

        <section class="content">
