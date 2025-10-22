<?php
// api/program/read.php
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli' && !headers_sent()) { session_start(); }
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
if (PHP_SAPI !== 'cli' && !headers_sent()) { header('Content-Type: application/json'); }
require_once dirname(__DIR__, 2) . '/db.php';

$status = $_GET['status'] ?? '';
$field = $_GET['bidang'] ?? '';

$query = 'SELECT * FROM program';
$where = [];
$params = [];
$types = '';

if ($status) { $where[] = 'status = ?'; $params[] = $status; $types .= 's'; }
if ($field) { $where[] = 'bidang = ?'; $params[] = $field; $types .= 's'; }

if ($where) { $query .= ' WHERE ' . implode(' AND ', $where); }
$query .= ' ORDER BY created_at DESC';

if ($params) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$programs = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
}

echo json_encode($programs);
// Do not close $conn to allow multiple API calls in same process
