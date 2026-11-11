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

$query = "SELECT ps.id, ps.nama_tahap, ps.urutan, ps.status, ps.catatan, pj.id as job_id, pj.target_qty, pj.progress, pj.status as job_status, o.nomor_order, c.nama as nama_pelanggan FROM production_steps ps JOIN production_jobs pj ON ps.job_id = pj.id JOIN orders o ON pj.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.tanggal DESC, ps.urutan ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahapan Produksi - KonveksiKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b; --gray-900: #0f172a;
            --radius: 14px; --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--main-bg); color: var(--text-dark); }
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
        /* 2. CSS KONTEN ASLI ANDA (DISESUAIKAN)     */
        /* ========================================= */
        .main { flex: 1; min-width: 0; padding: 28px 32px; display: flex; flex-direction: column; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 5px; }
        .breadcrumb { font-size: 0.85rem; color: var(--gray-500); margin: 0; }
        .role-badge { background-color: #dbeafe; color: #1e40af; padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        
        .card-custom { background: white; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); margin-bottom: 20px; }
        .card-header-custom { padding: 20px; border-bottom: 1px solid var(--gray-200); font-weight: 600; font-size: 1.05rem; color: var(--gray-900); background: white; border-radius: var(--radius) var(--radius) 0 0; }
        
        .stat-card { border-radius: var(--radius-sm); padding: 20px; color: white; margin-bottom: 20px; }

        @media (max-width: 650px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; min-width: 0; padding: 18px 14px; }
            .logo { margin-bottom: 20px; }
            .nav-link { margin-bottom: 4px; padding: 11px 12px; }
            .sidebar-footer { margin-top: 15px; }
            .page-header { flex-direction: column; gap: 12px; }
            .main { padding: 20px 16px; }
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
                    <h1 class="page-title">Monitoring Tahapan Produksi</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Produksi</a></li>
                            <li class="breadcrumb-item active">Tahapan</li>
                        </ol>
                    </nav>
                </div>
                <div class="role-badge">Produksi</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card bg-secondary">
                        <h6 class="mb-1">Total Tahapan</h6>
                        <h2 class="mb-0"><?php echo $result->num_rows; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-success">
                        <h6 class="mb-1">Lulus QC</h6>
                        <h2 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) as count FROM production_steps WHERE status='lulus_qc'")->fetch_assoc()['count']; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-warning text-dark">
                        <h6 class="mb-1">Sedang Dikerjakan</h6>
                        <h2 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) as count FROM production_steps WHERE status='dikerjakan'")->fetch_assoc()['count']; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-danger">
                        <h6 class="mb-1">Gagal QC</h6>
                        <h2 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) as count FROM production_steps WHERE status='gagal_qc'")->fetch_assoc()['count']; ?></h2>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fa-solid fa-table me-2"></i>Daftar Semua Tahapan</div>
                <div class="card-body p-0">
                    <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>No. Order</th>
                                    <th>Pelanggan</th>
                                    <th>Tahapan</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th class="pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nomor_order']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['nama_pelanggan'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_tahap']); ?></td>
                                    <td><span class="badge bg-info text-dark"><?php echo $row['urutan']; ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $row['status'] == 'lulus_qc' ? 'success' : ($row['status'] == 'gagal_qc' ? 'danger' : ($row['status'] == 'dikerjakan' ? 'primary' : 'secondary')); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['catatan'] ?? '-'); ?></td>
                                    <td class="pe-4">
                                        <a href="detail_job.php?id=<?php echo $row['job_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">Belum ada tahapan produksi.</div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>