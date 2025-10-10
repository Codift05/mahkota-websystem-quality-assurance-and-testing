<?php
// lib_log.php - helper to insert activity logs into log_aktivitas
require_once __DIR__ . '/db.php';

function log_activity($conn, $user_id, $action, $detail = null) {
    $stmt = $conn->prepare("INSERT INTO log_aktivitas (user_id, action, detail, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) return false;
    $stmt->bind_param('iss', $user_id, $action, $detail);
    $res = $stmt->execute();
    $stmt->close();
    return $res;
}
?>