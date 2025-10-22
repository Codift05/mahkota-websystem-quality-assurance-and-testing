<?php
// Change working directory to API script folder so relative requires resolve
chdir(dirname(__DIR__) . '/api/artikel');

// Start session and mock admin
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$_SESSION['is_admin'] = true;
$_SESSION['username'] = 'smoke_admin';

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'judul' => 'Smoke Test Artikel',
    'isi' => 'Konten artikel untuk smoke test',
    'kategori' => 'umum'
];

ob_start();
include 'create.php';
$out = ob_get_clean();

echo $out, "\n";