<?php
require_once "../config/database.php";

// Ambil ID pesanan dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Periksa apakah ID valid
if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// Hapus pesanan menggunakan prepared statement
$query = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ?");

if ($query) {
    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);
    mysqli_stmt_close($query);
}

// Kembali ke halaman Pesanan
header("Location: index.php");
exit;
?>
