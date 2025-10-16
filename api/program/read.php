<?php
// api/program/read.php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
header('Content-Type: application/json');
require_once '../../db.php';

$tableCheck = $conn->query("SHOW TABLES LIKE 'program'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    echo json_encode([]);
    $conn->close();
    exit;
}

$bidang = $_GET['bidang'] ?? '';
$status = $_GET['status'] ?? '';

$sql = 'SELECT * FROM program WHERE 1=1';
$params = [];
$types = '';

if ($bidang) {
    $sql .= ' AND bidang = ?';
    $params[] = $bidang;
    $types .= 's';
}

if ($status) {
    $sql .= ' AND status = ?';
    $params[] = $status;
    $types .= 's';
}

$sql .= ' ORDER BY tanggal_mulai DESC';

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$programs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
}

echo json_encode($programs);
$conn->close();
