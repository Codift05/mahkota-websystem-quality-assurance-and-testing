<?php
// get_artikel.php
header('Content-Type: application/json');
$file = __DIR__ . '/artikel.json';
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}
$artikels = json_decode(file_get_contents($file), true);
echo json_encode($artikels);
