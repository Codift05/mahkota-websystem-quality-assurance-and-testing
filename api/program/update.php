<?php
// api/program/update.php
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
$nama_program = $_POST['nama_program'] ?? '';
$bidang = $_POST['bidang'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status = $_POST['status'] ?? 'planned';
$gambar = '';

if (!$id || !$nama_program || !$bidang) {
    echo json_encode(['error' => 'ID, nama program, dan bidang wajib diisi']);
    exit;
}

// Cek jika ada upload gambar baru
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploads_dir = dirname(dirname(__DIR__)) . '/uploads/program';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('program_', true) . '.' . $ext;
    $target = $uploads_dir . '/' . $filename;
    
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = 'uploads/program/' . $filename;
        
        // Hapus gambar lama jika ada
        $old = $conn->query("SELECT gambar FROM program WHERE id = $id");
        if ($old_row = $old->fetch_assoc()) {
            if ($old_row['gambar']) {
                $old_file = dirname(dirname(__DIR__)) . '/' . $old_row['gambar'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }
    }
}

if ($gambar) {
    $stmt = $conn->prepare('UPDATE program SET nama_program=?, bidang=?, deskripsi=?, tanggal_mulai=?, tanggal_selesai=?, status=?, gambar=? WHERE id=?');
    $stmt->bind_param('sssssssi', $nama_program, $bidang, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $gambar, $id);
} else {
    $stmt = $conn->prepare('UPDATE program SET nama_program=?, bidang=?, deskripsi=?, tanggal_mulai=?, tanggal_selesai=?, status=? WHERE id=?');
    $stmt->bind_param('ssssssi', $nama_program, $bidang, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Program berhasil diupdate']);
} else {
    echo json_encode(['error' => 'Gagal update program']);
}

$stmt->close();
$conn->close();
