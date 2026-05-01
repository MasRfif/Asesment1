<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'koneksi.php';

$uid    = (int)$_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM monitoring WHERE user_id = $uid ORDER BY waktu_monitoring DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Monitoring - SmartTraffic Cam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="section-header">
        <div>
            <div class="page-title">Data Monitoring CCTV</div>
            <div class="page-subtitle">Seluruh data monitoring yang Anda catat</div>
        </div>
        <a href="tambah_monitoring.php" class="btn btn-primary">+ Tambah Data</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lokasi CCTV</th>
                        <th>Waktu Monitoring</th>
                        <th>Kendaraan</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr><td colspan="8" class="no-data">Belum ada data. <a href="tambah_monitoring.php">Tambah sekarang</a>.</td></tr>
                <?php else: ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="color:#555;"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['lokasi_cctv']) ?></td>
                        <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($row['waktu_monitoring'])) ?></td>
                        <td><?= (int)$row['jumlah_kendaraan'] ?></td>
                        <td><span class="badge badge-<?= strtolower($row['status_kemacetan']) ?>"><?= $row['status_kemacetan'] ?></span></td>
                        <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($row['deskripsi']) ?>"><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><a href="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>" target="_blank" class="btn btn-sm" style="background:#1c1c1c; border-color:#3a3a3a; color:#999; font-size:.72rem;">Lihat</a></td>
                        <td style="white-space:nowrap;">
                            <a href="edit_monitoring.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="hapus_monitoring.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini beserta file buktinya?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
</body>
</html>
