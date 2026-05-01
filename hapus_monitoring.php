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

$r   = mysqli_query($conn, "SELECT foto_bukti FROM monitoring WHERE id = $id AND user_id = $uid");
$row = mysqli_fetch_assoc($r);

if (!$row) {
    header('Location: monitoring.php');
    exit;
}

$file = __DIR__ . '/uploads/' . $row['foto_bukti'];
if (file_exists($file)) {
    unlink($file);
}

mysqli_query($conn, "DELETE FROM monitoring WHERE id = $id AND user_id = $uid");

header('Location: monitoring.php');
exit;
