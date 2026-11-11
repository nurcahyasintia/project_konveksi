<?php
session_start();
require_once "../config/database.php";

// Cek login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'absensi';
// ==========================================

// Ambil daftar karyawan untuk dropdown
$employees = $conn->query("SELECT id, kode, nama FROM employees ORDER BY nama ASC");

// Logika Simpan Data Absensi
$pesan_error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = (int)($_POST["employee_id"] ?? 0);
    $tanggal     = trim($_POST["tanggal"] ?? "");
    $jam_masuk   = !empty($_POST["jam_masuk"]) ? trim($_POST["jam_masuk"]) : null;
    $jam_pulang  = !empty($_POST["jam_pulang"]) ? trim($_POST["jam_pulang"]) : null;
    $status      = trim($_POST["status"] ?? "Hadir");

    if ($employee_id > 0 && $tanggal !== "") {
        // Cek apakah karyawan sudah absen di tanggal yang sama
        $check = $conn->prepare("SELECT id FROM attendance WHERE employee_id = ? AND tanggal = ?");
        $check->bind_param("is", $employee_id, $tanggal);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $pesan_error = "Karyawan ini sudah memiliki data absensi pada tanggal tersebut.";
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (employee_id, tanggal, jam_masuk, jam_pulang, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $employee_id, $tanggal, $jam_masuk, $jam_pulang, $status);
            
            if ($stmt->execute()) {
                header("Location: index.php?pesan=berhasil");
                exit;
            } else {
                $pesan_error = "Gagal menyimpan data: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $pesan_error = "Mohon pilih karyawan dan tanggal.";
    }
}

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Absensi - KonveksiKu</title>
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
        
        /* Sidebar Styles */
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
        
        /* Main Content */
        .main { flex: 1; min-width: 0; }
        .topbar { min-height: 90px; background: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .topbar h1 { font-size: 23px; color: var(--text-dark); margin-bottom: 7px; }
        .breadcrumb { color: var(--text-light); font-size: 12px; }
        .breadcrumb span { color: var(--primary-blue); }
        .content { padding: 30px 32px; }

        /* 2. CSS KHUSUS FORM TAMBAH ABSENSI */
        .form-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 30px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        .form-card h2 { font-size: 18px; color: var(--text-dark); margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .form-group input, .form-group select {
            padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px;
            font-size: 14px; font-family: inherit; transition: .2s; background: var(--white);
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
        
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; border-radius: 9px; padding: 15px; margin-bottom: 20px; font-size: 13px; }
        
        .form-actions { display: flex; gap: 12px; margin-top: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 20px; border: none; border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: .2s; text-decoration: none; }
        .btn-primary { background: var(--primary-blue); color: var(--white); }
        .btn-primary:hover { background: #1D4ED8; }
        .btn-secondary { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); }
        .btn-secondary:hover { background: #F8FAFC; }

        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Tambah Absensi</h1>
                <div class="breadcrumb">Home / Absensi / <span>Tambah Absensi</span></div>
            </div>
        </header>

        <section class="content">
            <?php if ($pesan_error): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan_error); ?></div>
            <?php endif; ?>

            <div class="form-card">
                <h2>Form Input Absensi Karyawan</h2>
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Karyawan <span style="color:red">*</span></label>
                            <select name="employee_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php while ($emp = $employees->fetch_assoc()): ?>
                                    <option value="<?= (int)$emp['id'] ?>">
                                        <?= e($emp['kode']) ?> - <?= e($emp['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal <span style="color:red">*</span></label>
                            <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Jam Masuk</label>
                            <input type="time" name="jam_masuk">
                        </div>
                        <div class="form-group">
                            <label>Jam Pulang</label>
                            <input type="time" name="jam_pulang">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Status Kehadiran <span style="color:red">*</span></label>
                            <select name="status" required>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpa">Alpa</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Absensi</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>