<?php
require_once "../config/database.php";

// Ambil ID produk dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Jika ID tidak valid, kembali ke halaman Produk
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Siapkan perintah hapus produk
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");

if (!$stmt) {
    die("Gagal menyiapkan penghapusan produk.");
}

// Jalankan penghapusan
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

// Tutup perintah
mysqli_stmt_close($stmt);

// Kembali ke halaman Produk
header("Location: index.php");
exit;
?>
