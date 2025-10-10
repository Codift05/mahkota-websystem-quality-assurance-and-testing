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
    $id = $_POST['id'];
    
    // Validasi data
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID program harus diisi']);
        exit();
    }
    
    // Cek apakah program dengan id tersebut ada
    $check_sql = "SELECT * FROM program_bidang WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Program tidak ditemukan']);
        exit();
    }
    
    $program = $check_result->fetch_assoc();
    $nama_program = $program['nama_program'];
    $gambar = $program['gambar'];
    
    // Hapus data dari database
    try {
        $sql = "DELETE FROM program_bidang WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Hapus gambar jika ada
            if (!empty($gambar) && file_exists($gambar)) {
                unlink($gambar);
            }
            
            // Log aktivitas
            logActivity($conn, $_SESSION['user_id'], "Menghapus program bidang: $nama_program");
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Program bidang berhasil dihapus'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus program bidang: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>