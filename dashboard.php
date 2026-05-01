<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'koneksi.php';

$uid = (int)$_SESSION['user_id'];

$r_stats = mysqli_query($conn,
    "SELECT
        COUNT(*) AS total,
        SUM(status_kemacetan = 'Lancar') AS lancar,
        SUM(status_kemacetan = 'Padat')  AS padat,
        SUM(status_kemacetan = 'Macet')  AS macet
     FROM monitoring WHERE user_id = $uid"
);
$stats = mysqli_fetch_assoc($r_stats);

$r_recent = mysqli_query($conn,
    "SELECT * FROM monitoring WHERE user_id = $uid ORDER BY created_at DESC LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartTraffic Cam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="section-header">
        <div>
            <div class="page-title">Dashboard</div>
            <div class="page-subtitle">Selamat datang, <strong style="color:#c4a882;"><?= htmlspecialchars($_SESSION['nama']) ?></strong></div>
        </div>
        <a href="tambah_monitoring.php" class="btn btn-primary">+ Tambah Data</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-num"><?= (int)$stats['total'] ?></div>
            <div class="stat-label">Total Data</div>
        </div>
        <div class="stat-card lancar">
            <div class="stat-num"><?= (int)$stats['lancar'] ?></div>
            <div class="stat-label">Lancar</div>
        </div>
        <div class="stat-card padat">
            <div class="stat-num"><?= (int)$stats['padat'] ?></div>
            <div class="stat-label">Padat</div>
        </div>
        <div class="stat-card macet">
            <div class="stat-num"><?= (int)$stats['macet'] ?></div>
            <div class="stat-label">Macet</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Data Monitoring Terbaru</span>
            <a href="monitoring.php" class="btn-link">Lihat semua &rarr;</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Lokasi CCTV</th>
                        <th>Waktu</th>
                        <th>Kendaraan</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($r_recent) === 0): ?>
                    <tr><td colspan="6" class="no-data">Belum ada data monitoring.</td></tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($r_recent)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['lokasi_cctv']) ?></td>
                        <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($row['waktu_monitoring'])) ?></td>
                        <td><?= (int)$row['jumlah_kendaraan'] ?></td>
                        <td><span class="badge badge-<?= strtolower($row['status_kemacetan']) ?>"><?= $row['status_kemacetan'] ?></span></td>
                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($row['deskripsi']) ?>"><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><a href="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>" target="_blank" class="btn btn-sm" style="background:#1c1c1c; border-color:#3a3a3a; color:#999; font-size:.72rem;">Lihat</a></td>
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
