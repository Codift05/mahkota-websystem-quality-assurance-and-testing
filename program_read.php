<?php
// Koneksi ke database
require_once('db.php');

// Cek apakah ada session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Cek apakah ada parameter id
if (isset($_GET['id'])) {
    // Ambil data program berdasarkan id
    $id = $_GET['id'];
    $sql = "SELECT * FROM program_bidang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $program = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'program' => $program]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Program tidak ditemukan']);
    }
} else {
    // Ambil semua data program
    $sql = "SELECT * FROM program_bidang ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $programs = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row;
        }
    }
    
    echo json_encode(['status' => 'success', 'programs' => $programs]);
}
?>