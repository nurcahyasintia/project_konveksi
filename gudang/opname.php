<?php
require_once '../config/database.php';
try { $bahan_list = $pdo->query("SELECT id, nama, satuan, stok FROM inventory_items ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC); } catch (Exception $e) { $bahan_list = []; }
if (!function_exists('e')) { function e($v) { return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, "UTF-8"); } }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Opname - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
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

        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { background: var(--white); padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; z-index: 50; }
        .page-heading h1 { font-size: 20px; font-weight: 800; color: var(--gray-900); margin: 0; }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .page-content { padding: 28px 32px; flex: 1; }

        .card { background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); max-width: 800px; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid var(--gray-200); font-weight: 700; font-size: 15px; color: var(--gray-900); }
        .card-body { padding: 24px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 14px; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; }
        .form-control { padding: 10px 14px; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-size: 14px; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary-blue); }
        .form-control:read-only { background: var(--gray-100); color: var(--gray-500); cursor: not-allowed; }
        .btn { padding: 10px 20px; border-radius: var(--radius-sm); font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; font-size: 13px; }
        .btn-primary { background: var(--primary-blue); color: white; margin-top: 24px; }
        .btn-primary:hover { background: #1d4ed8; }
        .alert { padding: 12px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13.5px; font-weight: 500; }
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
                <h1>Stock Opname</h1>
                <div class="breadcrumb">Gudang & Stok / <span>Stock Opname</span></div>
            </div>
        </header>
        <section class="page-content">
            <?php if (isset($_GET['msg'])): ?><div class="alert alert-success"><?= e($_GET['msg']) ?></div><?php endif; ?>
            <?php if (isset($_GET['err'])): ?><div class="alert alert-error"><?= e($_GET['err']) ?></div><?php endif; ?>
            <div class="card">
                <div class="card-header">Form Stock Opname</div>
                <div class="card-body">
                    <form action="proses/simpan_opname.php" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tanggal Opname</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Bahan</label>
                                <select name="item_id" id="bahanSelect" class="form-control" required>
                                    <option value="">-- Pilih Bahan --</option>
                                    <?php foreach ($bahan_list as $b): ?>
                                        <option value="<?= $b['id'] ?>" data-satuan="<?= e($b['satuan']) ?>"><?= e($b['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Stok Sistem</label>
                                <input type="text" id="stokSistem" class="form-control" readonly placeholder="Pilih bahan dulu...">
                                <input type="hidden" name="stok_sistem_value" id="stokSistemValue">
                            </div>
                            <div class="form-group">
                                <label>Stok Fisik</label>
                                <input type="number" name="stok_fisik" id="stokFisik" class="form-control" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:20px;">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan selisih, kerusakan, dll..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Opname</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
document.getElementById('bahanSelect').addEventListener('change', function() {
    const itemId = this.value;
    const stokInput = document.getElementById('stokSistem');
    const stokHidden = document.getElementById('stokSistemValue');
    const satuan = this.options[this.selectedIndex].getAttribute('data-satuan') || '';
    if (!itemId) { stokInput.value = ''; stokHidden.value = ''; return; }
    stokInput.value = 'Memuat...';
    fetch('proses/get_stok.php?item_id=' + itemId)
        .then(r => r.json())
        .then(data => {
            if (data.success) { stokInput.value = data.stok + ' ' + satuan; stokHidden.value = data.stok; }
            else { stokInput.value = 'Error'; stokHidden.value = ''; }
        });
});
</script>
</body>
</html>