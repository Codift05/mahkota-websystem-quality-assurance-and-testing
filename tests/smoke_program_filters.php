<?php
// Smoke test untuk filter status/bidang dan edge cases Program

define('TEST_MODE', true);
putenv('DB_NAME=mahkota_test');

define('PROJECT_ROOT', dirname(__DIR__));
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$_SESSION['is_admin'] = true;

$created_ids = [];

// 1) Buat beberapa program dengan variasi status & bidang
$cases = [
    ['nama_program' => 'Prog Planned Pendidikan', 'bidang' => 'Pendidikan', 'status' => 'planned'],
    ['nama_program' => 'Prog Ongoing Kesehatan',   'bidang' => 'Kesehatan',  'status' => 'ongoing'],
    ['nama_program' => 'Prog Completed Ekonomi',   'bidang' => 'Ekonomi',    'status' => 'completed'],
    ['nama_program' => 'Prog Planned Kesehatan',   'bidang' => 'Kesehatan',  'status' => 'planned'],
];
foreach ($cases as $i => $payload) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = $payload;
    ob_start();
    include PROJECT_ROOT . '/api/program/create.php';
    $resp = ob_get_clean();
    echo "Create[$i]: ", $resp, "\n";
    $data = json_decode($resp, true);
    if (!empty($data['id'])) { $created_ids[] = $data['id']; }
}

// 2) Read tanpa filter
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$resp = ob_get_clean();
$list = json_decode($resp, true);
echo "Read all count: ", is_array($list) ? count($list) : 0, "\n";

// 3) Filter status=planned
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['status' => 'planned'];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$resp = ob_get_clean();
$list = json_decode($resp, true);
echo "Filter status=planned count: ", is_array($list) ? count($list) : 0, "\n";

// 4) Filter bidang=Kesehatan
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['bidang' => 'Kesehatan'];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$resp = ob_get_clean();
$list = json_decode($resp, true);
echo "Filter bidang=Kesehatan count: ", is_array($list) ? count($list) : 0, "\n";

// 5) Filter gabungan status=planned & bidang=Kesehatan
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['status' => 'planned', 'bidang' => 'Kesehatan'];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$resp = ob_get_clean();
$list = json_decode($resp, true);
echo "Filter planned+Kesehatan count: ", is_array($list) ? count($list) : 0, "\n";

// 6) Edge case: create tanpa nama_program
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['bidang' => 'Umum'];
ob_start();
include PROJECT_ROOT . '/api/program/create.php';
$resp = ob_get_clean();
echo "Create missing nama_program: ", $resp, "\n";

// 7) Edge case: update tanpa id
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['nama_program' => 'Tanpa ID', 'bidang' => 'Umum'];
ob_start();
include PROJECT_ROOT . '/api/program/update.php';
$resp = ob_get_clean();
echo "Update missing id: ", $resp, "\n";

// 8) Edge case: update id tidak ada
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['id' => 999999, 'nama_program' => 'Tidak Ada', 'bidang' => 'Umum', 'status' => 'planned'];
ob_start();
include PROJECT_ROOT . '/api/program/update.php';
$resp = ob_get_clean();
echo "Update nonexistent id: ", $resp, "\n";

// 9) Edge case: delete id tidak ada
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['id' => 999999];
ob_start();
include PROJECT_ROOT . '/api/program/delete.php';
$resp = ob_get_clean();
echo "Delete nonexistent id: ", $resp, "\n";

// 10) Cleanup: hapus program yang dibuat
foreach ($created_ids as $id) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['id' => $id];
    ob_start();
    include PROJECT_ROOT . '/api/program/delete.php';
    $resp = ob_get_clean();
    echo "Cleanup delete id=$id: ", $resp, "\n";
}

// 11) Read akhir
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$resp = ob_get_clean();
$list = json_decode($resp, true);
echo "Final read count: ", is_array($list) ? count($list) : 0, "\n";