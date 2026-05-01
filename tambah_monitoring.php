<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid       = (int)$_SESSION['user_id'];
    $lokasi    = trim($_POST['lokasi_cctv']);
    $waktu     = trim($_POST['waktu_monitoring']);
    $jumlah    = trim($_POST['jumlah_kendaraan']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($lokasi) || empty($waktu) || $jumlah === '' || empty($deskripsi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!ctype_digit($jumlah) && !(is_numeric($jumlah) && (int)$jumlah >= 0)) {
        $error = 'Jumlah kendaraan harus berupa angka.';
    } elseif (!isset($_FILES['foto_bukti']) || $_FILES['foto_bukti']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File bukti wajib diunggah.';
    } else {
        $jumlah = (int)$jumlah;

        if ($jumlah <= 20) {
            $status = 'Lancar';
        } elseif ($jumlah <= 50) {
            $status = 'Padat';
        } else {
            $status = 'Macet';
        }

        $ext_ok  = ['jpg', 'jpeg', 'png'];
        $ext     = strtolower(pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $ext_ok)) {
            $error = 'Format file tidak diizinkan. Gunakan jpg, jpeg, atau png.';
        } else {
            $filename = uniqid('cctv_') . '.' . $ext;
            $dest     = __DIR__ . '/uploads/' . $filename;

            if (!move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $dest)) {
                $error = 'Gagal mengupload file.';
            } else {
                $lok = mysqli_real_escape_string($conn, $lokasi);
                $wkt = mysqli_real_escape_string($conn, $waktu);
                $dsk = mysqli_real_escape_string($conn, $deskripsi);
                $fnm = mysqli_real_escape_string($conn, $filename);
                $sts = mysqli_real_escape_string($conn, $status);

                $q = mysqli_query($conn,
                    "INSERT INTO monitoring (user_id, lokasi_cctv, waktu_monitoring, jumlah_kendaraan, status_kemacetan, deskripsi, foto_bukti)
                     VALUES ($uid, '$lok', '$wkt', $jumlah, '$sts', '$dsk', '$fnm')"
                );

                if ($q) {
                    header('Location: monitoring.php');
                    exit;
                } else {
                    $error = 'Gagal menyimpan data ke database.';
                }
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
    <title>Tambah Monitoring - SmartTraffic Cam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require 'navbar.php'; ?>
<div class="page-wrapper">
    <a href="monitoring.php" class="back-link">&larr; Kembali ke Monitoring</a>
    <div class="page-title">Tambah Data Monitoring</div>
    <div class="page-subtitle">Catat hasil pemantauan CCTV secara manual</div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card form-max">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Lokasi CCTV</label>
                    <input type="text" name="lokasi_cctv" placeholder="Contoh: Simpang Lima, Pasar Kota" value="<?= htmlspecialchars($_POST['lokasi_cctv'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Waktu Monitoring</label>
                    <input type="datetime-local" name="waktu_monitoring" value="<?= htmlspecialchars($_POST['waktu_monitoring'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Jumlah Kendaraan</label>
                    <input type="number" name="jumlah_kendaraan" min="0" placeholder="Masukkan jumlah kendaraan" value="<?= htmlspecialchars($_POST['jumlah_kendaraan'] ?? '') ?>">
                    <div class="hint-badges">
                        <span class="badge badge-lancar">0 - 20 = Lancar</span>
                        <span class="badge badge-padat">21 - 50 = Padat</span>
                        <span class="badge badge-macet">&gt; 50 = Macet</span>
                    </div>
                    <div class="form-hint">Status kemacetan ditentukan otomatis berdasarkan jumlah.</div>
                </div>
                <div class="form-group">
                    <label>Deskripsi Kondisi Lalu Lintas</label>
                    <textarea name="deskripsi" placeholder="Jelaskan kondisi lalu lintas secara singkat"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto Bukti CCTV</label>
                    <input type="file" name="foto_bukti" accept=".jpg,.jpeg,.png">
                    <div class="form-hint">Format yang diizinkan: jpg, jpeg, png.</div>
                </div>
                <div class="divider"></div>
                <button type="submit" class="btn btn-primary btn-block">Simpan Data Monitoring</button>
            </form>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
</body>
</html>
