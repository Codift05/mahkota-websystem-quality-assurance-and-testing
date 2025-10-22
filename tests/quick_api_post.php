<?php
// Quick script to mimic ApiTestCase::post behavior against TEST DB

define('TEST_MODE', true);
putenv('DB_NAME=mahkota_test');

define('PROJECT_ROOT', dirname(__DIR__));
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$_SESSION['is_admin'] = true;

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'judul' => 'Artikel Test Baru',
    'kategori' => 'Berita',
    'isi' => 'Ini adalah isi artikel yang sangat panjang dan detail untuk testing purposes.'
];

ob_start();
include PROJECT_ROOT . '/api/artikel/create.php';
$out = ob_get_clean();

echo $out, "\n";