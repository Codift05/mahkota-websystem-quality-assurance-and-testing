<?php
// login.php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(['success' => false, 'error' => 'Username dan password wajib diisi']);
    exit;
}

// Cek di database
$stmt = $conn->prepare('SELECT id, username, password, email FROM admin WHERE username = ? OR email = ?');
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    // Cek apakah password di-hash dengan password_hash atau plain text
    if (password_verify($password, $admin['password'])) {
        // Password menggunakan hash (recommended)
        $_SESSION['is_admin'] = true;
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'] ?? '';
        echo json_encode(['success' => true]);
    } elseif ($password === $admin['password']) {
        // Password plain text (untuk backward compatibility)
        $_SESSION['is_admin'] = true;
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'] ?? '';
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Password salah']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Username atau email tidak ditemukan']);
}

$stmt->close();
$conn->close();
