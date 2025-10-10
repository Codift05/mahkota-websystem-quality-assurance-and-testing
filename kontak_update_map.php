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
    $google_maps_embed = $_POST['google_maps_embed'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    // Cek apakah data kontak sudah ada
    $check_sql = "SELECT * FROM kontak_info LIMIT 1";
    $check_result = $conn->query($check_sql);
    
    try {
        if ($check_result->num_rows > 0) {
            // Update data yang sudah ada
            $sql = "UPDATE kontak_info SET 
                    google_maps_embed = ?, 
                    latitude = ?, 
                    longitude = ?, 
                    updated_at = NOW() 
                    WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $google_maps_embed, $latitude, $longitude);
        } else {
            // Insert data baru
            $sql = "INSERT INTO kontak_info (google_maps_embed, latitude, longitude, created_at) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $google_maps_embed, $latitude, $longitude);
        }
        
        if ($stmt->execute()) {
            // Log aktivitas
            logActivity($conn, $_SESSION['user_id'], "Memperbarui informasi lokasi peta");
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Informasi lokasi berhasil disimpan'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan informasi lokasi: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>