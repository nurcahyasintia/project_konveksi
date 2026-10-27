<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: login.php?error=Email dan password wajib diisi");
    exit;
}

$query = "
    SELECT 
        users.id,
        users.nama,
        users.email,
        users.password_hash,
        users.role_id,
        users.status,
        roles.nama_role
    FROM users
    LEFT JOIN roles ON users.role_id = roles.id
    WHERE users.email = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query gagal: " . $conn->error);
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {
    header("Location: login.php?error=Email atau password salah");
    exit;
}

if ($user['status'] !== 'aktif') {
    header("Location: login.php?error=Akun tidak aktif");
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    header("Location: login.php?error=Email atau password salah");
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['email'] = $user['email'];
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['role'] = $user['nama_role'];

header("Location: ../index.php");
exit;