<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);
$currentUserId = (int) $_SESSION["user_id"];

if ($id <= 0 || $id === $currentUserId) {
    header("Location: index.php?pesan=akun_tidak_bisa_dihapus");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$account) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}

/* Jangan menghapus akun yang masih terhubung ke data karyawan. */
$stmt = $conn->prepare("SELECT id FROM employees WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$linkedEmployee = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($linkedEmployee) {
    header("Location: index.php?pesan=akun_masih_terhubung");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    $uid = $currentUserId;
    $aktivitas = "Menghapus akun pengguna";
    $referensi = "User ID: " . $id;

    $log = $conn->prepare(
        "INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu)
         VALUES (?, ?, ?, NOW())"
    );

    if ($log) {
        $log->bind_param("iss", $uid, $aktivitas, $referensi);
        $log->execute();
        $log->close();
    }

    header("Location: index.php?pesan=berhasil_hapus");
    exit;
}

$stmt->close();
header("Location: index.php?pesan=gagal_hapus");
exit;