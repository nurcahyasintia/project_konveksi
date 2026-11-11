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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id']; 
    $target_qty = $_POST['target_qty']; 
    $nama_tahap = $_POST['nama_tahap'];
    
    $stmt = $conn->prepare("INSERT INTO production_jobs (order_id, target_qty, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $order_id, $target_qty);
    
    if ($stmt->execute()) {
        $job_id = $stmt->insert_id;
        $tahapan = explode(',', $nama_tahap); 
        $urutan = 1;
        
        foreach ($tahapan as $tahap) {
            $tahap = trim($tahap);
            if (!empty($tahap)) {
                $stmt2 = $conn->prepare("INSERT INTO production_steps (job_id, nama_tahap, urutan, status) VALUES (?, ?, ?, 'belum')");
                $stmt2->bind_param("isi", $job_id, $tahap, $urutan); 
                $stmt2->execute(); 
                $urutan++;
            }
        }
        
        $conn->query("UPDATE orders SET status_produksi = 'dalam_produksi' WHERE id = $order_id");
        header("Location: index.php?msg=success"); 
        exit;
    } else { 
        $error = "Gagal membuat work order: " . $conn->error; 
    }
}

$query_orders = "SELECT o.id, o.nomor_order, o.tanggal, o.deadline, c.nama as nama_pelanggan FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.status_produksi IN ('pending', 'diterima', 'disetujui') ORDER BY o.tanggal ASC";
$result_orders = $conn->query($query_orders);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Work Order - KonveksiKu</title>
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
        
        .card-custom { background: white; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); }
        .card-header-custom { padding: 20px; border-bottom: 1px solid var(--gray-200); font-weight: 600; font-size: 1.05rem; color: var(--gray-900); background: white; border-radius: var(--radius) var(--radius) 0 0; }
        
        .btn-custom-secondary { background-color: var(--gray-500); border-color: var(--gray-500); color: white; padding: 8px 16px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem; }
        .btn-custom-success { background-color: var(--success); border-color: var(--success); color: white; padding: 12px; border-radius: 6px; width: 100%; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-custom-success:hover { background-color: #059669; color: white; }
        .form-label { font-weight: 600; color: var(--gray-700); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }

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
                    <h1 class="page-title">Buat Work Order Baru</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Produksi</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <a href="index.php" class="btn-custom-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-exclamation-triangle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card-custom">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label"><i class="fa-solid fa-cart-check text-primary"></i> Pilih Order yang Siap Produksi</label>
                            <?php if ($result_orders->num_rows > 0): ?>
                                <select name="order_id" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Order --</option>
                                    <?php while ($order = $result_orders->fetch_assoc()): ?>
                                        <option value="<?php echo $order['id']; ?>">
                                            <?php echo htmlspecialchars($order['nomor_order']); ?> - 
                                            <?php echo htmlspecialchars($order['nama_pelanggan']); ?> 
                                            (Deadline: <?php echo date('d/m/Y', strtotime($order['deadline'])); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else: ?>
                                <div class="alert alert-warning"><i class="fa-solid fa-info-circle"></i> Tidak ada order yang siap produksi saat ini.</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><i class="fa-solid fa-boxes text-primary"></i> Target Quantity (pcs)</label>
                            <input type="number" name="target_qty" class="form-control form-control-lg" placeholder="Masukkan jumlah produksi" required min="1">
                            <small class="text-muted">Jumlah barang yang akan diproduksi</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><i class="fa-solid fa-list-check text-primary"></i> Tahapan Produksi</label>
                            <textarea name="nama_tahap" class="form-control" rows="4" required placeholder="Masukkan tahapan produksi, pisahkan dengan koma. Contoh: Pemotongan, Penjahitan, Finishing, QC"></textarea>
                            <small class="text-muted">Pisahkan setiap tahapan dengan <strong>koma (,)</strong>. Contoh: <code>Pemotongan, Penjahitan, Finishing, Quality Control</code></small>
                        </div>
                        
                        <button type="submit" class="btn-custom-success">
                            <i class="fa-solid fa-check-circle"></i> Buat Work Order
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>