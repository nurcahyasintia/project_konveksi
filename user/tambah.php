<?php
session_start();
require_once "../config/database.php";
require_once "../auth/role_check.php";

requireRole(['Admin', 'Owner']);

<<<<<<< HEAD
// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'user'; 
// ==========================================

$error = "";
$roles = $conn->query("SELECT id, nama_role FROM roles ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_id = (int)$_POST['role_id'];
    $status = trim($_POST['status']);
    
    if (empty($nama) || empty($email) || empty($password) || empty($role_id)) {
        $error = "Semua field wajib diisi!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
=======
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
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
<<<<<<< HEAD
=======
            // Insert user baru
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password_hash, role_id, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $nama, $email, $password_hash, $role_id, $status);
            
            if ($stmt->execute()) {
<<<<<<< HEAD
                // Audit Log
                $uid = (int) $_SESSION["user_id"];
                $log = $conn->prepare("INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu) VALUES (?, ?, ?, NOW())");
                if ($log) {
                    $log->bind_param("iss", $uid, "Menambahkan akun pengguna baru", "Email: " . $email);
                    $log->execute();
                    $log->close();
                }
                
=======
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
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
<<<<<<< HEAD

function e($val) { return htmlspecialchars((string)($val ?? ""), ENT_QUOTES, "UTF-8"); }
=======
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - KonveksiKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 1. CSS GLOBAL & SIDEBAR (UNIFIED) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: #1E293B; --sidebar-active: #3B82F6;
            --primary-blue: #2563EB; --main-bg: #F8FAFC;
            --text-dark: #1E293B; --text-light: #94A3B8;
            --white: #FFFFFF; --border-color: #E2E8F0;
        }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--main-bg); color: var(--text-dark); }
        a { text-decoration: none; }
        .app { display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; min-width: 250px; background: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; padding: 25px 16px 18px; }
        .logo { display: flex; align-items: center; gap: 10px; padding: 0 12px; margin-bottom: 38px; font-size: 23px; font-weight: 800; color: var(--white); }
        .logo i { color: var(--primary-blue); font-size: 25px; }
        .logo span { color: #60A5FA; }
        .nav-title { color: var(--text-light); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; padding: 0 13px; margin-bottom: 12px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 13px 14px; margin-bottom: 7px; color: #CBD5E1; font-size: 14px; font-weight: 500; border-radius: 9px; transition: .2s; }
        .nav-link i { width: 20px; text-align: center; font-size: 16px; }
        .nav-link:hover { background: #334155; color: var(--white); }
        .nav-link.active { background: var(--sidebar-active); color: var(--white); box-shadow: 0 4px 12px rgba(59, 130, 246, .2); }
        .sidebar-footer { display: flex; align-items: center; gap: 11px; border-top: 1px solid #334155; padding: 20px 8px 0; margin-top: auto; }
        .avatar { width: 42px; height: 42px; min-width: 42px; border-radius: 50%; background: var(--primary-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
        .user-info { min-width: 0; }
        .user-name { font-size: 12px; font-weight: 700; color: var(--white); line-height: 1.5; overflow-wrap: anywhere; }
        .user-role { font-size: 11px; color: var(--text-light); margin-top: 3px; }
        
        .main { flex: 1; min-width: 0; }
        .topbar { min-height: 90px; background: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .topbar h1 { font-size: 23px; color: var(--text-dark); margin-bottom: 7px; }
        .breadcrumb { color: var(--text-light); font-size: 12px; }
        .breadcrumb span { color: var(--primary-blue); }
        .content { padding: 30px 32px; }

        /* 2. CSS KHUSUS FORM TAMBAH */
        .form-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 30px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); max-width: 700px; }
        .form-card h2 { font-size: 18px; color: var(--text-dark); margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); }
        .form-group { margin-bottom: 18px; display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .form-group input, .form-group select {
            padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px;
            font-size: 14px; font-family: inherit; transition: .2s; background: var(--white); width: 100%;
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; border-radius: 9px; padding: 15px; margin-bottom: 20px; font-size: 13px; }
        .form-actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border: none; border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: .2s; text-decoration: none; }
        .btn-primary { background: var(--primary-blue); color: var(--white); }
        .btn-primary:hover { background: #1D4ED8; }
        .btn-secondary { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); }
        .btn-secondary:hover { background: #F8FAFC; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Tambah User Baru</h1>
                <div class="breadcrumb">Beranda / Manajemen User / <span>Tambah</span></div>
            </div>
        </header>
        <section class="content">
            <?php if ($error): ?>
                <div class="alert-error"><i class="fa-solid fa-exclamation-circle"></i> <?= e($error) ?></div>
            <?php endif; ?>

            <div class="form-card">
                <h2><i class="fa-solid fa-user-plus" style="color:var(--primary-blue);margin-right:8px"></i> Form Tambah Akun</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select id="role_id" name="role_id" required>
                            <option value="">-- Pilih Role --</option>
                            <?php while ($r = $roles->fetch_assoc()): ?>
                                <option value="<?= $r['id'] ?>"><?= e($r['nama_role']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan User</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Batal</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
=======
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
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
</body>
</html>