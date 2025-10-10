<?php
/**
 * Script untuk membuat user admin
 * Jalankan file ini sekali untuk membuat user admin pertama kali
 * Akses: http://localhost/Devin/create_admin.php
 */

require_once 'db.php';

// Data admin default
$username = 'admin';
$email = 'admin@mahkota.com';
$password = 'admin123'; // Password akan di-hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah admin sudah ada
$check = $conn->prepare('SELECT id FROM admin WHERE username = ? OR email = ?');
$check->bind_param('ss', $username, $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<h2>Admin sudah ada!</h2>";
    echo "<p>Username: <strong>$username</strong> sudah terdaftar di database.</p>";
    echo "<p><a href='login.html'>Kembali ke Login</a></p>";
} else {
    // Insert admin baru
    $stmt = $conn->prepare('INSERT INTO admin (username, email, password, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->bind_param('sss', $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<h2>✅ Admin berhasil dibuat!</h2>";
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p><strong>Username:</strong> $username</p>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Password:</strong> $password</p>";
        echo "</div>";
        echo "<p><strong>⚠️ PENTING:</strong> Simpan kredensial ini dengan aman!</p>";
        echo "<p><a href='login.html' style='background: #4e73df; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login Sekarang</a></p>";
    } else {
        echo "<h2>❌ Error!</h2>";
        echo "<p>Gagal membuat admin: " . $conn->error . "</p>";
    }
    
    $stmt->close();
}

$check->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - Mahkota</title>
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
        h2 {
            color: #3a3b45;
            margin-bottom: 20px;
        }
        p {
            color: #5a5c69;
            line-height: 1.6;
        }
        a {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- PHP output akan muncul di sini -->
    </div>
</body>
</html>
