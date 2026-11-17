<?php
require_once __DIR__ . '/config.php';
auth_check();

// Mengambil data alat dari database
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
if (!$result) {
    die('Gagal mengambil data alat: ' . mysqli_error($mysqli));
}

// Menghitung statistik inventaris
$countRow = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(*) AS total FROM alat"));
$totalAlat = (int) ($countRow['total'] ?? 0);

$merekRow = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT merek) AS total FROM alat"));
$totalMerek = (int) ($merekRow['total'] ?? 0);

$lokasiRow = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT COUNT(DISTINCT lokasi) AS total FROM alat"));
$totalLokasi = (int) ($lokasiRow['total'] ?? 0);

// Status flash message
$status = $_GET['status'] ?? '';

$pageTitle = 'Dashboard Inventaris';
$activePage = 'dashboard';
include 'header.php';
?>

<!-- Alert Notifikasi Status -->
<?php if ($status === 'added'): ?>
    <div class="notice notice-success">
        <span class="notice-icon">✓</span>
        <div><strong>Sukses!</strong> Data alat baru berhasil ditambahkan ke dalam inventaris.</div>
    </div>
<?php elseif ($status === 'updated'): ?>
    <div class="notice notice-success">
        <span class="notice-icon">✓</span>
        <div><strong>Sukses!</strong> Data alat telah berhasil diperbarui.</div>
    </div>
<?php elseif ($status === 'deleted'): ?>
    <div class="notice notice-info">
        <span class="notice-icon">ℹ</span>
        <div><strong>Terhapus!</strong> Data alat telah dihapus dari sistem.</div>
    </div>
<?php elseif ($status === 'login_success'): ?>
    <div class="notice notice-success">
        <span class="notice-icon">👋</span>
        <div><strong>Selamat datang kembali!</strong> Anda berhasil masuk sebagai <?= htmlspecialchars($userName) ?>.</div>
    </div>
<?php endif; ?>

<!-- Header Halaman -->
<div class="page-heading">
    <div>
        <div class="eyebrow">Ringkasan Sistem & Inventaris</div>
        <h1>Dashboard Alat Elektromedis</h1>
        <p>Pantau status, ketersediaan, dan kelola seluruh inventaris fasilitas kesehatan secara efisien.</p>
    </div>
    <div class="heading-actions">
        <a class="btn btn-primary" href="add.php">
            <span>＋</span> Tambah Alat Baru
        </a>
    </div>
</div>

<!-- Grid Kartu Statistik -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Alat Terdaftar</div>
            <div class="stat-value"><?= number_format($totalAlat) ?></div>
            <div class="stat-note">Seluruh inventaris aktif</div>
        </div>
        <div class="stat-icon icon-teal">⚕</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Merek Produsen</div>
            <div class="stat-value"><?= number_format($totalMerek) ?></div>
            <div class="stat-note">Variasi produsen medis</div>
        </div>
        <div class="stat-icon icon-blue">▣</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Lokasi Penempatan</div>
            <div class="stat-value"><?= number_format($totalLokasi) ?></div>
            <div class="stat-note">Ruang & bangsal rumah sakit</div>
        </div>
        <div class="stat-icon icon-purple">⌖</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Status Sistem</div>
            <div class="stat-value stat-online">Aktif</div>
            <div class="stat-note">Database terhubung lancar</div>
        </div>
        <div class="stat-icon icon-green">🏥</div>
    </div>
</div>

<!-- Panel Tabel Inventaris -->
<div class="panel" id="alatTableSection">
    <div class="panel-head">
        <div>
            <div class="panel-title">Data Inventaris Alat Elektromedis</div>
            <div class="panel-subtitle">Daftar lengkap alat, tahun perolehan, merek, dan penempatan ruangan</div>
        </div>

        <div class="panel-actions">
            <label class="search-box">
                <span class="search-icon">⌕</span>
                <input type="search" id="tableSearch" placeholder="Cari nama, merek, lokasi...">
            </label>
        </div>
    </div>

    <div class="table-wrap">
        <table id="alatTable">
            <thead>
                <tr>
                    <th style="width: 60px;">No.</th>
                    <th>Nama Alat</th>
                    <th style="width: 100px;">Tahun</th>
                    <th>Merek</th>
                    <th>Lokasi Ruangan</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 170px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr data-row>
                            <td class="number-cell"><?= $i++ ?></td>
                            <td>
                                <div class="device-cell">
                                    <div class="device-icon">🩺</div>
                                    <div>
                                        <strong class="device-name"><?= htmlspecialchars($row['nama_alat']) ?></strong>
                                        <div class="device-id">ID: #<?= (int)$row['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="year-badge"><?= htmlspecialchars($row['tahun']) ?></span>
                            </td>
                            <td>
                                <span class="device-tag"><?= htmlspecialchars($row['merek']) ?></span>
                            </td>
                            <td>
                                <div class="location-badge">
                                    <span class="loc-pin">📍</span> <?= htmlspecialchars($row['lokasi']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success">● Terverifikasi</span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a class="action-btn action-edit" href="edit.php?id=<?= (int)$row['id'] ?>" title="Edit data">
                                        ✎ Edit
                                    </a>
                                    <a class="action-btn action-delete" href="delete.php?id=<?= (int)$row['id'] ?>" data-confirm="Apakah Anda yakin ingin menghapus alat '<?= htmlspecialchars(addslashes($row['nama_alat'])) ?>'?" title="Hapus data">
                                        🗑 Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <strong>Belum ada data alat terdaftar</strong>
                                <p>Inventaris alat elektromedis saat ini masih kosong. Silakan mulai dengan menambahkan alat pertama.</p>
                                <a href="add.php" class="btn btn-primary btn-sm" style="margin-top: 15px;">
                                    ＋ Tambah Alat Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-foot">
        Menampilkan <strong><?= number_format($totalAlat) ?></strong> data alat elektromedis
    </div>
</div>

<?php include 'footer.php'; ?>
