<?php
/**
 * Check Galeri Table Structure
 * Script untuk mengecek struktur tabel galeri saat ini
 */

require_once 'db.php';

echo "<h2>Checking Galeri Table Structure</h2>";
echo "<hr>";

// Cek apakah tabel galeri ada
$check_table = $conn->query("SHOW TABLES LIKE 'galeri'");

if ($check_table->num_rows == 0) {
    echo "<p style='color: red;'><strong>❌ Tabel 'galeri' TIDAK DITEMUKAN!</strong></p>";
    echo "<p>Tabel galeri belum dibuat. Silakan jalankan script SQL untuk membuat tabel.</p>";
} else {
    echo "<p style='color: green;'><strong>✅ Tabel 'galeri' ditemukan</strong></p>";
    
    // Tampilkan struktur kolom
    echo "<h3>Struktur Kolom Tabel Galeri:</h3>";
    $columns = $conn->query("DESCRIBE galeri");
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
    echo "</tr>";
    
    $found_columns = [];
    while ($row = $columns->fetch_assoc()) {
        $found_columns[] = $row['Field'];
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Cek kolom yang diperlukan
    echo "<h3>Validasi Kolom:</h3>";
    $required_columns = ['id', 'judul', 'deskripsi', 'kategori', 'file_path', 'tanggal_upload'];
    
    echo "<ul>";
    foreach ($required_columns as $col) {
        if (in_array($col, $found_columns)) {
            echo "<li style='color: green;'>✅ Kolom '<strong>$col</strong>' ada</li>";
        } else {
            echo "<li style='color: red;'>❌ Kolom '<strong>$col</strong>' TIDAK ADA (perlu ditambahkan!)</li>";
        }
    }
    echo "</ul>";
    
    // Hitung jumlah data
    $count = $conn->query("SELECT COUNT(*) as total FROM galeri");
    $total = $count->fetch_assoc()['total'];
    echo "<p><strong>Total data galeri:</strong> $total</p>";
}

echo "<hr>";
echo "<h3>Solusi:</h3>";
echo "<ol>";
echo "<li>Buka <strong>phpMyAdmin</strong>: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
echo "<li>Pilih database Anda</li>";
echo "<li>Klik tab <strong>SQL</strong></li>";
echo "<li>Copy-paste isi file <code>fix_galeri_table.sql</code></li>";
echo "<li>Klik <strong>Go</strong></li>";
echo "<li>Refresh halaman ini untuk cek lagi</li>";
echo "</ol>";

echo "<p><a href='check_galeri_table.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔄 Refresh / Cek Lagi</a></p>";

$conn->close();
?>
