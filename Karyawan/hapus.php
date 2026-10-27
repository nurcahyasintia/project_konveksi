<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_POST["id"];

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$employee) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}

/* Hapus absensi terlebih dahulu agar tidak mengganggu relasi tabel. */
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("DELETE FROM attendance WHERE employee_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Catat aktivitas bila tabel log tersedia.
    $aktivitas = "Menghapus data karyawan";
    $referensi = "Employee ID: " . $id;
    $userId = (int) $_SESSION["user_id"];

    $stmt = $conn->prepare(
        "INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu)
         VALUES (?, ?, ?, NOW())"
    );

    if ($stmt) {
        $stmt->bind_param("iss", $userId, $aktivitas, $referensi);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    header("Location: index.php?pesan=berhasil_hapus");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header("Location: index.php?pesan=gagal_hapus");
    exit;
}