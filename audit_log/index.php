<?php
session_start();
require_once "../config/database.php";
require_once "../auth/role_check.php";

// Audit log biasanya hanya boleh dilihat oleh Admin dan Owner
requireRole(['Admin', 'Owner']);

// Ambil data log dari database, diurutkan dari yang terbaru
$query = "SELECT activity_logs.id, activity_logs.aktivitas, activity_logs.referensi, activity_logs.waktu, users.nama 
          FROM activity_logs 
          LEFT JOIN users ON activity_logs.user_id = users.id 
          ORDER BY activity_logs.id DESC 
          LIMIT 100"; // Tampilkan 100 log terbaru
$logs = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ============================================================
           KONVEKSIKU THEME — CSS INTERNAL
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
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--gray-100); color: var(--gray-800);
            line-height: 1.6; -webkit-font-smoothing: antialiased;
            padding: 24px;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 {
            font-size: 22px; font-weight: 800; color: var(--gray-900);
            letter-spacing: -.3px;
        }
        .page-header .breadcrumb { font-size: 13px; color: var(--gray-500); margin-top: 2px; }

        /* ===== NAVIGASI ===== */
        .nav-links {
            display: flex; gap: 8px; flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .nav-links a {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: var(--radius-sm);
            background: #fff; color: var(--gray-600);
            text-decoration: none; font-size: 13px; font-weight: 600;
            border: 1px solid var(--gray-300);
            transition: all .2s;
        }
        .nav-links a:hover {
            background: var(--primary-light); color: var(--primary-dark);
            border-color: var(--primary);
        }
        .nav-links a.danger:hover {
            background: #fee2e2; color: var(--danger); border-color: var(--danger);
        }

        /* ===== CARD ===== */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            overflow: hidden; margin-bottom: 22px;
        }
        .card-header {
            padding: 18px 22px; border-bottom: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        .card-header h2 {
            font-size: 15px; font-weight: 700; color: var(--gray-900);
            letter-spacing: -.2px;
        }
        .card-header .info {
            font-size: 12.5px; color: var(--gray-500);
            display: flex; align-items: center; gap: 6px;
        }

        /* ===== TABEL ===== */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 12px 16px; font-size: 11px;
            text-transform: uppercase; letter-spacing: .6px;
            color: var(--gray-500); font-weight: 700;
            border-bottom: 1px solid var(--gray-200); background: var(--gray-50);
            white-space: nowrap;
        }
        tbody td {
            padding: 14px 16px; font-size: 13.5px; color: var(--gray-700);
            border-bottom: 1px solid var(--gray-100);
            vertical-align: top;
        }
        tbody tr { transition: background .15s; }
        tbody tr:hover { background: var(--gray-50); }
        tbody tr:last-child td { border-bottom: none; }

        /* ===== BADGE ===== */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 11px; border-radius: 20px;
            font-size: 11.5px; font-weight: 700; white-space: nowrap;
        }
        .badge-primary { background: var(--primary-light); color: var(--primary-dark); }
        .badge-gray { background: var(--gray-200); color: var(--gray-600); }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            padding: 60px 20px; text-align: center; color: var(--gray-500);
        }
        .empty-state i {
            font-size: 48px; color: var(--gray-300); margin-bottom: 14px;
        }
        .empty-state h4 {
            font-size: 16px; color: var(--gray-700); margin-bottom: 6px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body { padding: 16px; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .nav-links { flex-direction: column; }
            .nav-links a { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <div>
            <h1>Audit Log</h1>
            <div class="breadcrumb">Home / Audit Log</div>
        </div>
    </div>

    <!-- ===== NAVIGASI ===== -->
    <div class="nav-links">
        <a href="../index.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="../user/index.php"><i class="fas fa-users"></i> Manajemen User</a>
        <a href="../auth/logout.php" class="danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- ===== TABEL AUDIT LOG ===== -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-history" style="color:var(--primary);margin-right:6px"></i> Riwayat Aktivitas Sistem</h2>
            <div class="info">
                <i class="fas fa-info-circle"></i>
                Menampilkan 100 aktivitas terakhir
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 6%;">ID</th>
                        <th style="width: 20%;">Waktu</th>
                        <th style="width: 20%;">Pelaku (User)</th>
                        <th style="width: 25%;">Aktivitas</th>
                        <th style="width: 29%;">Referensi / Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs && $logs->num_rows > 0): ?>
                        <?php while ($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge badge-gray">#<?= $row['id'] ?></span></td>
                            <td><?= htmlspecialchars($row['waktu']) ?></td>
                            <td><strong><?= htmlspecialchars($row['nama'] ?? 'Sistem') ?></strong></td>
                            <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($row['referensi']) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h4>Belum ada riwayat aktivitas</h4>
                                    <p>Aktivitas sistem akan muncul di sini setelah ada interaksi dari pengguna.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>