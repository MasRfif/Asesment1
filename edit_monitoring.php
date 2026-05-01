<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'koneksi.php';

$uid = (int)$_SESSION['user_id'];
$id  = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: monitoring.php');
    exit;
}

$r    = mysqli_query($conn, "SELECT * FROM monitoring WHERE id = $id AND user_id = $uid");
$data = mysqli_fetch_assoc($r);

if (!$data) {
    header('Location: monitoring.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lokasi    = trim($_POST['lokasi_cctv']);
    $waktu     = trim($_POST['waktu_monitoring']);
    $jumlah    = trim($_POST['jumlah_kendaraan']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($lokasi) || empty($waktu) || $jumlah === '' || empty($deskripsi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($jumlah) || (int)$jumlah < 0) {
        $error = 'Jumlah kendaraan harus berupa angka.';
    } else {
        $jumlah = (int)$jumlah;

        if ($jumlah <= 20) {
            $status = 'Lancar';
        } elseif ($jumlah <= 50) {
            $status = 'Padat';
        } else {
            $status = 'Macet';
        }

        $foto_bukti = $data['foto_bukti'];

        if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
            $ext_ok = ['jpg', 'jpeg', 'png'];
            $ext    = strtolower(pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_ok)) {
                $error = 'Format file tidak diizinkan. Gunakan jpg, jpeg, atau png.';
            } else {
                $new_file = uniqid('cctv_') . '.' . $ext;
                $dest     = __DIR__ . '/uploads/' . $new_file;

                if (move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $dest)) {
                    $old = __DIR__ . '/uploads/' . $foto_bukti;
                    if (file_exists($old)) unlink($old);
                    $foto_bukti = $new_file;
                } else {
                    $error = 'Gagal mengupload file baru.';
                }
            }
        }

        if (empty($error)) {
            $lok = mysqli_real_escape_string($conn, $lokasi);
            $wkt = mysqli_real_escape_string($conn, $waktu);
            $dsk = mysqli_real_escape_string($conn, $deskripsi);
            $fnm = mysqli_real_escape_string($conn, $foto_bukti);
            $sts = mysqli_real_escape_string($conn, $status);

            $q = mysqli_query($conn,
                "UPDATE monitoring SET
                    lokasi_cctv = '$lok',
                    waktu_monitoring = '$wkt',
                    jumlah_kendaraan = $jumlah,
                    status_kemacetan = '$sts',
                    deskripsi = '$dsk',
                    foto_bukti = '$fnm'
                 WHERE id = $id AND user_id = $uid"
            );

            if ($q) {
                header('Location: monitoring.php');
                exit;
            } else {
                $error = 'Gagal memperbarui data.';
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
    <title>Edit Monitoring - SmartTraffic Cam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require 'navbar.php'; ?>
<div class="page-wrapper">
    <a href="monitoring.php" class="back-link">&larr; Kembali ke Monitoring</a>
    <div class="page-title">Edit Data Monitoring</div>
    <div class="page-subtitle">Perbarui data pemantauan CCTV</div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card form-max">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Lokasi CCTV</label>
                    <input type="text" name="lokasi_cctv" value="<?= htmlspecialchars($_POST['lokasi_cctv'] ?? $data['lokasi_cctv']) ?>">
                </div>
                <div class="form-group">
                    <label>Waktu Monitoring</label>
                    <input type="datetime-local" name="waktu_monitoring" value="<?= htmlspecialchars($_POST['waktu_monitoring'] ?? date('Y-m-d\TH:i', strtotime($data['waktu_monitoring']))) ?>">
                </div>
                <div class="form-group">
                    <label>Jumlah Kendaraan</label>
                    <input type="number" name="jumlah_kendaraan" min="0" value="<?= htmlspecialchars($_POST['jumlah_kendaraan'] ?? $data['jumlah_kendaraan']) ?>">
                    <div class="hint-badges">
                        <span class="badge badge-lancar">0 - 20 = Lancar</span>
                        <span class="badge badge-padat">21 - 50 = Padat</span>
                        <span class="badge badge-macet">&gt; 50 = Macet</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi Kondisi Lalu Lintas</label>
                    <textarea name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? $data['deskripsi']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto Bukti CCTV (opsional &mdash; kosongkan jika tidak ingin mengganti)</label>
                    <input type="file" name="foto_bukti" accept=".jpg,.jpeg,.png">
                    <div class="file-current">
                        File saat ini: <a href="uploads/<?= htmlspecialchars($data['foto_bukti']) ?>" target="_blank"><?= htmlspecialchars($data['foto_bukti']) ?></a>
                    </div>
                </div>
                <div class="divider"></div>
                <button type="submit" class="btn btn-primary btn-block">Perbarui Data Monitoring</button>
            </form>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
</body>
</html>
