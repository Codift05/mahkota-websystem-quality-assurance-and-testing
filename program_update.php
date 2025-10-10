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
    $nama_program = $_POST['nama_program'];
    $deskripsi = $_POST['deskripsi'];
    $tujuan = $_POST['tujuan'];
    $sasaran = $_POST['sasaran'];
    $status = isset($_POST['status']) ? $_POST['status'] : 'aktif';
    
    // Validasi data
    if (empty($id) || empty($nama_program) || empty($deskripsi)) {
        echo json_encode(['status' => 'error', 'message' => 'ID, nama program, dan deskripsi harus diisi']);
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
    $old_gambar = $program['gambar'];
    
    // Upload gambar jika ada
    $gambar_path = $old_gambar;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($ext), $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Format file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF']);
            exit();
        }
        
        // Generate unique filename
        $new_filename = 'program_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_dir = 'uploads/program/';
        
        // Create directory if not exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            $gambar_path = $upload_path;
            
            // Hapus gambar lama jika ada
            if (!empty($old_gambar) && file_exists($old_gambar)) {
                unlink($old_gambar);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar']);
            exit();
        }
    }
    
    // Update data di database
    try {
        $sql = "UPDATE program_bidang SET 
                nama_program = ?, 
                deskripsi = ?, 
                tujuan = ?, 
                sasaran = ?, 
                gambar = ?, 
                status = ?, 
                updated_at = NOW() 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $nama_program, $deskripsi, $tujuan, $sasaran, $gambar_path, $status, $id);
        
        if ($stmt->execute()) {
            // Log aktivitas
            logActivity($conn, $_SESSION['user_id'], "Mengupdate program bidang: $nama_program");
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Program bidang berhasil diupdate'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate program bidang: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>