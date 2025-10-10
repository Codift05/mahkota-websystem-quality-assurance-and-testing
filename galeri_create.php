<?php
// galeri_create.php
header('Content-Type: application/json');
session_start();
require_once 'db.php';
require_once 'lib_log.php';

// basic auth check - assumes session stores user id
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'] ?? 0;

// validate POST
 $title = trim($_POST['judul'] ?? '');
 $desc = trim($_POST['deskripsi'] ?? '');

if ($title === '') {
    echo json_encode(['success' => false, 'error' => 'Judul wajib diisi']);
    exit;
}

// validate file
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'File gambar tidak ditemukan atau gagal upload']);
    exit;
}
$file = $_FILES['gambar'];
$allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
if (!isset($allowed_types[$file['type']])) {
    echo json_encode(['success' => false, 'error' => 'Tipe file tidak didukung']);
    exit;
}

// limit size to 5MB
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'Ukuran file terlalu besar (max 5MB)']);
    exit;
}

// move uploaded file
$ext = $allowed_types[$file['type']];
$uploads_dir = __DIR__ . '/uploads/galeri';
if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);
$filename = uniqid('galeri_') . '.' . $ext;
$target = $uploads_dir . '/' . $filename;
if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['success' => false, 'error' => 'Gagal menyimpan file']);
    exit;
}

// insert into db
$path = 'uploads/galeri/' . $filename;
$stmt = $conn->prepare('INSERT INTO galeri (judul, deskripsi, gambar, tanggal_upload, uploader_id) VALUES (?, ?, ?, NOW(), ?)');
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'DB prepare error']);
    exit;
}
$stmt->bind_param('sssi', $title, $desc, $path, $user_id);
$ok = $stmt->execute();
$stmt->close();
if ($ok) {
    // log action
    log_activity($conn, $user_id, 'create_galeri', json_encode(['judul' => $title, 'path' => $path]));
    echo json_encode(['success' => true, 'message' => 'Galeri berhasil disimpan']);
} else {
    echo json_encode(['success' => false, 'error' => 'Gagal menyimpan data']);
}
?>