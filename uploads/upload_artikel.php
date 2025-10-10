<?php
// upload_artikel.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (!$title || !$content || !isset($_FILES['image'])) {
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

$uploads_dir = __DIR__ . '/uploads';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

$image = $_FILES['image'];
$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $ext;
$target = $uploads_dir . '/' . $filename;

if (!move_uploaded_file($image['tmp_name'], $target)) {
    echo json_encode(['error' => 'Gagal upload gambar']);
    exit;
}

$artikel = [
    'title' => $title,
    'content' => $content,
    'image' => 'uploads/' . $filename,
    'created_at' => date('Y-m-d H:i:s')
];

$file = __DIR__ . '/artikel.json';
$artikels = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$artikels[] = $artikel;
file_put_contents($file, json_encode($artikels, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'artikel' => $artikel]);
