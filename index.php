<?php
session_start();
require_once "config/database.php";
require_once "auth/role_check.php";

// Cek login
requireLogin();

// ==========================================
// VARIABEL UNTUK SIDEBAR UNIVERSAL
// ==========================================
$base_path   = './'; // Root folder
$active_page = 'dashboard';
// ==========================================

// Ambil data ringkasan untuk Dashboard
// 1. Total Karyawan (sesuai schema: 'aktif')
$totalKaryawan = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE status = 'aktif'")->fetch_assoc()['total'] ?? 0;

// 2. Karyawan Hadir Hari Ini (sesuai schema: 'hadir')
$hariIni = date("Y-m-d");
$stmtHadir = $conn->prepare("SELECT COUNT(*) AS total FROM attendance WHERE tanggal = ? AND status = 'hadir'");
$stmtHadir->bind_param("s", $hariIni);
$stmtHadir->execute();
$hadirHariIni = $stmtHadir->get_result()->fetch_assoc()['total'] ?? 0;
$stmtHadir->close();

// 3. Total User/Akun (sesuai schema: 'aktif')
$totalUser = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status = 'aktif'")->fetch_assoc()['total'] ?? 0;

// 4. Placeholder untuk modul lain (akan diisi anggota lain nanti)
$totalOrder = 0; // Dari tabel orders (Anggota 2)
$stokKritis = 0; // Dari tabel inventory_items (Anggota 4)
$saldoKas = 0;   // Dari income - expense (Anggota 5)

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ========================================= */
        /* 1. CSS SIDEBAR UNIVERSAL (STANDAR TIM)    */
        /* ========================================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: #1E293B; --sidebar-active: #3B82F6;
            --primary-blue: #2563EB; --main-bg: #F8FAFC;
            --text-dark: #1E293B; --text-light: #94A3B8;
            --white: #FFFFFF; --border-color: #E2E8F0;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
            --info: #06b6d4; --purple: #8b5cf6;
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b; --gray-900: #0f172a;
            --radius: 14px; --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--main-bg); color: var(--gray-800); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
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

        /* ========================================= */
        /* 2. CSS KONTEN DASHBOARD (DIPERTAHANKAN)   */
        /* ========================================= */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { background: var(--white); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; z-index: 50; flex-wrap: wrap; gap: 12px; }
        .page-heading h1 { font-size: 20px; font-weight: 800; color: var(--gray-900); letter-spacing: -.3px; }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .admin-pill { background: #DBEAFE; color: #1D4ED8; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; }
        .page-content { padding: 28px 32px; flex: 1; }

        .alert-info { padding: 14px 18px; border-radius: var(--radius-sm); background: #DBEAFE; color: #1D4ED8; border-left: 3px solid var(--primary-blue); font-size: 13.5px; font-weight: 500; margin-bottom: 22px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); border: 1px solid var(--gray-200); transition: all .25s; position: relative; overflow: hidden; }
        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
        .stat-card .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px; }
        .stat-icon.blue { background: #DBEAFE; color: var(--primary-blue); }
        .stat-icon.green { background: #d1fae5; color: var(--success); }
        .stat-icon.orange { background: #fef3c7; color: var(--warning); }
        .stat-icon.red { background: #fee2e2; color: var(--danger); }
        .stat-icon.purple { background: #ede9fe; color: var(--purple); }
        .stat-icon.cyan { background: #cffafe; color: var(--info); }
        .stat-label { font-size: 12.5px; color: var(--gray-500); font-weight: 500; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; color: var(--gray-900); letter-spacing: -.5px; }
        .stat-card small { display: block; font-size: 11px; color: var(--gray-400); margin-top: 6px; font-style: italic; }
        
        .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); overflow: hidden; margin-bottom: 22px; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid var(--gray-200); font-size: 15px; font-weight: 700; color: var(--gray-900); letter-spacing: -.2px; }
        .card-body { padding: 22px; }
        .card-body p { margin-bottom: 10px; font-size: 13.5px; color: var(--gray-700); }
        .card-body ul { margin: 10px 0 14px 20px; font-size: 13.5px; color: var(--gray-700); }
        .card-body ul li { margin-bottom: 6px; }
        .text-muted { color: var(--gray-500); font-size: 12.5px; }
        
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all .2s; text-decoration: none; font-family: inherit; }
        .btn-outline { background: var(--white); color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }
        .btn-sm { padding: 6px 12px; font-size: 12.5px; }

        @media (max-width: 768px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; min-width: 0; padding: 18px 14px; }
            .logo { margin-bottom: 20px; }
            .nav-link { margin-bottom: 4px; padding: 11px 12px; }
            .sidebar-footer { margin-top: 15px; }
            .main { margin-left: 0; }
            .page-content { padding: 20px 16px; }
            .topbar { padding: 14px 16px; }
        }
    </style>
</head>
<body>

<div class="app">
    <!-- ===== SIDEBAR UNIVERSAL ===== -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main">
        <header class="topbar">
            <div class="page-heading">
                <h1>Dashboard</h1>
                <div class="breadcrumb">Beranda / <span>Dashboard</span></div>
            </div>
            <div class="topbar-actions">
                <span class="admin-pill"><?= e($_SESSION["role"] ?? "Pengguna") ?></span>
                <a href="auth/logout.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </header>

        <section class="page-content">
            <div class="alert-info">
                <i class="fas fa-hand-wave" style="margin-right:6px"></i>
                Selamat datang, <strong><?= e($_SESSION["nama"]) ?></strong>! Ini adalah ringkasan sistem KonveksiKu.
            </div>

            <!-- ===== STATS GRID ===== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Total Karyawan Aktif</div>
                    <div class="stat-value"><?= (int)$totalKaryawan ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                    <div class="stat-label">Hadir Hari Ini</div>
                    <div class="stat-value"><?= (int)$hadirHariIni ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-user-shield"></i></div>
                    <div class="stat-label">Total User/Akun</div>
                    <div class="stat-value"><?= (int)$totalUser ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-label">Order Aktif</div>
                    <div class="stat-value"><?= (int)$totalOrder ?></div>
                    <small class="text-muted">(Menunggu data dari Anggota 2)</small>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-label">Stok Kritis</div>
                    <div class="stat-value"><?= (int)$stokKritis ?></div>
                    <small class="text-muted">(Menunggu data dari Anggota 4)</small>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-wallet"></i></div>
                    <div class="stat-label">Saldo Kas</div>
                    <div class="stat-value">Rp <?= number_format($saldoKas, 0, ',', '.') ?></div>
                    <small class="text-muted">(Menunggu data dari Anggota 5)</small>
                </div>
            </div>

            <!-- ===== CARD INFORMASI ===== -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle" style="color:var(--primary-blue);margin-right:6px"></i>
                    Informasi Sistem
                </div>
                <div class="card-body">
                    <p><strong>Platform Manajemen &amp; Digitalisasi Konveksi</strong></p>
                    <p>Sistem ini membantu manajemen operasional usaha konveksi secara digital, meliputi:</p>
                    <ul>
                        <li>Manajemen karyawan dan absensi</li>
                        <li>Penggajian (Payroll)</li>
                        <li>Pemesanan dan produksi</li>
                        <li>Stok bahan dan barang jadi</li>
                        <li>Keuangan dan laporan</li>
                    </ul>
                    <p class="text-muted">
                        <i class="fas fa-calendar-alt" style="margin-right:4px"></i>
                        Tanggal: <?= date("d M Y") ?>
                    </p>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>