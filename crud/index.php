
<?php
include_once("config.php");

$result = mysqli_query(
    $mysqli,
    "SELECT * FROM alat ORDER BY id DESC"
);

$total = mysqli_num_rows(
    mysqli_query($mysqli, "SELECT id FROM alat")
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard SIMRS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <aside class="sidebar">
        <div class="logo">SIM<span>RS</span></div>

        <div class="menu-title">Menu Utama</div>

        <a href="index.php" class="active">▦ Dashboard</a>
        <a href="index.php">▤ Data Alat</a>
        <a href="add.php">＋ Tambah Alat</a>

        <div class="menu-title">Informasi</div>
        <a href="index.php">ℹ Tentang Aplikasi</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <div>
                <h2>Dashboard SIMRS</h2>
                <p>Sistem Informasi Manajemen Rumah Sakit</p>
            </div>

            <div class="admin">👤 Administrator</div>
        </div>

        <div class="cards">

            <div class="card">
                <div class="symbol">🏥</div>
                <p>Total Alat Elektromedis</p>
                <h2><?php echo $total; ?></h2>
            </div>

            <div class="card">
                <div class="symbol">📋</div>
                <p>Data Inventaris</p>
                <h2><?php echo $total; ?></h2>
            </div>

            <div class="card">
                <div class="symbol">💻</div>
                <p>Status Sistem</p>
                <h2 style="font-size:22px;color:#16a34a;">
                    Aktif
                </h2>
            </div>

        </div>

        <div class="table-box">

            <div class="table-heading">
                <div>
                    <h3>Data Alat Elektromedis</h3>
                    <p style="color:#64748b;font-size:13px;margin-top:5px;">
                        Daftar inventaris alat rumah sakit
                    </p>
                </div>

                <a href="add.php" class="btn btn-primary">
                    + Tambah Alat
                </a>
            </div>

            <table>

                <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Tahun</th>
                    <th>Merek</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $i = 1;

                if (mysqli_num_rows($result) > 0) {

                    while ($data = mysqli_fetch_assoc($result)) {
                ?>

                <tr>
                    <td><?php echo $i++; ?></td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($data['nama_alat']); ?>
                        </strong>
                    </td>

                    <td><?php echo htmlspecialchars($data['tahun']); ?></td>

                    <td><?php echo htmlspecialchars($data['merek']); ?></td>

                    <td><?php echo htmlspecialchars($data['lokasi']); ?></td>

                    <td>
                        <span class="badge">Terdaftar</span>
                    </td>

                    <td>
                        <a class="btn btn-edit"
                           href="edit.php?id=<?php echo $data['id']; ?>">
                           Edit
                        </a>

                        <a class="btn btn-delete"
                           href="delete.php?id=<?php echo $data['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                           Hapus
                        </a>
                    </td>
                </tr>

                <?php
                    }

                } else {
                ?>

                <tr>
                    <td colspan="7" class="empty">
                        Belum ada data alat elektromedis.
                        Silakan tambahkan data baru.
                    </td>
                </tr>

                <?php } ?>

            </table>

        </div>

        <p style="margin-top:25px;color:#94a3b8;font-size:12px;">
            © 2026 SIMRS - Sistem Informasi Manajemen Rumah Sakit
        </p>

    </main>

</div>

</body>
</html>
