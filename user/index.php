<?php
session_start();
require_once "../config/database.php";

// Cek login (Sesuaikan dengan logic auth Anggota 1)
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'user'; 
// ==========================================

$user_nama = $_SESSION['nama'] ?? 'Pengguna';
$user_role = $_SESSION['role'] ?? 'Staff';
$inisial   = strtoupper(substr($user_nama, 0, 2));
$cari = trim($_GET["cari"] ?? "");

$sql = "SELECT u.id, u.nama, u.email, u.status, r.nama_role
        FROM users u
        LEFT JOIN roles r ON r.id = u.role_id";

if ($cari !== "") {
    $sql .= " WHERE u.nama LIKE ? OR u.email LIKE ? OR r.nama_role LIKE ?";
}

$sql .= " ORDER BY u.id DESC";

$stmt = $conn->prepare($sql);

if ($cari !== "") {
    $keyword = "%" . $cari . "%";
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
}

$stmt->execute();
$users = $stmt->get_result();

$pesan = $_GET["pesan"] ?? "";
$pesanTampil = [
    "berhasil_hapus" => "Akun berhasil dihapus.",
    "gagal_hapus" => "Akun gagal dihapus.",
    "tidak_ditemukan" => "Akun tidak ditemukan.",
    "akun_masih_terhubung" => "Akun masih terhubung ke data karyawan. Akun tidak dihapus.",
    "akun_tidak_bisa_dihapus" => "Akun sendiri atau ID tidak valid tidak dapat dihapus."
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Akun | KonveksiKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 1. CSS GLOBAL & SIDEBAR (SAMA PERSIS ANGGOTA 2) */
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
        
        /* 2. CSS KONTEN SPESIFIK */
        .topbar { min-height: 90px; background: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .topbar h1 { font-size: 23px; color: var(--text-dark); margin-bottom: 7px; }
        .breadcrumb { color: var(--text-light); font-size: 12px; }
        .breadcrumb span { color: var(--primary-blue); }
        .content { padding: 30px 32px; }
        .profile-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 30px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); display: flex; align-items: center; gap: 25px; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; background: var(--primary-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 800; flex-shrink: 0; }
        .profile-details h2 { font-size: 22px; color: var(--text-dark); margin-bottom: 5px; }
        .profile-details p { color: var(--text-light); font-size: 14px; margin-bottom: 15px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border: none; border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: .2s; }
        .btn-primary { background: var(--primary-blue); color: var(--white); }
        .btn-primary:hover { background: #1D4ED8; }
        .btn-outline { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); }
        .btn-outline:hover { background: #F8FAFC; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Profil & Akun</h1>
                <div class="breadcrumb">Beranda / <span>Profil</span></div>
            </div>
        </header>
        <section class="content">
            <div class="profile-card">
                <div class="profile-avatar"><?= $inisial; ?></div>
                <div class="profile-details">
                    <h2><?= htmlspecialchars($user_nama); ?></h2>
                    <p>Role: <?= htmlspecialchars($user_role); ?></p>
                    <div style="display: flex; gap: 10px;">
                        <a href="edit.php" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Edit Profil</a>
                        <a href="../auth/logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>