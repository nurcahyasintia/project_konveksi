<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$employees = $conn->query(
    "SELECT id, kode, nama FROM employees ORDER BY nama ASC"
);

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Absensi - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ============================================================
           KONVEKSIKU THEME — CSS INTERNAL (Tambah Absensi)
           ============================================================ */
        :root {
            --primary: #2563eb; --primary-dark: #1d4ed8; --primary-light: #dbeafe;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
            --info: #06b6d4; --purple: #8b5cf6; --dark: #0f172a;
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b; --gray-900: #0f172a;
            --radius: 14px; --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--gray-100); color: var(--gray-800);
            line-height: 1.6; -webkit-font-smoothing: antialiased;
        }

        /* ===== LAYOUT ===== */
        .app-layout { display: flex; min-height: 100vh; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px; background: var(--dark); color: #fff;
            display: flex; flex-direction: column;
            position: fixed; inset: 0 auto 0 0; height: 100vh; z-index: 100;
        }
        .sidebar-brand {
            padding: 22px 20px; border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 12px;
            font-size: 20px; font-weight: 800; letter-spacing: -.3px;
        }
        .brand-icon {
            background: rgba(37,99,235,.15); padding: 8px; border-radius: 10px;
            font-size: 16px; line-height: 1;
        }
        .brand-accent { color: var(--primary); }
        .sidebar-label {
            font-size: 10.5px; text-transform: uppercase; letter-spacing: 1.4px;
            color: var(--gray-500); padding: 16px 20px 8px; font-weight: 700;
        }
        .sidebar-nav { flex: 1; padding: 8px 12px; overflow-y: auto; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all .2s; margin-bottom: 2px;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar-nav a.active {
            background: var(--primary); color: #fff;
            box-shadow: 0 4px 12px rgba(37,99,235,.35);
        }
        .sidebar-bottom {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-mini { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
        }
        .user-mini strong { font-size: 13.5px; font-weight: 600; color: #fff; display: block; }
        .user-mini small { font-size: 11.5px; color: var(--gray-500); }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1; margin-left: 260px; display: flex;
            flex-direction: column; min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff; padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--gray-200);
            position: sticky; top: 0; z-index: 50;
            flex-wrap: wrap; gap: 12px;
        }
        .page-heading h1 {
            font-size: 20px; font-weight: 800; color: var(--gray-900);
            letter-spacing: -.3px;
        }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .admin-pill {
            background: var(--primary-light); color: var(--primary-dark);
            font-size: 12px; font-weight: 700;
            padding: 6px 14px; border-radius: 20px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content { padding: 28px 32px; flex: 1; }

        /* ===== ALERT ===== */
        .alert-info {
            padding: 14px 18px; border-radius: var(--radius-sm);
            background: #fee2e2; color: #991b1b;
            border-left: 3px solid var(--danger);
            font-size: 13.5px; font-weight: 500; margin-bottom: 16px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            overflow: hidden; max-width: 760px;
        }
        .card-header {
            padding: 18px 22px; border-bottom: 1px solid var(--gray-200);
            font-size: 15px; font-weight: 700; color: var(--gray-900);
            letter-spacing: -.2px;
        }
        .card-body { padding: 22px; }

        /* ===== FORM ===== */
        .form-grid {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 16px; margin-bottom: 20px;
        }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: span 2; }
        .form-group label {
            font-size: 12.5px; font-weight: 600; color: var(--gray-700);
        }
        .form-group input,
        .form-group select {
            padding: 10px 14px; border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 13.5px;
            font-family: inherit; color: var(--gray-800);
            transition: all .2s; background: #fff; width: 100%;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 600; border: none;
            cursor: pointer; transition: all .2s; text-decoration: none;
            font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 16px rgba(37,99,235,.35);
        }
        .btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
            .page-content { padding: 20px 16px; }
            .topbar { padding: 14px 16px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
        }
    </style>
</head>
<body>

<div class="app-layout">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">👕</span>
            <span>Konveksi<span class="brand-accent">Ku</span></span>
        </div>
        <div class="sidebar-label">Anggota 1</div>
        <nav class="sidebar-nav">
            <a href="../user/index.php"><i class="fas fa-user-circle" style="width:16px"></i> Profil &amp; Akun</a>
            <a href="../Karyawan/index.php"><i class="fas fa-users" style="width:16px"></i> Karyawan</a>
            <a href="index.php" class="active"><i class="fas fa-clipboard-check" style="width:16px"></i> Absensi</a>
            <a href="../audit_log/index.php"><i class="fas fa-history" style="width:16px"></i> Audit Log</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="user-avatar">AD</div>
                <div>
                    <strong><?= htmlspecialchars($_SESSION["nama"] ?? "Pengguna") ?></strong>
                    <small>KonveksiKu</small>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="main-content">
        <header class="topbar">
            <div class="page-heading">
                <h1>Tambah Absensi</h1>
                <div class="breadcrumb">Home / Absensi / Tambah</div>
            </div>
            <div class="topbar-actions">
                <span class="admin-pill"><?= htmlspecialchars($_SESSION["role"] ?? "Pengguna") ?></span>
            </div>
        </header>

        <section class="page-content">
            <?php if ($error !== ""): ?>
                <div class="alert-info">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>
                    <?= htmlspecialchars($error === "data" ? "Periksa kembali data absensi." : $error) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list" style="color:var(--primary);margin-right:6px"></i>
                    Form Data Absensi
                </div>
                <div class="card-body">
                    <form method="POST" action="proses.php">
                        <div class="form-grid">
                            <div class="form-group full">
                                <label for="employee_id">Nama Karyawan</label>
                                <select id="employee_id" name="employee_id" required>
                                    <option value="">Pilih karyawan</option>
                                    <?php while ($e = $employees->fetch_assoc()): ?>
                                        <option value="<?= (int)$e["id"] ?>">
                                            <?= htmlspecialchars($e["kode"] . " - " . $e["nama"]) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" id="tanggal" name="tanggal"
                                       value="<?= date("Y-m-d") ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="status">Status Kehadiran</label>
                                <select id="status" name="status" required>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jam_masuk">Jam Masuk</label>
                                <input type="time" id="jam_masuk" name="jam_masuk">
                            </div>

                            <div class="form-group">
                                <label for="jam_pulang">Jam Pulang</label>
                                <input type="time" id="jam_pulang" name="jam_pulang">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Absensi
                        </button>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>