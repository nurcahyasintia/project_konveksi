<?php
require_once '../config/database.php';

// --- LOGIKA PHP KAMU TETAP SAMA (TIDAK DIUBAH) ---
$totalItem  = $pdo->query("SELECT COUNT(*) FROM inventory_items")->fetchColumn();
$kritis     = $pdo->query("SELECT COUNT(*) FROM inventory_items WHERE stok <= (stok_minimum * 0.5)")->fetchColumn();
$menipis    = $pdo->query("SELECT COUNT(*) FROM inventory_items WHERE stok > (stok_minimum * 0.5) AND stok < stok_minimum")->fetchColumn();
$barangJadi = $pdo->query("SELECT COALESCE(SUM(stok), 0) FROM inventory_items WHERE kategori = 'Barang Jadi'")->fetchColumn();

// Fungsi helper dari kelompok (jika belum ada di database.php, kita definisikan di sini)
if (!function_exists('e')) {
    function e($value) { return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8"); }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gudang & Stok - KonveksiKu</title>
    <!-- Font & Icon dari kelompok -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        /* COPY PASTE CSS UNIVERSAL DARI KELOMPOKMU DI SINI */
        /* (Saya ringkas agar tidak terlalu panjang, pastikan CSS :root, .app, .sidebar, .main, .topbar, .page-content, .stats-grid, .stat-card, .card ada di file ini atau di-include) */
        :root {
            /* ===== CSS SIDEBAR (COPY DARI DASHBOARD KELOMPOK) ===== */
* { margin: 0; padding: 0; box-sizing: border-box; }
a { text-decoration: none; color: inherit; }
.app { display: flex; min-height: 100vh; }

.sidebar { 
    width: 250px; min-width: 250px; 
    background: var(--sidebar-bg); color: var(--white); 
    display: flex; flex-direction: column; 
    padding: 25px 16px 18px; 
}
.logo { 
    display: flex; align-items: center; gap: 10px; 
    padding: 0 12px; margin-bottom: 38px; 
    font-size: 23px; font-weight: 800; color: var(--white); 
}
.logo i { color: var(--primary-blue); font-size: 25px; }
.logo span { color: #60A5FA; }
.nav-title { 
    color: var(--text-light); font-size: 11px; font-weight: 700; 
    text-transform: uppercase; letter-spacing: 1.2px; 
    padding: 0 13px; margin-bottom: 12px; 
}
.nav-link { 
    display: flex; align-items: center; gap: 12px; 
    padding: 13px 14px; margin-bottom: 7px; 
    color: #CBD5E1; font-size: 14px; font-weight: 500; 
    border-radius: 9px; transition: .2s; 
}
.nav-link i { width: 20px; text-align: center; font-size: 16px; }
.nav-link:hover { background: #334155; color: var(--white); }
.nav-link.active { 
    background: var(--sidebar-active); color: var(--white); 
    box-shadow: 0 4px 12px rgba(59, 130, 246, .2); 
}
.sidebar-footer { 
    display: flex; align-items: center; gap: 11px; 
    border-top: 1px solid #334155; 
    padding: 20px 8px 0; margin-top: auto; 
}
.avatar { 
    width: 42px; height: 42px; min-width: 42px; 
    border-radius: 50%; background: var(--primary-blue); 
    color: var(--white); display: flex; align-items: center; 
    justify-content: center; font-size: 14px; font-weight: 800; 
}
.user-info { min-width: 0; }
.user-name { 
    font-size: 12px; font-weight: 700; color: var(--white); 
    line-height: 1.5; overflow-wrap: anywhere; 
}
.user-role { font-size: 11px; color: var(--text-light); margin-top: 3px; }
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
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--main-bg); color: var(--gray-800); line-height: 1.6; margin: 0; }
        a { text-decoration: none; color: inherit; }
        .app { display: flex; min-height: 100vh; }
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { background: var(--white); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; z-index: 50; }
        .page-heading h1 { font-size: 20px; font-weight: 800; color: var(--gray-900); margin: 0; }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .page-content { padding: 28px 32px; flex: 1; }
        
        /* Stats Grid & Card (Dari Kelompok) */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); border: 1px solid var(--gray-200); transition: all .25s; }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px; }
        .stat-icon.blue { background: #DBEAFE; color: var(--primary-blue); }
        .stat-icon.red { background: #fee2e2; color: var(--danger); }
        .stat-icon.orange { background: #fef3c7; color: var(--warning); }
        .stat-icon.green { background: #d1fae5; color: var(--success); }
        .stat-label { font-size: 12.5px; color: var(--gray-500); font-weight: 500; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; color: var(--gray-900); }
        
        /* Card Umum (Untuk Tabel nanti) */
        .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); overflow: hidden; margin-bottom: 22px; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid var(--gray-200); font-size: 15px; font-weight: 700; color: var(--gray-900); }
        .card-body { padding: 22px; }

        /* Tombol Aksi */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all .2s; }
        .btn-primary { background: var(--primary-blue); color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-outline { background: var(--white); color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }
    </style>
</head>
<body>

<!-- STRUKTUR LAYOUT PERSIS SEPERTI DASHBOARD KELOMPOK -->
<div class="app">
    
    <!-- 1. SIDEBAR UNIVERSAL -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- 2. MAIN CONTENT -->
    <main class="main">
        
        <!-- TOPBAR (Ditambahkan agar sama dengan dashboard) -->
        <header class="topbar">
            <div class="page-heading">
                <h1>Gudang & Stok</h1>
                <div class="breadcrumb">Beranda / <span>Gudang & Stok</span></div>
            </div>
            <div class="topbar-actions">
                <!-- Bisa tambahkan tombol atau profil user di sini nanti -->
                <span style="font-size: 13px; color: var(--gray-600); font-weight: 600;">
                    <i class="fas fa-user-circle"></i> <?= e($_SESSION['nama'] ?? 'Staff Gudang') ?>
                </span>
            </div should>
        </header>

        <!-- PAGE CONTENT -->
        <section class="page-content">
            
            <!-- TOMBOL AKSI CEPAT -->
            <div style="margin-bottom: 22px; display: flex; gap: 10px;">
                <a href="barang-masuk.php" class="btn btn-success">
                    <i class="fas fa-arrow-down"></i> Barang Masuk
                </a>
                <a href="barang-keluar.php" class="btn btn-outline">
                    <i class="fas fa-arrow-up"></i> Barang Keluar
                </a>
                <a href="tambah-barang.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang
                </a>
            </div>

            <!-- STATS GRID (Menggunakan class persis milik kelompok) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-boxes"></i></div>
                    <div class="stat-label">Total Item</div>
                    <div class="stat-value"><?= (int)$totalItem ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-label">Stok Kritis</div>
                    <div class="stat-value"><?= (int)$kritis ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-bell"></i></div>
                    <div class="stat-label">Stok Menipis</div>
                    <div class="stat-value"><?= (int)$menipis ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-label">Stok Barang Jadi</div>
                    <div class="stat-value"><?= (int)$barangJadi ?> <small style="font-size:14px; font-weight:500;">pcs</small></div>
                </div>
            </div>

            <!-- CONTOH CARD UNTUK TABEL (Nanti dipakai di master-stok.php) -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list" style="color:var(--primary-blue); margin-right:8px;"></i>
                    Ringkasan Stok Terbaru
                </div>
                <div class="card-body">
                    <p style="color: var(--gray-500); font-size: 13.5px;">
                        <i class="fas fa-info-circle"></i> 
                        Silakan klik menu <strong>Master Stok</strong> di sidebar untuk melihat detail seluruh inventaris.
                    </p>
                </div>
            </div>

        </section>
    </main>
</div>

</body>
</html>