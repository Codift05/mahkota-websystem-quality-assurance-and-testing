<?php
// koneksi.php
$host = 'localhost';
$user = 'root'; // ganti jika user MySQL kamu berbeda
$pass = '';
$db = 'mahkota';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}
