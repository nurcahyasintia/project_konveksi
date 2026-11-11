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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { 
    header("Location: index.php"); 
    exit; 
}
$job_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_step'])) {
    $step_id = $_POST['step_id']; 
    $new_status = $_POST['new_status']; 
    $catatan = $_POST['catatan'] ?? '';
    
    $stmt = $conn->prepare("UPDATE production_steps SET status = ?, catatan = ? WHERE id = ? AND job_id = ?");
    $stmt->bind_param("ssii", $new_status, $catatan, $step_id, $job_id); 
    $stmt->execute();
    
    $total_steps = $conn->query("SELECT COUNT(*) as total FROM production_steps WHERE job_id = $job_id")->fetch_assoc()['total'];
    $completed_steps = $conn->query("SELECT COUNT(*) as total FROM production_steps WHERE job_id = $job_id AND status = 'lulus_qc'")->fetch_assoc()['total'];
    $progress_percent = ($total_steps > 0) ? round(($completed_steps / $total_steps) * 100) : 0;
    $job_status = ($progress_percent == 100) ? 'selesai' : 'proses';
    
    $conn->query("UPDATE production_jobs SET progress = $progress_percent, status = '$job_status' WHERE id = $job_id");
    header("Location: detail_job.php?id=$job_id&msg=updated"); 
    exit;
}

$query_job = "SELECT pj.*, o.nomor_order, o.tanggal, o.deadline, o.total, c.nama as nama_pelanggan FROM production_jobs pj LEFT JOIN orders o ON pj.order_id = o.id LEFT JOIN customers c ON o.customer_id = c.id WHERE pj.id = $job_id";
$result_job = $conn->query($query_job);
if ($result_job->num_rows === 0) { 
    header("Location: index.php"); 
    exit; 
}
$job = $result_job->fetch_assoc();

$query_steps = "SELECT * FROM production_steps WHERE job_id = $job_id ORDER BY urutan ASC";
$result_steps = $conn->query($query_steps);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Work Order - KonveksiKu</title>
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
        
        .btn-custom-secondary { background-color: var(--gray-500); border-color: var(--gray-500); color: white; padding: 8px 16px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
        .btn-custom-success { background-color: var(--success); border-color: var(--success); color: white; padding: 12px 24px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: none; }
        .btn-custom-success:hover { background-color: #059669; color: white; }

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
                    <h1 class="page-title">Detail Work Order</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Produksi</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>
                </div>
                <a href="index.php" class="btn-custom-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Status tahapan berhasil diupdate!</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'completed'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-trophy"></i> <strong>Produksi Selesai!</strong> Status order telah diperbarui.</div>
            <?php endif; ?>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fa-solid fa-info-circle me-2"></i>Informasi Work Order</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($job['nama_pelanggan']); ?></p>
                            <p><strong>Tanggal Order:</strong> <?php echo date('d/m/Y', strtotime($job['tanggal'])); ?></p>
                            <p><strong>Deadline:</strong> <?php echo date('d/m/Y', strtotime($job['deadline'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Target Quantity:</strong> <?php echo $job['target_qty']; ?> pcs</p>
                            <p><strong>Progress:</strong> 
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?php echo $job['progress']; ?>%"></div>
                                </div>
                                <small class="text-muted mt-1 d-block"><?php echo $job['progress']; ?>% Selesai</small>
                            </p>
                            <p><strong>Status:</strong> <span class="badge bg-<?php echo $job['status'] == 'selesai' ? 'success' : ($job['status'] == 'proses' ? 'primary' : 'secondary'); ?>"><?php echo ucfirst($job['status']); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom"><i class="fa-solid fa-list-check me-2"></i>Tahapan Produksi & Quality Control</div>
                <div class="card-body p-0">
                    <?php if ($result_steps->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Urutan</th>
                                    <th>Nama Tahap</th>
                                    <th>Status</th>
                                    <th>Catatan QC</th>
                                    <th class="pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($step = $result_steps->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><span class="badge bg-info text-dark"><?php echo $step['urutan']; ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($step['nama_tahap']); ?></strong></td>
                                    <td><span class="badge bg-<?php echo $step['status'] == 'lulus_qc' ? 'success' : ($step['status'] == 'gagal_qc' ? 'danger' : ($step['status'] == 'dikerjakan' ? 'primary' : 'secondary')); ?>"><?php echo ucfirst(str_replace('_', ' ', $step['status'])); ?></span></td>
                                    <td><?php echo htmlspecialchars($step['catatan'] ?? '-'); ?></td>
                                    <td class="pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $step['id']; ?>" <?php echo $job['status'] == 'selesai' ? 'disabled' : ''; ?>>
                                            <i class="fa-solid fa-pencil"></i> Update
                                        </button>
                                    </td>
                                </tr>
                                <div class="modal fade" id="updateModal<?php echo $step['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update: <?php echo htmlspecialchars($step['nama_tahap']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="step_id" value="<?php echo $step['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status QC</label>
                                                        <select name="new_status" class="form-select" required>
                                                            <option value="belum" <?php echo $step['status'] == 'belum' ? 'selected' : ''; ?>>Belum Dikerjakan</option>
                                                            <option value="dikerjakan" <?php echo $step['status'] == 'dikerjakan' ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                                                            <option value="lulus_qc" <?php echo $step['status'] == 'lulus_qc' ? 'selected' : ''; ?>>Lulus QC</option>
                                                            <option value="gagal_qc" <?php echo $step['status'] == 'gagal_qc' ? 'selected' : ''; ?>>Gagal QC</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan</label>
                                                        <textarea name="catatan" class="form-control" rows="3"><?php echo htmlspecialchars($step['catatan'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update_step" class="btn btn-primary">Simpan</button>
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
                        <div class="text-center py-5 text-muted">Belum ada tahapan produksi.</div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($job['progress'] >= 100 && $job['status'] != 'selesai'): ?>
            <div class="card-custom border-success" style="border-color: #10b981 !important;">
                <div class="card-body text-center p-4">
                    <h4 class="text-success"><i class="fa-solid fa-check-circle"></i> Semua Tahapan Selesai & Lulus QC!</h4>
                    <p class="text-muted mb-3">Klik tombol di bawah untuk menutup work order ini dan mengupdate status pesanan.</p>
                    <form method="POST" action="selesaikan_job.php" class="d-inline">
                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $job['order_id']; ?>">
                        <button type="submit" class="btn-custom-success"><i class="fa-solid fa-check-double"></i> Selesaikan Produksi & Update Order</button>
                    </form>
                </div>
            </div>
            <?php elseif ($job['status'] == 'selesai'): ?>
            <div class="alert alert-success text-center p-4">
                <h4><i class="fa-solid fa-trophy"></i> Produksi Selesai!</h4>
                <p class="mb-0">Work order ini telah ditutup.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>