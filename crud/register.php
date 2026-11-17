<?php
require_once __DIR__ . '/config.php';

// Jika sudah login, langsung ke dashboard
if (is_logged_in()) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header("Location: index.php");
    exit;
}

$error = '';
$namaInput = '';
$emailInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaInput     = trim($_POST['nama'] ?? '');
    $emailInput    = trim($_POST['email'] ?? '');
    $passwordInput = trim($_POST['password'] ?? '');
    $confirmPass   = trim($_POST['confirm_password'] ?? '');

    if ($namaInput === '' || $emailInput === '' || $passwordInput === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format alamat email tidak valid.';
    } elseif (strlen($passwordInput) < 6) {
        $error = 'Kata sandi minimal harus 6 karakter.';
    } elseif ($passwordInput !== $confirmPass) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        // Cek apakah email sudah terdaftar
        $checkStmt = mysqli_prepare($mysqli, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($checkStmt, "s", $emailInput);
        mysqli_stmt_execute($checkStmt);
        $checkRes = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($checkRes) > 0) {
            $error = 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk.';
            mysqli_stmt_close($checkStmt);
        } else {
            mysqli_stmt_close($checkStmt);

            // Hash password dan simpan
            $hashedPass = password_hash($passwordInput, PASSWORD_DEFAULT);
            $role = 'Staf Elektromedis';

            $insertStmt = mysqli_prepare($mysqli, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            if ($insertStmt) {
                mysqli_stmt_bind_param($insertStmt, "ssss", $namaInput, $emailInput, $hashedPass, $role);
                if (mysqli_stmt_execute($insertStmt)) {
                    mysqli_stmt_close($insertStmt);
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_write_close();
                    }
                    header("Location: login.php?status=registered");
                    exit;
                } else {
                    $error = 'Gagal mendaftarkan akun: ' . mysqli_stmt_error($insertStmt);
                }
                mysqli_stmt_close($insertStmt);
            } else {
                $error = 'Gagal memproses pendaftaran akun.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna Baru | SIMRS Elektromedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        
        <!-- Sisi Kiri: Branding -->
        <div class="auth-brand-side">
            <div class="auth-brand-header">
                <div class="brand-logo-circle">
                    <span>✚</span>
                </div>
                <div>
                    <h2 class="brand-title">SIMRS</h2>
                    <span class="brand-tag">Sistem Elektromedis</span>
                </div>
            </div>

            <div class="auth-hero-content">
                <h3>Registrasi Akun Petugas Rumah Sakit</h3>
                <p>Buat akun baru untuk mengelola data inventaris elektromedis, mencatat alat baru, serta memperbarui riwayat penempatan ruangan.</p>
                
                <div class="auth-feature-list">
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Akses penuh ke manajemen inventaris</span>
                    </div>
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Pencatatan data alat & spesifikasi</span>
                    </div>
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Pemantauan status operasional berkala</span>
                    </div>
                </div>
            </div>

            <div class="auth-brand-footer">
                <span>© <?= date('Y') ?> SIMRS Elektromedis. Seluruh hak cipta dilindungi.</span>
            </div>
        </div>

        <!-- Sisi Kanan: Form Registrasi -->
        <div class="auth-form-side">
            <div class="auth-form-header">
                <h2>Buat Akun Baru</h2>
                <p>Daftarkan email dan kata sandi Anda untuk mendapatkan akses ke sistem.</p>
            </div>

            <!-- Pesan Notifikasi Error -->
            <?php if (!empty($error)): ?>
                <div class="notice notice-error">
                    <span class="notice-icon">⚠</span>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="nama">Nama Lengkap & Gelar</label>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">👤</span>
                        <input type="text"
                               id="nama"
                               name="nama"
                               class="form-control with-icon"
                               placeholder="contoh: Dr. Rian Pratama, S.Tr.Kes"
                               value="<?= htmlspecialchars($namaInput) ?>"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">✉</span>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control with-icon"
                               placeholder="contoh: petugas@simrs.com"
                               value="<?= htmlspecialchars($emailInput) ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi (Minimal 6 karakter)</label>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">🔒</span>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control with-icon"
                               placeholder="Buat kata sandi aman..."
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Kata Sandi</label>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">🔑</span>
                        <input type="password"
                               id="confirm_password"
                               name="confirm_password"
                               class="form-control with-icon"
                               placeholder="Ulangi kata sandi di atas..."
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 25px;">
                    <span>Daftarkan Akun</span>
                    <span class="btn-arrow">✓</span>
                </button>
            </form>

            <div class="auth-switch">
                Sudah memiliki akun? 
                <a href="login.php" class="link-primary">Masuk di sini</a>
            </div>

        </div>

    </div>
</div>

</body>
</html>
