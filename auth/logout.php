<?php
// auth/logout.php

// 1. Mulai session untuk mengakses data session yang ada
session_start();

// 2. Hapus semua variabel session
$_SESSION = array();

// 3. Hapus cookie session di browser (Sangat penting untuk keamanan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan session di server
session_destroy();

// 5. Redirect ke halaman login dengan pesan sukses
header("Location: login.php?logout=success");
exit;
?>