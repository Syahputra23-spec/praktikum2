
<?php
include_once("config.php");

if (isset($_POST['Submit'])) {

    $nama_alat = $_POST['nama_alat'];
    $tahun = $_POST['tahun'];
    $merek = $_POST['merek'];
    $lokasi = $_POST['lokasi'];

    $stmt = mysqli_prepare(
        $mysqli,
        "INSERT INTO alat (nama_alat, tahun, merek, lokasi)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt, "ssss",
        $nama_alat, $tahun, $merek, $lokasi
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menyimpan data alat.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Alat - SIMRS</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <aside class="sidebar">
        <div class="logo">SIM<span>RS</span></div>
        <div class="menu-title">Menu Utama</div>
        <a href="index.php">▦ Dashboard</a>
        <a href="index.php">▤ Data Alat</a>
        <a href="add.php" class="active">＋ Tambah Alat</a>
    </aside>

    <main class="main">

        <div class="topbar">
            <div>
                <h2>Tambah Alat</h2>
                <p>Masukkan informasi alat elektromedis baru</p>
            </div>
            <div class="admin">👤 Administrator</div>
        </div>

        <div class="form-box">

            <h3 style="margin-bottom:25px;">
                Formulir Data Alat Elektromedis
            </h3>

            <?php if (isset($error)) { ?>
                <p style="color:red;margin-bottom:15px;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php } ?>

            <form method="POST">

                <div class="form-group">
                    <label>Nama Alat</label>
                    <input type="text" name="nama_alat"
                    placeholder="Contoh: ECG" required>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun"
                    min="1901" max="2155"
                    placeholder="Contoh: 2024" required>
                </div>

                <div class="form-group">
                    <label>Merek</label>
                    <input type="text" name="merek"
                    placeholder="Contoh: GE" required>
                </div>

                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi"
                    maxlength="10"
                    placeholder="Contoh: ICU" required>
                </div>

                <div class="form-actions">
                    <button type="submit" name="Submit"
                    class="btn btn-primary">
                        Simpan Data
                    </button>

                    <a href="index.php"
                    class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
