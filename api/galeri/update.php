<?php
// api/galeri/update.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? '';
$judul = $_POST['judul'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$gambar = '';

if (!$id || !$judul) {
    echo json_encode(['error' => 'ID dan judul wajib diisi']);
    exit;
}

// Cek jika ada upload gambar baru
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads/galeri';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['gambar']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['error' => 'Format file tidak didukung']);
        exit;
    }
    
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('galeri_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    
    $moved = move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
    if (!$moved && is_file($_FILES['gambar']['tmp_name'])) {
        $moved = copy($_FILES['gambar']['tmp_name'], $target);
    }
    if ($moved) {
        $gambar = 'uploads/galeri/' . $filename;
        $old = $conn->query("SELECT gambar FROM galeri WHERE id = $id");
        if ($old_row = $old->fetch_assoc()) {
            $old_file = dirname(dirname(__DIR__)) . '/' . $old_row['gambar'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }
    }
}

if ($gambar) {
    $stmt = $conn->prepare('UPDATE galeri SET judul=?, deskripsi=?, kategori=?, gambar=? WHERE id=?');
    $stmt->bind_param('ssssi', $judul, $deskripsi, $kategori, $gambar, $id);
} else {
    $stmt = $conn->prepare('UPDATE galeri SET judul=?, deskripsi=?, kategori=? WHERE id=?');
    $stmt->bind_param('sssi', $judul, $deskripsi, $kategori, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Galeri berhasil diupdate']);
} else {
    echo json_encode(['error' => 'Gagal update galeri']);
}

$stmt->close();
$conn->close();
