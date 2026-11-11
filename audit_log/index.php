<?php
session_start();
require_once "../config/database.php";
require_once "../auth/role_check.php";

requireRole(['Admin', 'Owner']);

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'audit_log'; 
// ==========================================

$query = "SELECT activity_logs.id, activity_logs.aktivitas, activity_logs.referensi, activity_logs.waktu, users.nama 
          FROM activity_logs 
          LEFT JOIN users ON activity_logs.user_id = users.id 
          ORDER BY activity_logs.id DESC 
          LIMIT 100";
$logs = $conn->query($query);

function e($val) { return htmlspecialchars((string)($val ?? ""), ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - KonveksiKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 1. CSS GLOBAL & SIDEBAR (UNIFIED) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: #1E293B; --sidebar-active: #3B82F6;
            --primary-blue: #2563EB; --main-bg: #F8FAFC;
            --text-dark: #1E293B; --text-light: #94A3B8;
            --white: #FFFFFF; --table-text: #475569;
            --border-color: #E2E8F0;
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

        /* 2. CSS KHUSUS TABEL AUDIT LOG */
        .table-panel { background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        .table-heading { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 22px; flex-wrap: wrap; }
        .table-heading h2 { color: var(--text-dark); font-size: 18px; }
        .table-heading .info { font-size: 12.5px; color: var(--text-light); display: flex; align-items: center; gap: 6px; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        .data-table { width: 100%; min-width: 700px; border-collapse: collapse; }
        .data-table th { padding: 14px 12px; text-align: left; background: #F1F5F9; color: var(--table-text); font-size: 11px; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        .data-table td { padding: 15px 12px; border-bottom: 1px solid var(--border-color); color: var(--table-text); font-size: 13px; vertical-align: middle; }
        .data-table tbody tr:hover { background: #F8FAFC; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
        .badge-primary { background: #DBEAFE; color: #1D4ED8; }
        .badge-gray { background: #E2E8F0; color: #475569; }
        .empty-data { padding: 40px 20px !important; text-align: center; color: var(--text-light) !important; line-height: 1.9; }
        .empty-data i { font-size: 30px; color: #93C5FD; display: block; margin-bottom: 10px; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Audit Log</h1>
                <div class="breadcrumb">Beranda / <span>Audit Log</span></div>
            </div>
        </header>
        <section class="content">
            <div class="table-panel">
                <div class="table-heading">
                    <h2><i class="fa-solid fa-history" style="color:var(--primary-blue);margin-right:8px"></i> Riwayat Aktivitas Sistem</h2>
                    <div class="info"><i class="fa-solid fa-info-circle"></i> Menampilkan 100 aktivitas terakhir</div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">ID</th>
                                <th style="width: 20%;">Waktu</th>
                                <th style="width: 20%;">Pelaku (User)</th>
                                <th style="width: 25%;">Aktivitas</th>
                                <th style="width: 27%;">Referensi / Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs && $logs->num_rows > 0): ?>
                                <?php while ($row = $logs->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="badge badge-gray">#<?= (int)$row['id'] ?></span></td>
                                    <td><?= e(date('d M Y, H:i', strtotime($row['waktu']))) ?></td>
                                    <td><strong><?= e($row['nama'] ?? 'Sistem') ?></strong></td>
                                    <td><?= e($row['aktivitas']) ?></td>
                                    <td><span class="badge badge-primary"><?= e($row['referensi']) ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-data">
                                        <i class="fa-solid fa-inbox"></i>
                                        <strong>Belum ada riwayat aktivitas</strong><br>
                                        Aktivitas sistem akan muncul di sini setelah ada interaksi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>