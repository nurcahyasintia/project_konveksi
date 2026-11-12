<?php
session_start();
require_once "../config/database.php";

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'pelanggan';
// ==========================================

$error = "";
$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/* Ambil data pelanggan */
$stmt = mysqli_prepare($conn, "SELECT kode, nama, kontak, alamat FROM customers WHERE id = ?");
if (!$stmt) {
    die("Gagal mengambil data pelanggan.");
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $kode_db, $nama_db, $kontak_db, $alamat_db);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: index.php");
    exit;
}
mysqli_stmt_close($stmt);

$old_input = [
    'kode'   => $kode_db ?? "",
    'nama'   => $nama_db ?? "",
    'kontak' => $kontak_db ?? "",
    'alamat' => $alamat_db ?? "",
];

/* Proses menyimpan perubahan */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode   = trim($_POST["kode"] ?? "");
    $nama   = trim($_POST["nama"] ?? "");
    $kontak = trim($_POST["kontak"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if ($kode === "" || $nama === "") {
        $error = "Kode dan nama pelanggan wajib diisi.";
    } else {
        $update = mysqli_prepare($conn, "UPDATE customers SET kode = ?, nama = ?, kontak = ?, alamat = ? WHERE id = ?");

        if ($update) {
            mysqli_stmt_bind_param($update, "ssssi", $kode, $nama, $kontak, $alamat, $id);

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                if (mysqli_stmt_errno($update) == 1062) {
                    $error = "Kode pelanggan sudah digunakan. Silakan gunakan kode lain.";
                } else {
                    $error = "Perubahan gagal disimpan. Silakan coba kembali.";
                }
            }
            mysqli_stmt_close($update);
        } else {
            $error = "Terjadi kesalahan saat menyiapkan perubahan data.";
        }
    }
    // Update input agar form tidak reset saat error
    $old_input = ['kode' => $kode, 'nama' => $nama, 'kontak' => $kontak, 'alamat' => $alamat];
}

function e($val) { return htmlspecialchars((string)($val ?? ""), ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan | KonveksiKu</title>
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
        .topbar-label { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #EFF6FF; border-radius: 9px; color: var(--primary-blue); font-size: 12px; font-weight: 700; }
        .content { padding: 30px 32px; }

        /* 2. CSS KHUSUS FORM */
        .form-panel { max-width: 850px; background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 28px; box-shadow: 0 4px 16px rgba(15, 23, 42, .04); }
        .form-heading { margin-bottom: 25px; padding-bottom: 18px; border-bottom: 1px solid var(--border-color); }
        .form-heading h3 { font-size: 18px; color: var(--text-dark); margin-bottom: 7px; }
        .form-heading h3 i { color: var(--primary-blue); margin-right: 5px; }
        .form-heading p { font-size: 13px; color: var(--text-light); line-height: 1.6; }
        
        .alert { padding: 13px 15px; border-radius: 9px; margin-bottom: 20px; font-size: 13px; line-height: 1.6; background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        
        .form-group { margin-bottom: 21px; }
        .form-group label { display: block; color: var(--text-dark); font-size: 13px; font-weight: 700; margin-bottom: 9px; }
        .required { color: #DC2626; }
        .form-group input, .form-group textarea {
            display: block; width: 100%; padding: 13px 14px; border: 1px solid var(--border-color); border-radius: 9px;
            background: var(--white); color: var(--text-dark); font-family: inherit; font-size: 13px; outline: none; transition: .2s;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        .form-group textarea { min-height: 110px; resize: vertical; }
        
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-color); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 18px; border: none; border-radius: 9px; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: .2s; text-decoration: none; }
        .btn-save { background: var(--primary-blue); color: var(--white); }
        .btn-save:hover { background: #1D4ED8; }
        .btn-back { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); }
        .btn-back:hover { background: #F8FAFC; }
        
        .footer-note { text-align: center; color: var(--text-light); font-size: 11px; padding: 24px 10px 5px; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } .form-actions { flex-direction: column-reverse; } .btn { width: 100%; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Edit Pelanggan</h1>
                <div class="breadcrumb">Beranda / Pelanggan / <span>Edit</span></div>
            </div>
            <div class="topbar-label"><i class="fa-solid fa-circle-check"></i> Sistem Konveksi</div>
        </header>

        <section class="content">
            <div class="form-panel">
                <div class="form-heading">
                    <h3><i class="fa-solid fa-user-pen"></i> Ubah Data Pelanggan</h3>
                    <p>Kolom bertanda <span class="required">*</span> wajib diisi.</p>
                </div>

                <?php if ($error !== ""): ?>
                    <div class="alert"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="form-group">
                        <label for="kode">Kode Pelanggan <span class="required">*</span></label>
                        <input type="text" id="kode" name="kode" value="<?= e($old_input['kode']) ?>" placeholder="Contoh: PLG001" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Pelanggan <span class="required">*</span></label>
                        <input type="text" id="nama" name="nama" value="<?= e($old_input['nama']) ?>" placeholder="Masukkan nama pelanggan" required>
                    </div>
                    <div class="form-group">
                        <label for="kontak">Kontak</label>
                        <input type="text" id="kontak" name="kontak" value="<?= e($old_input['kontak']) ?>" placeholder="Nomor telepon pelanggan">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" placeholder="Masukkan alamat pelanggan"><?= e($old_input['alamat']) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                        <button type="submit" class="btn btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
            <div class="footer-note">&copy; <?= date('Y'); ?> KonveksiKu — Sistem Manajemen Konveksi</div>
        </section>
    </main>
</div>
</body>
</html>
