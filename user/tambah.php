<?php
session_start();
require_once "../config/database.php";
require_once "../auth/role_check.php";

requireRole(['Admin', 'Owner']);

$error = "";
$success = "";

// Ambil daftar role
$roles = $conn->query("SELECT id, nama_role FROM roles ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];
    
    // Validasi sederhana
    if (empty($nama) || empty($email) || empty($password) || empty($role_id)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Cek email duplikat
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Insert user baru
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password_hash, role_id, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $nama, $email, $password_hash, $role_id, $status);
            
            if ($stmt->execute()) {
                header("Location: index.php?pesan=berhasil");
                exit;
            } else {
                $error = "Gagal menyimpan: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_email->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - KonveksiKu</title>
</head>
<body>
    <h2>Tambah User Baru</h2>
    <p>
        <a href="index.php">[ Kembali ke Daftar User ]</a> | 
        <a href="../index.php">[ Dashboard ]</a>
    </p>
    <hr>

    <?php if ($error): ?>
        <p style="color:red; background:#ffe6e6; padding:10px; border:1px solid red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" style="max-width:500px;">
        <table cellpadding="8">
            <tr>
                <td><label>Nama Lengkap</label></td>
                <td><input type="text" name="nama" required style="width:100%;"></td>
            </tr>
            <tr>
                <td><label>Email</label></td>
                <td><input type="email" name="email" required style="width:100%;"></td>
            </tr>
            <tr>
                <td><label>Password</label></td>
                <td><input type="password" name="password" required style="width:100%;" placeholder="Min. 6 karakter"></td>
            </tr>
            <tr>
                <td><label>Role</label></td>
                <td>
                    <select name="role_id" required style="width:100%;">
                        <option value="">-- Pilih Role --</option>
                        <?php while ($r = $roles->fetch_assoc()): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nama_role']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Status</label></td>
                <td>
                    <select name="status" required style="width:100%;">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" style="padding:8px 15px; background:blue; color:white; border:none; cursor:pointer;">Simpan User</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>