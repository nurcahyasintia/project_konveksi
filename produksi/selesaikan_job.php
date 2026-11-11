<?php
// ==========================================
// SELESAIKAN PRODUKSI (Backend Processing)
// Anggota 3: Produksi
// ==========================================

require_once '../config/database.php';
require_once '../auth/auth_check.php';
require_once '../auth/role_check.php';

// Pastikan hanya role Produksi, Admin, dan Owner yang bisa eksekusi file ini
requireRole(['Produksi', 'Admin', 'Owner']);

// Proses hanya jika method POST dan data job_id ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = (int)$_POST['job_id'];
    $order_id = (int)$_POST['order_id'];

    // 1. Tandai job produksi sebagai selesai dan catat waktu selesainya
    $stmt1 = $conn->prepare("UPDATE production_jobs SET status = 'selesai', finished_at = NOW() WHERE id = ?");
    $stmt1->bind_param("i", $job_id);
    $stmt1->execute();

    // 2. Update status_produksi di tabel orders menjadi 'selesai' (Integrasi dengan modul Anggota 2)
    $stmt2 = $conn->prepare("UPDATE orders SET status_produksi = 'selesai' WHERE id = ?");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();

    // 3. Redirect kembali ke halaman detail job dengan pesan sukses
    header("Location: detail_job.php?id=$job_id&msg=completed");
    exit;
} else {
    // Jika file ini diakses secara langsung (bukan lewat form POST), lempar ke index
    header("Location: index.php");
    exit;
}
?>