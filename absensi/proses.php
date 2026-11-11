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

$employeeId = (int) ($_POST["employee_id"] ?? 0);
$tanggal = trim($_POST["tanggal"] ?? "");
$jamMasuk = trim($_POST["jam_masuk"] ?? "");
$jamPulang = trim($_POST["jam_pulang"] ?? "");
$status = trim($_POST["status"] ?? "");

$statusValid = ["Hadir", "Izin", "Sakit", "Alpa"];

if ($employeeId <= 0 || $tanggal === "" ||
    !in_array($status, $statusValid, true)) {
    header("Location: tambah.php?error=data");
    exit;
}

$date = DateTime::createFromFormat("!Y-m-d", $tanggal);
if (!$date || $date->format("Y-m-d") !== $tanggal) {
    header("Location: tambah.php?error=data");
    exit;
}

$jamMasuk = $jamMasuk === "" ? null : $jamMasuk;
$jamPulang = $jamPulang === "" ? null : $jamPulang;

if ($jamMasuk !== null &&
    !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $jamMasuk)) {
    header("Location: tambah.php?error=data");
    exit;
}

if ($jamPulang !== null &&
    !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $jamPulang)) {
    header("Location: tambah.php?error=data");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$exists) {
    header("Location: tambah.php?error=data");
    exit;
}

/* Kolom jam_masuk dan jam_pulang boleh bernilai NULL. */
$stmt = $conn->prepare(
    "INSERT INTO attendance
     (employee_id, tanggal, jam_masuk, jam_pulang, status)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issss",
    $employeeId, $tanggal, $jamMasuk, $jamPulang, $status
);

if (!$stmt->execute()) {
    $stmt->close();
    header("Location: tambah.php?error=data");
    exit;
}

$attendanceId = $conn->insert_id;
$stmt->close();

$userId = (int) $_SESSION["user_id"];
$aktivitas = "Menambahkan data absensi";
$referensi = "Attendance ID: " . $attendanceId;

$log = $conn->prepare(
    "INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu)
     VALUES (?, ?, ?, NOW())"
);

if ($log) {
    $log->bind_param("iss", $userId, $aktivitas, $referensi);
    $log->execute();
    $log->close();
}

header("Location: index.php?pesan=berhasil");
exit;