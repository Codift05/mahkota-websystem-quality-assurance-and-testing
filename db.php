<?php
// db.php - simple mysqli connection wrapper
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'mahkota';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    error_log('DB connection error: ' . $conn->connect_error);
    die('Database connection failed');
}
// set charset
$conn->set_charset('utf8mb4');
?>