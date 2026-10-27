<?php
// auth/role_check.php
// File ini berfungsi sebagai "satpam" untuk mengecek login dan role

// 1. Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi 1: Cek apakah user sudah login
 * Panggil fungsi ini di setiap halaman yang membutuhkan login
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Jika belum login, lempar ke halaman login
        header("Location: ../auth/login.php");
        exit;
    }
}

/**
 * Fungsi 2: Cek apakah user punya role yang diizinkan
 * @param string|array $allowedRoles - Role yang diizinkan (bisa 1 atau banyak)
 * Contoh: requireRole('Admin') atau requireRole(['Admin', 'Owner'])
 */
function requireRole($allowedRoles) {
    // Pastikan user sudah login dulu
    requireLogin();
    
    // Normalisasi input menjadi array (agar bisa terima 1 role atau banyak role)
    $allowedRoles = (array) $allowedRoles;
    
    // Cek apakah role user ada di daftar yang diizinkan
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        // Jika tidak punya akses, tampilkan halaman error 403
        http_response_code(403);
        die("
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Akses Ditolak</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5'>
                <div class='card shadow border-0'>
                    <div class='card-body p-5 text-center'>
                        <h1 class='text-danger mb-3'>🚫 403 - Akses Ditolak</h1>
                        <p class='text-muted mb-4'>Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                        <p class='mb-4'>Role Anda saat ini: <strong class='text-primary'>{$_SESSION['role']}</strong></p>
                        <a href='../index.php' class='btn btn-primary'>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ");
        exit;
    }
}
?>