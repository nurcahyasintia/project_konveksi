<?php
require_once '../config/database.php';
require_once '../auth/auth_check.php';
require_once '../auth/role_check.php';
requireRole(['Produksi', 'Admin', 'Owner']);

// ==========================================
// VARIABEL UNTUK SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'produksi';
// ==========================================

$query = "SELECT pj.id, pj.order_id, o.nomor_order, o.tanggal as tanggal_order, o.deadline, c.nama as nama_pelanggan, pj.target_qty, pj.progress, pj.status, pj.started_at, pj.finished_at, pj.created_at FROM production_jobs pj LEFT JOIN orders o ON pj.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id ORDER BY pj.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produksi - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============================================================
           1. CSS GLOBAL & SIDEBAR (STANDAR ANGGOTA 2 + VARIABEL ANGGOTA 3)
           ============================================================ */
        :root {
            /* Universal Anggota 2 */
            --sidebar-bg: #1E293B;
            --sidebar-active: #3B82F6;
            --primary-blue: #2563EB;
            --main-bg: #F8FAFC;
            --text-dark: #1E293B;
            --text-light: #94A3B8;
            --white: #FFFFFF;
            --table-text: #475569;
            --border-color: #E2E8F0;
            
            /* Spesifik Anggota 3 (Dipertahankan agar UI Produksi tidak rusak) */
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
            background: var(--main-bg); color: var(--text-dark);
            line-height: 1.6; -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        .app { display: flex; min-height: 100vh; }

        /* SIDEBAR UNIVERSAL */
        .sidebar {
            width: 250px; min-width: 250px; background: var(--sidebar-bg);
            color: var(--white); display: flex; flex-direction: column;
            padding: 25px 16px 18px;
        }
        .logo {
            display: flex; align-items: center; gap: 10px; padding: 0 12px;
            margin-bottom: 38px; font-size: 23px; font-weight: 800; color: var(--white);
        }
        .logo i { color: var(--primary-blue); font-size: 25px; }
        .logo span { color: #60A5FA; }
        .nav-title {
            color: var(--text-light); font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.2px;
            padding: 0 13px; margin-bottom: 12px;
        }
        .nav-link {
            display: flex; align-items: center; gap: 12px; padding: 13px 14px;
            margin-bottom: 7px; color: #CBD5E1; font-size: 14px; font-weight: 500;
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
            border-top: 1px solid #334155; padding: 20px 8px 0; margin-top: auto;
        }
        .avatar {
            width: 42px; height: 42px; min-width: 42px; border-radius: 50%;
            background: var(--primary-blue); color: var(--white);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; flex-shrink: 0;
        }
        .user-info { min-width: 0; }
        .user-name { font-size: 12px; font-weight: 700; color: var(--white); line-height: 1.5; overflow-wrap: anywhere; }
        .user-role { font-size: 11px; color: var(--text-light); margin-top: 3px; }

        /* MAIN CONTENT WRAPPER (Pengganti .main-content lama) */
        .main {
            flex: 1; min-width: 0; padding: 28px 32px;
            display: flex; flex-direction: column;
        }

        /* ============================================================
           2. CSS KONTEN SPESIFIK (PUNYA ANGGOTA 3 - DIPERTAHANKAN)
           ============================================================ */
        .page-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 26px; background: var(--white); padding: 20px 22px;
            border-radius: var(--radius); border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        .page-title {
            font-size: 20px; font-weight: 800; color: var(--text-dark);
            letter-spacing: -.3px; margin-bottom: 4px;
        }
        .breadcrumb {
            font-size: 12.5px; color: var(--text-light);
            display: flex; gap: 6px; list-style: none; padding: 0; margin: 0;
        }
        .breadcrumb a { color: var(--text-light); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary-blue); }
        .breadcrumb-item.active { color: var(--gray-700); font-weight: 600; }
        .role-badge {
            background: var(--primary-light); color: var(--primary-dark);
            padding: 6px 16px; border-radius: 20px;
            font-size: 12.5px; font-weight: 700; display: inline-flex; align-items: center;
        }

        .info-bar {
            display: flex; justify-content: space-between; align-items: center;
            background: var(--white); padding: 16px 22px; border-radius: var(--radius);
            margin-bottom: 22px; border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        .info-text {
            color: var(--gray-600); font-size: 13.5px; margin: 0;
            display: flex; align-items: center; gap: 8px;
        }
        .info-text i { color: var(--primary-blue); }

        .btn-custom-primary {
            background: var(--primary-blue); border: none; color: #fff;
            padding: 10px 18px; border-radius: var(--radius-sm);
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; font-weight: 600; font-size: 13.5px;
            cursor: pointer; transition: all .2s; font-family: inherit;
        }
        .btn-custom-primary:hover {
            background: #1D4ED8; color: #fff;
            box-shadow: 0 6px 16px rgba(37,99,235,.35);
        }
        .btn-custom-success {
            background: var(--success); border: none; color: #fff;
            padding: 9px 16px; border-radius: var(--radius-sm);
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none; font-weight: 600; font-size: 13px;
            cursor: pointer; transition: all .2s; font-family: inherit;
        }
        .btn-custom-success:hover {
            background: #059669; color: #fff;
            box-shadow: 0 6px 16px rgba(16,185,129,.35);
        }

        .card-custom {
            background: var(--white); border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--border-color);
            overflow: hidden;
        }
        .card-header-custom {
            padding: 18px 22px; border-bottom: 1px solid var(--border-color);
            font-weight: 700; font-size: 15px; color: var(--text-dark);
            letter-spacing: -.2px; background: var(--white);
        }

        .table-responsive { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; margin: 0; }
        .table thead th {
            text-align: left; padding: 12px 16px; font-size: 11px;
            text-transform: uppercase; letter-spacing: .6px;
            color: var(--table-text); font-weight: 700;
            border-bottom: 1px solid var(--border-color); background: var(--gray-50);
        }
        .table tbody td {
            padding: 14px 16px; font-size: 13.5px; color: var(--table-text);
            border-bottom: 1px solid var(--gray-100); vertical-align: middle;
        }
        .table tbody tr { transition: background .15s; }
        .table tbody tr:hover { background: var(--main-bg); }
        .table tbody tr:last-child td { border-bottom: none; }
        .table .ps-4 { padding-left: 22px !important; }
        .table .pe-4 { padding-right: 22px !important; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 11px; border-radius: 20px;
            font-size: 11.5px; font-weight: 700; white-space: nowrap;
        }
        .badge.bg-secondary, .badge.bg-gray { background: var(--gray-200) !important; color: var(--gray-600) !important; }
        .badge.bg-primary { background: var(--primary-light) !important; color: var(--primary-dark) !important; }
        .badge.bg-success { background: #d1fae5 !important; color: #065f46 !important; }
        .badge.bg-warning { background: #fef3c7 !important; color: #92400e !important; }
        .badge.bg-danger { background: #fee2e2 !important; color: #991b1b !important; }

        .progress {
            background: var(--gray-200); border-radius: 4px; overflow: hidden;
            display: flex; height: 8px;
        }
        .progress-bar {
            border-radius: 4px; transition: width .4s ease;
            background: var(--primary-blue) !important;
        }

        .btn-outline-primary {
            color: var(--primary-blue); border: 1px solid var(--gray-300);
            background: var(--white); padding: 6px 12px; border-radius: var(--radius-sm);
            font-size: 12.5px; font-weight: 600; display: inline-flex;
            align-items: center; gap: 6px; text-decoration: none;
            transition: all .2s; font-family: inherit;
        }
        .btn-outline-primary:hover {
            background: var(--primary-light); border-color: var(--primary-blue);
            color: var(--primary-dark);
        }

        .empty-state { padding: 60px 20px; text-align: center; color: var(--text-light); }
        .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 14px; }
        .empty-state h5 { font-size: 16px; color: var(--gray-700); font-weight: 700; margin-bottom: 6px; }
        .empty-state p { font-size: 13.5px; color: var(--text-light); margin-bottom: 16px; }

        @media (max-width: 1000px) {
            .main { padding: 22px; }
        }
        @media (max-width: 650px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; min-width: 0; padding: 18px 14px; }
            .logo { margin-bottom: 20px; }
            .nav-link { margin-bottom: 4px; padding: 11px 12px; }
            .sidebar-footer { margin-top: 15px; }
            .page-header { flex-direction: column; gap: 12px; }
            .info-bar { flex-direction: column; gap: 12px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- ===== SIDEBAR UNIVERSAL ===== -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Manajemen Produksi</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../index.php">Beranda</a></li>
                            <li class="breadcrumb-item active">Produksi</li>
                        </ol>
                    </nav>
                </div>
                <div class="role-badge">Produksi</div>
            </div>

            <div class="info-bar">
                <p class="info-text"><i class="fas fa-circle-info"></i> Kelola work order, tahapan produksi, dan quality control</p>
                <a href="tambah_job.php" class="btn-custom-success"><i class="fas fa-plus-circle"></i> Buat Work Order Baru</a>
            </div>

            <div class="card-custom">
                <div class="card-header-custom">Daftar Work Order</div>
                <div class="card-body p-0">
                    <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>No. Order</th>
                                    <th>Pelanggan</th>
                                    <th>Deadline</th>
                                    <th>Target</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th class="pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): 
                                    $statusBadge = ['pending' => 'secondary', 'proses' => 'primary', 'selesai' => 'success', 'dibatalkan' => 'danger'];
                                    $progressPercent = $row['progress'] > 0 ? round(($row['progress'] / $row['target_qty']) * 100) : 0;
                                ?>
                                <tr>
                                    <td class="ps-4"><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nomor_order']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['deadline'])); ?></td>
                                    <td><?php echo $row['target_qty']; ?> pcs</td>
                                    <td>
                                        <div class="progress" style="width: 80px; height: 6px;">
                                            <div class="progress-bar" style="width: <?php echo $progressPercent; ?>%"></div>
                                        </div>
                                        <small style="color: var(--text-light); font-size: 11px;"><?php echo $progressPercent; ?>%</small>
                                    </td>
                                    <td><span class="badge bg-<?php echo $statusBadge[$row['status']] ?? 'secondary'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    <td class="pe-4">
                                        <a href="detail_job.php?id=<?php echo $row['id']; ?>" class="btn-outline-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>Belum ada work order produksi</h5>
                        <p>Mulai dengan membuat work order pertama Anda.</p>
                        <a href="tambah_job.php" class="btn-custom-primary"><i class="fas fa-plus-circle"></i> Buat Work Order Pertama</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>