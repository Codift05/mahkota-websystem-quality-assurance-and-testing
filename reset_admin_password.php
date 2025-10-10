<?php
/**
 * Script untuk reset password admin
 * Akses: http://localhost/Devin/reset_admin_password.php
 */

require_once 'db.php';

// Password baru yang akan di-set
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password untuk user admin
$stmt = $conn->prepare("UPDATE admin SET password = ?, email = ? WHERE username = ?");
$email = 'admin@mahkota.com';
$username = 'admin';
$stmt->bind_param('sss', $hashed_password, $email, $username);

if ($stmt->execute()) {
    echo "<h2>✅ Password berhasil direset!</h2>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0; font-family: Arial;'>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Email:</strong> admin@mahkota.com</p>";
    echo "</div>";
    echo "<p><a href='login-page.php' style='background: #4e73df; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login Sekarang</a></p>";
    
    // Verifikasi password
    $check = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    $result = $check->get_result();
    $admin = $result->fetch_assoc();
    
    echo "<hr>";
    echo "<h3>🔍 Verifikasi:</h3>";
    if (password_verify($new_password, $admin['password'])) {
        echo "<p style='color: green;'>✅ Password hash valid! Bisa digunakan untuk login.</p>";
    } else {
        echo "<p style='color: red;'>❌ Password hash tidak valid!</p>";
    }
    
    $check->close();
} else {
    echo "<h2>❌ Error!</h2>";
    echo "<p>Gagal reset password: " . $conn->error . "</p>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h2, h3 {
            color: #3a3b45;
        }
        p {
            color: #5a5c69;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- PHP output akan muncul di sini -->
    </div>
</body>
</html>
