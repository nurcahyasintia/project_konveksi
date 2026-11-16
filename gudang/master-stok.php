<?php
require_once '../config/database.php';
$items = $pdo->query("SELECT * FROM inventory_items ORDER BY kode ASC")->fetchAll(PDO::FETCH_ASSOC);
if (!function_exists('e')) { function e($v) { return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, "UTF-8"); } }
function getStatusStok($stok, $min) {
    if ($stok <= $min * 0.5) return ['Kritis', 'danger'];
    if ($stok <= $min) return ['Menipis', 'warning'];
    return ['Aman', 'success'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Stok - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ===== CSS SIDEBAR & LAYOUT (IDENTIK DENGAN FILE LAIN) ===== */
        :root {
            --sidebar-bg: #1E293B; --sidebar-active: #3B82F6;
            --primary-blue: #2563EB; --main-bg: #F8FAFC;
            --white: #FFFFFF; --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b; --gray-900: #0f172a;
            --radius: 14px; --radius-sm: 10px; --shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--main-bg); color: var(--gray-800); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .app { display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: 250px; min-width: 250px; background: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; padding: 25px 16px 18px; }
        .logo { display: flex; align-items: center; gap: 10px; padding: 0 12px; margin-bottom: 38px; font-size: 23px; font-weight: 800; color: var(--white); }
        .logo i { color: var(--primary-blue); font-size: 25px; }
        .logo span { color: #60A5FA; }
        .nav-category { color: var(--gray-400); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; padding: 0 13px; margin: 20px 0 12px 0; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 13px 14px; margin-bottom: 7px; color: #CBD5E1; font-size: 14px; font-weight: 500; border-radius: 9px; transition: .2s; }
        .nav-link i { width: 20px; text-align: center; font-size: 16px; }
        .nav-link:hover { background: #334155; color: var(--white); }
        .nav-link.active { background: var(--sidebar-active); color: var(--white); box-shadow: 0 4px 12px rgba(59, 130, 246, .2); }
        .sidebar-footer { display: flex; align-items: center; gap: 11px; border-top: 1px solid #334155; padding: 20px 8px 0; margin-top: auto; }
        .avatar { width: 42px; height: 42px; min-width: 42px; border-radius: 50%; background: var(--primary-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; }
        .user-info { min-width: 0; }
        .user-name { font-size: 12px; font-weight: 700; color: var(--white); line-height: 1.5; }
        .user-role { font-size: 11px; color: var(--gray-400); margin-top: 3px; }
        .logout-btn { margin-left: auto; color: var(--gray-400); font-size: 16px; }
        .logout-btn:hover { color: var(--white); }

        /* MAIN CONTENT */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { background: var(--white); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; z-index: 50; }
        .page-heading h1 { font-size: 20px; font-weight: 800; color: var(--gray-900); margin: 0; }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .page-content { padding: 28px 32px; flex: 1; }

        /* CARD & TABLE */
        .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); overflow: hidden; margin-bottom: 22px; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid var(--gray-200); font-size: 15px; font-weight: 700; color: var(--gray-900); display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 0; }
        .table-responsive { overflow-x: auto; }
        .custom-table { width: 100%; border-collapse: collapse; text-align: left; }
        .custom-table th { padding: 14px 22px; font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
        .custom-table td { padding: 16px 22px; font-size: 13.5px; color: var(--gray-700); border-bottom: 1px solid var(--gray-100); }
        .custom-table tr:last-child td { border-bottom: none; }
        .custom-table tr:hover { background: var(--gray-50); }

        /* BADGE */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-danger { background: #fee2e2; color: var(--danger); }
        .badge-warning { background: #fef3c7; color: var(--warning); }
        .badge-success { background: #d1fae5; color: var(--success); }

        /* BUTTON */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: var(--radius-sm); font-size: 12.5px; font-weight: 600; border: none; cursor: pointer; transition: all .2s; text-decoration: none; }
        .btn-primary { background: var(--primary-blue); color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 5px 10px; font-size: 11.5px; }

        /* ALERT */
        .alert { padding: 12px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
<div class="app">
    <?php $base_path = '../'; $active_page = 'gudang'; include '../includes/sidebar.php'; ?>

    <main class="main">
        <header class="topbar">
            <div class="page-heading">
                <h1>Master Stok</h1>
                <div class="breadcrumb">Gudang & Stok / <span>Master Bahan & Barang Jadi</span></div>
            </div>
            <div class="topbar-actions">
                <a href="tambah-barang.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang
                </a>
            </div>
        </header>

        <section class="page-content">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($_GET['msg']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['err'])): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($_GET['err']) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <span><i class="fas fa-boxes" style="color:var(--primary-blue); margin-right:8px;"></i> Daftar Inventaris Gudang</span>
                    <span style="font-size:12px; color:var(--gray-500); font-weight:400;">Total: <?= count($items) ?> Item</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th style="text-align:center;">Stok</th>
                                    <th style="text-align:center;">Min. Stok</th>
                                    <th>Status</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($items) > 0): ?>
                                    <?php foreach ($items as $item): 
                                        $statusData = getStatusStok($item['stok'], $item['stok_minimum']);
                                        $label = $statusData[0];
                                        $type = $statusData[1];
                                    ?>
                                    <tr>
                                        <td style="font-family:monospace; font-weight:600; color:var(--gray-600);"><?= e($item['kode']) ?></td>
                                        <td style="font-weight:600; color:var(--gray-800);"><?= e($item['nama']) ?></td>
                                        <td><?= e($item['kategori']) ?></td>
                                        <td><?= e($item['satuan']) ?></td>
                                        <td style="text-align:center; font-weight:700; color:var(--gray-900);"><?= (int)$item['stok'] ?></td>
                                        <td style="text-align:center; color:var(--gray-500);"><?= (int)$item['stok_minimum'] ?></td>
                                        <td><span class="badge badge-<?= $type ?>"><?= $label ?></span></td>
                                        <td style="text-align:center;">
                                            <a href="edit-barang.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="proses/hapus_barang.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin hapus <?= e($item['nama']) ?>?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding:40px; color:var(--gray-400);">
                                            <i class="fas fa-inbox" style="font-size:24px; margin-bottom:10px; display:block;"></i>
                                            Belum ada data barang. Silakan tambah barang baru.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>