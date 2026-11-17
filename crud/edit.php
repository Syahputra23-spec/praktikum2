<?php
require_once __DIR__ . '/config.php';
auth_check();

$error = '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $merek     = trim($_POST['merek'] ?? '');
    $lokasi    = trim($_POST['lokasi'] ?? '');

    if ($nama_alat === '' || $tahun === '' || $merek === '' || $lokasi === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($tahun, FILTER_VALIDATE_INT) || (int)$tahun < 1901 || (int)$tahun > 2155) {
        $error = 'Tahun harus berupa angka valid antara 1901 sampai 2155.';
    } else {
        $stmt = mysqli_prepare($mysqli, "UPDATE alat SET nama_alat=?, tahun=?, merek=?, lokasi=? WHERE id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $nama_alat, $tahun, $merek, $lokasi, $id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
                header('Location: index.php?status=updated');
                exit;
            } else {
                $error = 'Gagal memperbarui data alat: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Gagal menyiapkan query database: ' . mysqli_error($mysqli);
        }
    }
}

// Ambil data terbaru berdasarkan ID
$stmt = mysqli_prepare($mysqli, "SELECT * FROM alat WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$alat_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$alat_data) {
    http_response_code(404);
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2>Data alat tidak ditemukan</h2><p>Data dengan ID #' . htmlspecialchars($id) . ' tidak ada di database.</p><a href="index.php">Kembali ke dashboard</a></div>');
}

$pageTitle = 'Edit Data Alat';
$activePage = 'dashboard';
include 'header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Dashboard</a>
    <span class="bc-sep">/</span>
    <span>Edit Data Alat</span>
</div>

<div class="page-heading">
    <div>
        <div class="eyebrow">Pembaruan Inventaris</div>
        <h1>Edit Alat: <?= htmlspecialchars($alat_data['nama_alat']) ?></h1>
        <p>Perbarui informasi inventaris untuk unit dengan nomor inventaris #<?= (int)$alat_data['id'] ?>.</p>
    </div>
    <a class="btn btn-light" href="index.php">
        ← Kembali ke Dashboard
    </a>
</div>

<div class="panel form-panel">
    <div class="panel-head">
        <div>
            <div class="panel-title">Formulir Perubahan Data</div>
            <div class="panel-subtitle">ID Inventaris: #<?= (int)$alat_data['id'] ?></div>
        </div>
    </div>

    <div class="form-body">
        <?php if (!empty($error)): ?>
            <div class="notice notice-error">
                <span class="notice-icon">⚠</span>
                <div><strong>Peringatan!</strong> <?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="edit.php?id=<?= (int)$alat_data['id'] ?>" novalidate>
            <input type="hidden" name="id" value="<?= (int)$alat_data['id'] ?>">

            <div class="form-grid">
                <div class="form-group full">
                    <label for="nama_alat">
                        Nama Alat Elektromedis <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="nama_alat"
                           name="nama_alat"
                           type="text"
                           value="<?= htmlspecialchars($_POST['nama_alat'] ?? $alat_data['nama_alat']) ?>"
                           required>
                    <span class="form-hint">Nama lengkap atau kode seri unit alat</span>
                </div>

                <div class="form-group">
                    <label for="tahun">
                        Tahun Pengadaan <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="tahun"
                           name="tahun"
                           type="number"
                           min="1901"
                           max="2155"
                           value="<?= htmlspecialchars($_POST['tahun'] ?? $alat_data['tahun']) ?>"
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
                           value="<?= htmlspecialchars($_POST['merek'] ?? $alat_data['merek']) ?>"
                           required>
                    <span class="form-hint">Pabrikan resmi unit</span>
                </div>

                <div class="form-group full">
                    <label for="lokasi">
                        Lokasi Penempatan / Ruangan <span class="required-star">*</span>
                    </label>
                    <input class="form-control"
                           id="lokasi"
                           name="lokasi"
                           type="text"
                           value="<?= htmlspecialchars($_POST['lokasi'] ?? $alat_data['lokasi']) ?>"
                           required>
                    <span class="form-hint">Ruang atau bangsal tempat unit ditempatkan saat ini</span>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-light" href="index.php">Batal</a>
                <button class="btn btn-primary" type="submit" name="update">
                    ✓ Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
