<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'smarttraffic_cam';

$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, 'utf8mb4');

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
