<?php
// Simple CLI check: set $_SESSION and include API script
$_SESSION = ['is_admin' => true];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
include __DIR__ . '/../api/artikel/read.php';
$out = ob_get_clean();
echo $out, "\n";