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
$status = $_GET['status'] ?? '';
$emailInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailInput = trim($_POST['email'] ?? '');
    $passwordInput = trim($_POST['password'] ?? '');

    if ($emailInput === '' || $passwordInput === '') {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        // Pastikan skema database dan default admin sudah siap
        ensure_database_schema($mysqli);

        $stmt = mysqli_prepare($mysqli, "SELECT * FROM `users` WHERE `email` = ? OR `nama` = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $emailInput, $emailInput);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmt);

            if ($user) {
                $userPass = $user['password'] ?? '';
                // Verifikasi password (dukung hash bcrypt, plain text, maupun md5)
                $isValid = password_verify($passwordInput, $userPass)
                        || ($passwordInput === $userPass)
                        || (md5($passwordInput) === $userPass);

                if ($isValid) {
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'nama'  => $user['nama'] ?? 'Administrator',
                        'email' => $user['email'] ?? $emailInput,
                        'role'  => $user['role'] ?? 'Administrator'
                    ];

                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_write_close();
                    }
                    header("Location: index.php?status=login_success");
                    exit;
                } else {
                    $error = 'Kata sandi (password) salah. Silakan coba lagi.';
                }
            } else {
                $error = 'Akun dengan email "' . htmlspecialchars($emailInput) . '" tidak ditemukan. Silakan gunakan tombol demo atau daftar akun baru.';
            }
        } else {
            $error = 'Terjadi kesalahan database: ' . htmlspecialchars(mysqli_error($mysqli));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem | SIMRS Elektromedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        
        <!-- Sisi Kiri: Branding & Informasi -->
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
                <h3>Portal Manajemen Alat Kesehatan & Elektromedis</h3>
                <p>Platform terintegrasi pengelolaan inventaris, pemeliharaan, serta monitoring fasilitas alat elektromedis rumah sakit.</p>
                
                <div class="auth-feature-list">
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Inventarisasi alat elektromedis realtime</span>
                    </div>
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Pelacakan lokasi bangsal & ruangan</span>
                    </div>
                    <div class="feature-item">
                        <span class="feat-icon">✓</span>
                        <span>Keamanan data terenkripsi & standar SIMRS</span>
                    </div>
                </div>
            </div>

            <div class="auth-brand-footer">
                <span>© <?= date('Y') ?> SIMRS Elektromedis. Seluruh hak cipta dilindungi.</span>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="auth-form-side">
            <div class="auth-form-header">
                <h2>Selamat Datang</h2>
                <p>Masukkan email dan kata sandi Anda untuk mengakses dashboard inventaris.</p>
            </div>

            <!-- Pesan Notifikasi Status / Error -->
            <?php if (!empty($error)): ?>
                <div class="notice notice-error">
                    <span class="notice-icon">⚠</span>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>

            <?php if ($status === 'logout'): ?>
                <div class="notice notice-info">
                    <span class="notice-icon">ℹ</span>
                    <div>Anda telah berhasil keluar dari sistem.</div>
                </div>
            <?php elseif ($status === 'need_login'): ?>
                <div class="notice notice-warning">
                    <span class="notice-icon">🔒</span>
                    <div>Sesi berakhir atau belum masuk. Silakan login terlebih dahulu.</div>
                </div>
            <?php elseif ($status === 'registered'): ?>
                <div class="notice notice-success">
                    <span class="notice-icon">✓</span>
                    <div>Akun baru berhasil dibuat! Silakan masuk dengan email & kata sandi Anda.</div>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="login.php" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">✉</span>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control with-icon"
                               placeholder="contoh: admin@simrs.com"
                               value="<?= htmlspecialchars($emailInput) ?>"
                               required
                               autocomplete="email"
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Kata Sandi (Password)</label>
                    </div>
                    <div class="input-icon-wrapper">
                        <span class="field-icon">🔒</span>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control with-icon"
                               placeholder="Masukkan kata sandi..."
                               required
                               autocomplete="current-password">
                        <button type="button" class="btn-toggle-password" id="togglePassword" title="Lihat/Sembunyikan Kata Sandi">
                            👁
                        </button>
                    </div>
                </div>

                <div class="auth-meta-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" checked>
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <span>Masuk ke Dashboard</span>
                    <span class="btn-arrow">→</span>
                </button>
            </form>

            <!-- Akun Demo Cepat (Quick Fill) -->
            <div class="demo-box">
                <div class="demo-header">
                    <span class="demo-badge">💡 AKUN DEMO RESMI</span>
                    <button type="button" class="btn-fill-demo" id="btnFillDemo">
                        Gunakan Akun Ini
                    </button>
                </div>
                <div class="demo-credentials">
                    <div>Email: <strong id="demoEmail">admin@simrs.com</strong></div>
                    <div>Password: <strong id="demoPass">admin123</strong></div>
                </div>
            </div>

            <!-- Footer Tautan Registrasi -->
            <div class="auth-switch">
                Belum memiliki akun pengguna? 
                <a href="register.php" class="link-primary">Daftar Akun Baru</a>
            </div>

        </div>

    </div>
</div>

<script>
// Toggle Show / Hide Password
const toggleBtn = document.getElementById('togglePassword');
const passField = document.getElementById('password');

if (toggleBtn && passField) {
    toggleBtn.addEventListener('click', function() {
        const type = passField.getAttribute('type') === 'password' ? 'text' : 'password';
        passField.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁' : '🙈';
    });
}

// 1-Click Autofill Akun Demo
const btnFillDemo = document.getElementById('btnFillDemo');
if (btnFillDemo) {
    btnFillDemo.addEventListener('click', function() {
        document.getElementById('email').value = 'admin@simrs.com';
        document.getElementById('password').value = 'admin123';
        document.getElementById('password').focus();
    });
}
</script>

</body>
</html>
