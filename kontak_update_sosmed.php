<?php
// Koneksi ke database
require_once('db.php');
require_once('lib_log.php');

// Cek apakah ada session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Fungsi untuk mencatat aktivitas admin
function logActivity($conn, $user_id, $activity) {
    $sql = "INSERT INTO admin_logs (user_id, activity, log_time) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $activity);
    $stmt->execute();
}

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $twitter = $_POST['twitter'];
    $youtube = $_POST['youtube'];
    $linkedin = $_POST['linkedin'];
    $tiktok = $_POST['tiktok'];
    
    // Cek apakah data kontak sudah ada
    $check_sql = "SELECT * FROM kontak_info LIMIT 1";
    $check_result = $conn->query($check_sql);
    
    try {
        if ($check_result->num_rows > 0) {
            // Update data yang sudah ada
            $sql = "UPDATE kontak_info SET 
                    facebook = ?, 
                    instagram = ?, 
                    twitter = ?, 
                    youtube = ?, 
                    linkedin = ?, 
                    tiktok = ?, 
                    updated_at = NOW() 
                    WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $facebook, $instagram, $twitter, $youtube, $linkedin, $tiktok);
        } else {
            // Insert data baru
            $sql = "INSERT INTO kontak_info (facebook, instagram, twitter, youtube, linkedin, tiktok, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $facebook, $instagram, $twitter, $youtube, $linkedin, $tiktok);
        }
        
        if ($stmt->execute()) {
            // Log aktivitas
            logActivity($conn, $_SESSION['user_id'], "Memperbarui informasi media sosial");
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Informasi media sosial berhasil disimpan'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan informasi media sosial: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>