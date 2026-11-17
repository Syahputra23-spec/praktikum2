<?php
require_once __DIR__ . '/config.php';
auth_check();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $merek     = trim($_POST['merek'] ?? '');
    $lokasi    = trim($_POST['lokasi'] ?? '');

    if ($nama_alat === '' || $tahun === '' || $merek === '' || $lokasi === '') {
        $error = 'Semua kolom formulir wajib diisi dengan lengkap.';
    } elseif (!filter_var($tahun, FILTER_VALIDATE_INT) || (int)$tahun < 1901 || (int)$tahun > 2155) {
        $error = 'Tahun pengadaan harus berupa angka valid antara 1901 sampai 2155.';
    } else {
        $stmt = mysqli_prepare($mysqli, "INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $nama_alat, $tahun, $merek, $lokasi);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
                header('Location: index.php?status=added');
                exit;
            } else {
                $error = 'Gagal menyimpan data ke database: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Gagal menyiapkan perintah database: ' . mysqli_error($mysqli);
        }
    }
}

$pageTitle = 'Tambah Alat Elektromedis';
$activePage = 'add';
include 'header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Dashboard</a>
    <span class="bc-sep">/</span>
    <span>Tambah Data Alat</span>
</div>

<div class="page-heading">
    <div>
        <div class="eyebrow">Formulir Inventaris Baru</div>
        <h1>Tambah Alat Elektromedis</h1>
        <p>Lengkapi formulir di bawah ini untuk mencatatkan alat medis baru ke database inventaris rumah sakit.</p>
    </div>
    <a class="btn btn-light" href="index.php">
        ← Kembali ke Dashboard
    </a>
</div>

<div class="panel form-panel">
    <div class="panel-head">
        <div>
            <div class="panel-title">Informasi Detail Alat</div>
            <div class="panel-subtitle">Pastikan data nama, merek, tahun, dan penempatan sesuai dengan fisik alat</div>
        </div>
    </div>

    <div class="form-body">
        <?php if (!empty($error)): ?>
            <div class="notice notice-error">
                <span class="notice-icon">⚠</span>
                <div><strong>Terjadi Kesalahan!</strong> <?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="add.php" novalidate>
            <div class="form-grid">
                <div class="form-group full">
                    <label for="nama_alat">
                        Nama Alat Elektromedis <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="nama_alat"
                           name="nama_alat"
                           type="text"
                           placeholder="Contoh: Patient Monitor, ECG 12-Lead, Ventilator"
                           value="<?= htmlspecialchars($_POST['nama_alat'] ?? '') ?>"
                           required
                           autofocus>
                    <span class="form-hint">Nama resmi atau tipe umum peralatan medis yang didaftarkan</span>
                </div>

                <div class="form-group">
                    <label for="tahun">
                        Tahun Pengadaan / Produksi <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="tahun"
                           name="tahun"
                           type="number"
                           min="1901"
                           max="2155"
                           placeholder="Contoh: 2024"
                           value="<?= htmlspecialchars($_POST['tahun'] ?? '') ?>"
                           required>
                    <span class="form-hint">Rentang tahun valid: 1901 - 2155</span>
                </div>

                <div class="form-group">
                    <label for="merek">
                        Merek / Pabrikan <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="merek"
                           name="merek"
                           type="text"
                           placeholder="Contoh: Mindray, GE Healthcare, Philips, Nihon Kohden"
                           value="<?= htmlspecialchars($_POST['merek'] ?? '') ?>"
                           required>
                    <span class="form-hint">Nama manufaktur atau produsen alat</span>
                </div>

                <div class="form-group full">
                    <label for="lokasi">
                        Lokasi Penempatan / Ruangan <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="lokasi"
                           name="lokasi"
                           type="text"
                           placeholder="Contoh: Ruang ICU Bed 3, IGD, Radiologi, Poli Jantung"
                           value="<?= htmlspecialchars($_POST['lokasi'] ?? '') ?>"
                           required>
                    <span class="form-hint">Nama ruangan, poli, atau bangsal penempatan unit saat ini</span>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-light" href="index.php">Batal</a>
                <button class="btn btn-primary" type="submit">
                    ✓ Simpan ke Inventaris
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
