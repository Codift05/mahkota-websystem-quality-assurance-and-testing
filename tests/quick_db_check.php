<?php
// Quick DB check to verify connection, selected database, and key tables
// Uses the same db.php as the application, honoring TEST_MODE and env from phpunit.xml

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('TEST_MODE', true);
putenv('DB_NAME=mahkota_test');

define('PROJECT_ROOT', dirname(__DIR__));
require_once PROJECT_ROOT . '/db.php';

$info = [
    'host' => $conn->host_info,
    'server' => $conn->server_info,
    'client' => $conn->client_info,
];

$dbResult = $conn->query('SELECT DATABASE() as db');
$currentDb = $dbResult ? ($dbResult->fetch_assoc()['db'] ?? null) : null;

$tables = [];
$tablesResult = $conn->query('SHOW TABLES');
if ($tablesResult) {
    while ($row = $tablesResult->fetch_array()) {
        $tables[] = $row[0];
    }
}

$counts = [];
foreach (['artikel', 'galeri', 'program'] as $t) {
    if (in_array($t, $tables, true)) {
        $res = $conn->query("SELECT COUNT(*) AS c FROM `$t`");
        $counts[$t] = $res ? (int)$res->fetch_assoc()['c'] : null;
    } else {
        $counts[$t] = null;
    }
}

$output = [
    'db' => $currentDb,
    'info' => $info,
    'tables' => $tables,
    'counts' => $counts,
];

echo json_encode($output, JSON_PRETTY_PRINT), "\n";

$conn->close();