<?php
require_once "../config/database.php";

// Pastikan ID tersedia dan berupa angka
if (!isset($_GET["id"]) || !ctype_digit($_GET["id"]) || (int) $_GET["id"] < 1) {
    header("Location: index.php?pesan=id_tidak_valid");
    exit;
}

$id = (int) $_GET["id"];

// Pastikan data pelanggan memang ada
$stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($hasil) === 0) {
    mysqli_stmt_close($stmt);
    header("Location: index.php?pesan=data_tidak_ditemukan");
    exit;
}

mysqli_stmt_close($stmt);

// Hapus data pelanggan
$stmt = mysqli_prepare($conn, "DELETE FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: index.php?pesan=hapus");
    exit;
}

mysqli_stmt_close($stmt);
header("Location: index.php?pesan=gagal_hapus");
exit;
?>
