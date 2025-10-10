<?php
// logout.php
session_start();

// Hapus semua session
$_SESSION = array();

// Hapus cookie session jika ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('Location: login-page.php');
exit;
?>
