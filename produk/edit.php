<?php
<<<<<<< HEAD
session_start();
require_once "../config/database.php";

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'produk';
// ==========================================

=======
require_once "../config/database.php";

>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?pesan=id_tidak_valid");
    exit;
}

<<<<<<< HEAD
$stmt = mysqli_prepare($conn, "SELECT id, kode, nama, kategori, harga_jual, status FROM products WHERE id = ?");
=======
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, kode, nama, kategori, harga_jual, status
     FROM products
     WHERE id = ?"
);

>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
if (!$stmt) {
    die("Gagal mengambil data produk.");
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=data_tidak_ditemukan");
    exit;
}

$pesan_error = "";
<<<<<<< HEAD
$old_input = [
    'kode'       => $data['kode'],
    'nama'       => $data['nama'],
    'kategori'   => $data['kategori'],
    'harga_jual' => $data['harga_jual'],
    'status'     => $data['status']
];

if (isset($_POST['simpan'])) {
    $old_input['kode']       = trim($_POST['kode'] ?? '');
    $old_input['nama']       = trim($_POST['nama'] ?? '');
    $old_input['kategori']   = trim($_POST['kategori'] ?? '');
    $old_input['harga_jual'] = $_POST['harga_jual'] ?? '';
    $old_input['status']     = $_POST['status'] ?? '';

    if ($old_input['kode'] === '' || $old_input['nama'] === '' || $old_input['harga_jual'] === '') {
        $pesan_error = "Kode, nama, dan harga jual wajib diisi.";
    } elseif (!is_numeric($old_input['harga_jual']) || (float) $old_input['harga_jual'] < 0) {
        $pesan_error = "Harga jual harus berupa angka dan tidak boleh negatif.";
    } elseif (!in_array($old_input['status'], ['aktif', 'nonaktif'], true)) {
        $pesan_error = "Status produk tidak valid.";
    } else {
        $harga_jual = (int) $old_input['harga_jual'];

        $update = mysqli_prepare($conn, "UPDATE products SET kode = ?, nama = ?, kategori = ?, harga_jual = ?, status = ? WHERE id = ?");

        if ($update) {
            mysqli_stmt_bind_param($update, "sssisi", $old_input['kode'], $old_input['nama'], $old_input['kategori'], $harga_jual, $old_input['status'], $id);
=======

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga_input = $_POST['harga_jual'] ?? '';
    $status = $_POST['status'] ?? '';

    // Pertahankan input jika validasi gagal.
    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['kategori'] = $kategori;
    $data['harga_jual'] = $harga_input;
    $data['status'] = $status;

    if ($kode === '' || $nama === '' || $harga_input === '') {
        $pesan_error = "Kode, nama, dan harga jual wajib diisi.";
    } elseif (!is_numeric($harga_input) || (float) $harga_input < 0) {
        $pesan_error = "Harga jual harus berupa angka dan tidak boleh negatif.";
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $pesan_error = "Status produk tidak valid.";
    } else {
        $harga_jual = (int) $harga_input;

        $update = mysqli_prepare(
            $conn,
            "UPDATE products
             SET kode = ?, nama = ?, kategori = ?, harga_jual = ?, status = ?
             WHERE id = ?"
        );

        if ($update) {
            mysqli_stmt_bind_param(
                $update,
                "sssisi",
                $kode,
                $nama,
                $kategori,
                $harga_jual,
                $status,
                $id
            );
>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                if (mysqli_stmt_errno($update) == 1062) {
                    $pesan_error = "Kode produk sudah digunakan. Silakan pakai kode lain.";
                } else {
                    $pesan_error = "Data produk gagal diperbarui. Silakan periksa kembali.";
                }
<<<<<<< HEAD
=======

>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
                mysqli_stmt_close($update);
            }
        } else {
            $pesan_error = "Formulir belum bisa diproses. Silakan coba lagi.";
        }
    }
}
<<<<<<< HEAD

function e($val) { return htmlspecialchars((string)($val ?? ""), ENT_QUOTES, "UTF-8"); }
?>
=======
?>

>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk | KonveksiKu</title>
<<<<<<< HEAD
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
        .form-group input, .form-group select {
            display: block; width: 100%; padding: 13px 14px; border: 1px solid var(--border-color); border-radius: 9px;
            background: var(--white); color: var(--text-dark); font-family: inherit; font-size: 13px; outline: none; transition: .2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        
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
                <h1>Edit Produk</h1>
                <div class="breadcrumb">Beranda / Produk / <span>Edit</span></div>
            </div>
            <div class="topbar-label"><i class="fa-solid fa-circle-check"></i> Sistem Konveksi</div>
        </header>

        <section class="content">
            <div class="form-panel">
                <div class="form-heading">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Ubah Data Produk</h3>
                    <p>Kolom bertanda <span class="required">*</span> wajib diisi.</p>
                </div>

                <?php if ($pesan_error !== ""): ?>
                    <div class="alert"><i class="fa-solid fa-circle-exclamation"></i> <?= e($pesan_error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="kode">Kode Produk <span class="required">*</span></label>
                        <input type="text" id="kode" name="kode" value="<?= e($old_input['kode']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Produk <span class="required">*</span></label>
                        <input type="text" id="nama" name="nama" value="<?= e($old_input['nama']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" value="<?= e($old_input['kategori']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="harga_jual">Harga Jual (Rp) <span class="required">*</span></label>
                        <input type="number" id="harga_jual" name="harga_jual" min="0" step="1" value="<?= e($old_input['harga_jual']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status Produk</label>
                        <select id="status" name="status" required>
                            <option value="aktif" <?= $old_input['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $old_input['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                        <button type="submit" name="simpan" class="btn btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
            <div class="footer-note">&copy; <?= date('Y'); ?> KonveksiKu — Sistem Manajemen Konveksi</div>
        </section>
    </main>
=======

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-width: 250px;
            padding: 25px 16px;
            background: #1E293B;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .logo {
            margin: 0 0 35px 10px;
            font-size: 23px;
            font-weight: bold;
        }

        .logo i {
            margin-right: 8px;
            color: #60A5FA;
        }

        .logo span {
            color: #60A5FA;
        }

        .nav-title {
            margin: 0 0 12px 12px;
            color: #94A3B8;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        nav {
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 6px;
            color: #CBD5E1;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-link i {
            width: 18px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #3B82F6;
            color: white;
        }

        .sidebar-footer {
            display: flex !important;
            align-items: center !important;
            gap: 11px;
            margin-top: auto !important;
            padding: 20px 5px 0;
            border-top: 1px solid #334155;
            width: 100%;
        }

        .avatar {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            background: #2563EB !important;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .user-info {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .user-name {
            color: #FFFFFF !important;
            font-size: 13px !important;
            font-weight: bold !important;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .user-role {
            color: #94A3B8 !important;
            font-size: 12px !important;
            line-height: 1.4;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 90px;
            padding: 20px 30px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .breadcrumb {
            color: #64748B;
            font-size: 13px;
        }

        .topbar-label {
            color: #2563EB;
            font-size: 13px;
            font-weight: bold;
        }

        .topbar-label i {
            margin-right: 5px;
        }

        .content {
            padding: 30px;
        }

        .welcome {
            margin-bottom: 22px;
        }

        .welcome h1 {
            margin-bottom: 8px;
            color: #1E293B;
            font-size: 25px;
        }

        .welcome p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
        }

        .page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #2563EB;
            color: white;
        }

        .btn-primary:hover {
            background: #1D4ED8;
        }

        .btn-secondary {
            background: #E2E8F0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #CBD5E1;
        }

        .form-panel {
            width: 100%;
            max-width: 700px;
            padding: 25px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }

        .form-heading {
            margin-bottom: 24px;
        }

        .form-heading h2 {
            margin-bottom: 8px;
            color: #1E293B;
            font-size: 20px;
        }

        .form-heading h2 i {
            margin-right: 7px;
            color: #2563EB;
        }

        .form-heading p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #1E293B;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .form-footer {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .error-message {
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 8px;
            background: #FEE2E2;
            color: #B91C1C;
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 18px 20px;
            }
        }

        @media (max-width: 600px) {
            .app {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 0;
                padding: 18px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .sidebar-footer {
                margin-top: 15px !important;
            }

            .content {
                padding: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-panel {
                padding: 17px;
            }

            .page-actions,
            .form-footer {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="app">

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-shirt"></i>
            Konveksi<span>Ku</span>
        </div>

        <p class="nav-title">Menu Utama</p>

        <nav>
            <a href="../index.php" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="../pelanggan/index.php" class="nav-link">
                <i class="fa-solid fa-users"></i>
                <span>Pelanggan</span>
            </a>

            <a href="index.php" class="nav-link active">
                <i class="fa-solid fa-box"></i>
                <span>Produk</span>
            </a>

            <a href="../pesanan/index.php" class="nav-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Pesanan</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="avatar">PR</div>

            <div class="user-info">
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Produk</div>
            </div>
        </div>
    </aside>

    <main class="main">

        <header class="topbar">
            <div class="breadcrumb">
                Dashboard / Produk / Edit Produk
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-shirt"></i>
                KonveksiKu
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h1>Edit Produk</h1>
                <p>Perbarui informasi produk yang tersimpan di sistem.</p>
            </div>

            <div class="page-actions">
                <a href="index.php" class="btn-action btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Data Produk
                </a>
            </div>

            <div class="form-panel">

                <div class="form-heading">
                    <h2>
                        <i class="fa-solid fa-pen-to-square"></i>
                        Formulir Edit Produk
                    </h2>
                    <p>Ubah data produk sesuai informasi terbaru.</p>
                </div>

                <?php if ($pesan_error !== '') { ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($pesan_error); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="form-group">
                        <label for="kode">Kode Produk</label>
                        <input
                            type="text"
                            id="kode"
                            name="kode"
                            value="<?= htmlspecialchars((string) $data['kode']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= htmlspecialchars((string) $data['nama']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input
                            type="text"
                            id="kategori"
                            name="kategori"
                            value="<?= htmlspecialchars((string) ($data['kategori'] ?? '')); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="harga_jual">Harga Jual (Rp)</label>
                        <input
                            type="number"
                            id="harga_jual"
                            name="harga_jual"
                            min="0"
                            step="1"
                            value="<?= htmlspecialchars((string) $data['harga_jual']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status Produk</label>
                        <select id="status" name="status" required>
                            <option value="aktif"
                                <?= $data['status'] === 'aktif' ? 'selected' : ''; ?>>
                                Aktif
                            </option>

                            <option value="nonaktif"
                                <?= $data['status'] === 'nonaktif' ? 'selected' : ''; ?>>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="form-footer">
                        <button type="submit" name="simpan"
                                class="btn-action btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn-action btn-secondary">
                            Batal
                        </a>
                    </div>

                </form>
            </div>

        </section>
    </main>

>>>>>>> 1b9e4f250cf15fee5e3c59f7646a32305589883b
</div>
</body>
</html>