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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qc_step_id'])) {
    $step_id = $_POST['qc_step_id']; 
    $qc_status = $_POST['qc_status']; 
    $catatan_qc = $_POST['catatan_qc'] ?? '';
    
    $stmt = $conn->prepare("UPDATE production_steps SET status = ?, catatan = ? WHERE id = ?");
    $stmt->bind_param("ssi", $qc_status, $catatan_qc, $step_id); 
    $stmt->execute();
    
    $job = $conn->query("SELECT job_id FROM production_steps WHERE id = $step_id")->fetch_assoc(); 
    $job_id = $job['job_id'];
    
    $total_steps = $conn->query("SELECT COUNT(*) as total FROM production_steps WHERE job_id = $job_id")->fetch_assoc()['total'];
    $completed_steps = $conn->query("SELECT COUNT(*) as total FROM production_steps WHERE job_id = $job_id AND status = 'lulus_qc'")->fetch_assoc()['total'];
    $progress_percent = ($total_steps > 0) ? round(($completed_steps / $total_steps) * 100) : 0;
    $job_status = ($progress_percent == 100) ? 'selesai' : 'proses';
    
    $conn->query("UPDATE production_jobs SET progress = $progress_percent, status = '$job_status' WHERE id = $job_id");
    header("Location: qc.php?msg=updated"); 
    exit;
}

$query_qc = "SELECT ps.id, ps.nama_tahap, ps.urutan, ps.status, ps.catatan, pj.id as job_id, pj.target_qty, o.nomor_order, c.nama as nama_pelanggan FROM production_steps ps JOIN production_jobs pj ON ps.job_id = pj.id JOIN orders o ON pj.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id WHERE ps.status = 'dikerjakan' ORDER BY o.tanggal ASC, ps.urutan ASC";
$result_qc = $conn->query($query_qc);

$qc_history = "SELECT ps.nama_tahap, ps.catatan, ps.status, o.nomor_order, c.nama as nama_pelanggan FROM production_steps ps JOIN production_jobs pj ON ps.job_id = pj.id JOIN orders o ON pj.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id WHERE ps.status IN ('lulus_qc', 'gagal_qc') ORDER BY ps.id DESC LIMIT 10";
$result_history = $conn->query($qc_history);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Control - KonveksiKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
                    <h1 class="page-title">Quality Control (QC)</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Produksi</a></li>
                            <li class="breadcrumb-item active">QC</li>
                        </ol>
                    </nav>
                </div>
                <div class="role-badge">Produksi</div>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle"></i> Hasil QC berhasil disimpan!</div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card-custom">
                        <div class="card-header-custom bg-warning text-dark"><i class="bi bi-clock-history me-2"></i>Menunggu QC (<?php echo $result_qc->num_rows; ?>)</div>
                        <div class="card-body p-0">
                            <?php if ($result_qc->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Order</th>
                                            <th>Tahapan</th>
                                            <th class="pe-4">Aksi QC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while ($step = $result_qc->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4"><?php echo $no++; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($step['nomor_order']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($step['nama_pelanggan'] ?? '-'); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark"><?php echo $step['urutan']; ?></span> 
                                                <?php echo htmlspecialchars($step['nama_tahap']); ?>
                                            </td>
                                            <td class="pe-4">
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#qcModal<?php echo $step['id']; ?>">
                                                    <i class="bi bi-check-lg"></i> QC
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="qcModal<?php echo $step['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Quality Control: <?php echo htmlspecialchars($step['nama_tahap']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="qc_step_id" value="<?php echo $step['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Hasil QC</label>
                                                                <select name="qc_status" class="form-select" required>
                                                                    <option value="lulus_qc">Lulus QC</option>
                                                                    <option value="gagal_qc">Gagal QC (Revisi)</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Catatan QC</label>
                                                                <textarea name="catatan_qc" class="form-control" rows="3"><?php echo htmlspecialchars($step['catatan'] ?? ''); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan Hasil QC</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-check-circle display-6 text-success d-block mb-2"></i>
                                    Tidak ada tahapan yang menunggu QC
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom">
                        <div class="card-header-custom bg-success text-white"><i class="bi bi-clock me-2"></i>Riwayat QC Terbaru</div>
                        <div class="card-body p-0">
                            <?php if ($result_history->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($hist = $result_history->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($hist['nama_tahap']); ?></h6>
                                        <span class="badge bg-<?php echo $hist['status'] == 'lulus_qc' ? 'success' : 'danger'; ?>">
                                            <?php echo $hist['status'] == 'lulus_qc' ? 'Lulus' : 'Gagal'; ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small text-muted"><?php echo htmlspecialchars($hist['nomor_order']); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($hist['catatan'] ?? '-'); ?></small>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">Belum ada riwayat QC</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>