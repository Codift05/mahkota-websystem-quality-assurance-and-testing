<?php
// Quick script to test Program API against TEST DB

define('TEST_MODE', true);
putenv('DB_NAME=mahkota_test');

define('PROJECT_ROOT', dirname(__DIR__));
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$_SESSION['is_admin'] = true;

// Create a program
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'nama_program' => 'Program QA Smoke',
    'bidang' => 'Pendidikan',
    'deskripsi' => 'Deskripsi smoke test',
    'status' => 'planned'
];
ob_start();
include PROJECT_ROOT . '/api/program/create.php';
$createResp = ob_get_clean();
echo "Create: ", $createResp, "\n";

$data = json_decode($createResp, true);
$id = $data['id'] ?? null;

// Read programs
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
ob_start();
include PROJECT_ROOT . '/api/program/read.php';
$readResp = ob_get_clean();
$items = json_decode($readResp, true);
echo "Read count: ", is_array($items) ? count($items) : 0, "\n";

// Update program
if ($id) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [
        'id' => $id,
        'nama_program' => 'Program QA Smoke Updated',
        'bidang' => 'Kesehatan',
        'status' => 'ongoing'
    ];
    ob_start();
    include PROJECT_ROOT . '/api/program/update.php';
    $updateResp = ob_get_clean();
    echo "Update: ", $updateResp, "\n";
}

// Delete program
if ($id) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['id' => $id];
    ob_start();
    include PROJECT_ROOT . '/api/program/delete.php';
    $deleteResp = ob_get_clean();
    echo "Delete: ", $deleteResp, "\n";
}