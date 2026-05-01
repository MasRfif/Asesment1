<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $email_esc = mysqli_real_escape_string($conn, $email);
        $cek       = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_esc'");

        if (mysqli_num_rows($cek) > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $nama_esc = mysqli_real_escape_string($conn, $nama);
            $hash     = password_hash($password, PASSWORD_DEFAULT);
            $hash_esc = mysqli_real_escape_string($conn, $hash);

            $q = mysqli_query($conn, "INSERT INTO users (nama, email, password) VALUES ('$nama_esc', '$email_esc', '$hash_esc')");

            if ($q) {
                $success = 'Akun berhasil dibuat. Silakan login.';
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
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
    <title>Register - SmartTraffic Cam</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="display:flex; flex-direction:column;">
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a67c52" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/></svg>
            </div>
            <h1>Buat Akun</h1>
            <p>Daftarkan akun operator baru</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama operator" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" placeholder="operator@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter">
                <div class="form-hint">Minimal 6 karakter.</div>
            </div>
            <div class="divider"></div>
            <button type="submit" class="btn btn-primary btn-block">Buat Akun</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
    </div>
</div>
</body>
</html>
