<?php
require_once "../config/database.php";

// ==========================================
// VARIABEL UNTUK SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'produk';
// ==========================================

$query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$total_produk = $query ? mysqli_num_rows($query) : 0;

function amanProduk($nilai) {
    return htmlspecialchars((string)($nilai ?? "-"), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk | KonveksiKu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        .topbar-label { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #EFF6FF; border-radius: 9px; color: var(--primary-blue); font-size: 12px; font-weight: 700; }
        .content { padding: 30px 32px; }
        .summary-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 13px; padding: 22px; display: flex; align-items: center; gap: 18px; margin-bottom: 25px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); }
        .summary-icon { width: 55px; height: 55px; border-radius: 13px; background: #DBEAFE; color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-size: 23px; flex-shrink: 0; }
        .summary-label { color: var(--text-light); font-size: 12px; margin-bottom: 7px; }
        .summary-number { color: var(--text-dark); font-size: 27px; font-weight: 800; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 15px; border: none; border-radius: 8px; font-family: inherit; font-size: 12px; font-weight: 700; cursor: pointer; transition: .2s; }
        .btn-primary { background: var(--primary-blue); color: var(--white); }
        .btn-primary:hover { background: #1D4ED8; }
        .btn-secondary { background: #E2E8F0; color: var(--text-dark); }
        .btn-secondary:hover { background: #CBD5E1; }
        .table-panel { background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; box-shadow: 0 4px 16px rgba(15, 23, 42, .035); margin-bottom: 20px; }
        .table-heading { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 22px; }
        .table-heading h2 { color: var(--text-dark); font-size: 18px; margin-bottom: 7px; }
        .table-heading p { color: var(--text-light); font-size: 12px; line-height: 1.6; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        .data-table { width: 100%; min-width: 700px; border-collapse: collapse; }
        .data-table th { padding: 14px 12px; text-align: left; background: #F1F5F9; color: var(--table-text); font-size: 11px; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        .data-table td { padding: 15px 12px; border-bottom: 1px solid var(--border-color); color: var(--table-text); font-size: 12px; vertical-align: middle; }
        .data-table tbody tr:hover { background: #F8FAFC; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .empty-data { padding: 40px 20px !important; text-align: center; color: var(--text-light) !important; line-height: 1.9; }
        .empty-data i { font-size: 30px; color: #93C5FD; display: block; margin-bottom: 10px; }
        .footer-note { text-align: center; color: var(--text-light); font-size: 11px; padding: 24px 10px 5px; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Data Produk</h1>
                <div class="breadcrumb">Beranda / <span>Produk</span></div>
            </div>
            <div class="topbar-label"><i class="fa-solid fa-circle-check"></i> Sistem Konveksi</div>
        </header>
        <section class="content">
            <div class="summary-card">
                <div class="summary-icon"><i class="fa-solid fa-shirt"></i></div>
                <div>
                    <div class="summary-label">Total Produk</div>
                    <div class="summary-number"><?= $total_produk; ?></div>
                </div>
            </div>
            <div class="table-panel">
                <div class="table-heading">
                    <div>
                        <h2>Daftar Produk</h2>
                        <p>Data produk yang tersimpan di database.</p>
                    </div>
                    <div>
                        <a href="tambah.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
                        <a href="../index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr><th>No.</th><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th>Harga Jual</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php if ($query && mysqli_num_rows($query) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= amanProduk($row["kode"] ?? "-"); ?></td>
                                    <td><?= amanProduk($row["nama"] ?? "-"); ?></td>
                                    <td><?= amanProduk($row["kategori"] ?? "-"); ?></td>
                                    <td><?= amanProduk($row["harga_jual"] ?? "-"); ?></td>
                                    <td><?= amanProduk($row["status"] ?? "-"); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= (int)$row["id"]; ?>" class="btn btn-primary" style="padding:8px 10px;"><i class="fa-solid fa-pen"></i></a>
                                        <a href="hapus.php?id=<?= (int)$row["id"]; ?>" class="btn" style="background:#FEE2E2;color:#B91C1C;padding:8px 10px;" onclick="return confirm('Yakin hapus?')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="empty-data"><i class="fa-solid fa-inbox"></i>Belum ada data produk.<br>Klik tombol Tambah Produk untuk menambahkan data.</td></tr>
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