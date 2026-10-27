<?php

require_once "../config/database.php";

$nama = "Administrator";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role_id = 2;

$query = "INSERT INTO users
          (nama, email, password_hash, role_id)
          VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $nama,
    $email,
    $password,
    $role_id
);

if (mysqli_stmt_execute($stmt)) {
    echo "User berhasil dibuat.";
} else {
    echo "Gagal: " . mysqli_error($conn);
}