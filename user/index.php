<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$cari = trim($_GET["cari"] ?? "");

$sql = "SELECT u.id, u.nama, u.email, u.status, r.nama_role
        FROM users u
        LEFT JOIN roles r ON r.id = u.role_id";

if ($cari !== "") {
    $sql .= " WHERE u.nama LIKE ? OR u.email LIKE ? OR r.nama_role LIKE ?";
}

$sql .= " ORDER BY u.id DESC";

$stmt = $conn->prepare($sql);

if ($cari !== "") {
    $keyword = "%" . $cari . "%";
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
}

$stmt->execute();
$users = $stmt->get_result();

$pesan = $_GET["pesan"] ?? "";
$pesanTampil = [
    "berhasil_hapus" => "Akun berhasil dihapus.",
    "gagal_hapus" => "Akun gagal dihapus.",
    "tidak_ditemukan" => "Akun tidak ditemukan.",
    "akun_masih_terhubung" => "Akun masih terhubung ke data karyawan. Akun tidak dihapus.",
    "akun_tidak_bisa_dihapus" => "Akun sendiri atau ID tidak valid tidak dapat dihapus."
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil &amp; Akun - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ============================================================
           KONVEKSIKU THEME — CSS INTERNAL (Profil & Akun)
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--gray-100); color: var(--gray-800);
            line-height: 1.6; -webkit-font-smoothing: antialiased;
        }

        /* ===== LAYOUT ===== */
        .app-layout { display: flex; min-height: 100vh; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px; background: var(--dark); color: #fff;
            display: flex; flex-direction: column;
            position: fixed; inset: 0 auto 0 0; height: 100vh; z-index: 100;
        }
        .sidebar-brand {
            padding: 22px 20px; border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 12px;
            font-size: 20px; font-weight: 800; letter-spacing: -.3px;
        }
        .brand-icon {
            background: rgba(37,99,235,.15); padding: 8px; border-radius: 10px;
            font-size: 16px; line-height: 1;
        }
        .brand-accent { color: var(--primary); }
        .sidebar-label {
            font-size: 10.5px; text-transform: uppercase; letter-spacing: 1.4px;
            color: var(--gray-500); padding: 16px 20px 8px; font-weight: 700;
        }
        .sidebar-nav { flex: 1; padding: 8px 12px; overflow-y: auto; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all .2s; margin-bottom: 2px;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar-nav a.active {
            background: var(--primary); color: #fff;
            box-shadow: 0 4px 12px rgba(37,99,235,.35);
        }
        .sidebar-bottom {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-mini { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
        }
        .user-mini strong { font-size: 13.5px; font-weight: 600; color: #fff; display: block; }
        .user-mini small { font-size: 11.5px; color: var(--gray-500); }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1; margin-left: 260px; display: flex;
            flex-direction: column; min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff; padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--gray-200);
            position: sticky; top: 0; z-index: 50;
            flex-wrap: wrap; gap: 12px;
        }
        .page-heading h1 {
            font-size: 20px; font-weight: 800; color: var(--gray-900);
            letter-spacing: -.3px;
        }
        .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .search-box {
            padding: 9px 16px; border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 13.5px;
            font-family: inherit; width: 240px; transition: all .2s;
        }
        .search-box:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .admin-pill {
            background: var(--primary-light); color: var(--primary-dark);
            font-size: 12px; font-weight: 700;
            padding: 6px 14px; border-radius: 20px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content { padding: 28px 32px; flex: 1; }

        /* ===== ALERT ===== */
        .alert-info {
            padding: 14px 18px; border-radius: var(--radius-sm);
            background: var(--primary-light); color: var(--primary-dark);
            border-left: 3px solid var(--primary);
            font-size: 13.5px; font-weight: 500; margin-bottom: 16px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            overflow: hidden; margin-bottom: 22px;
        }
        .card-header {
            padding: 18px 22px; border-bottom: 1px solid var(--gray-200);
            font-size: 15px; font-weight: 700; color: var(--gray-900);
            letter-spacing: -.2px;
        }

        /* ===== TABEL ===== */
        .table-wrapper { overflow-x: auto; }
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
            vertical-align: middle;
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
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-primary { background: var(--primary-light); color: var(--primary-dark); }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: var(--gray-200); color: var(--gray-600); }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 14px; border-radius: var(--radius-sm);
            font-size: 12.5px; font-weight: 600; border: none;
            cursor: pointer; transition: all .2s; text-decoration: none;
            font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); box-shadow: 0 6px 16px rgba(37,99,235,.35); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #dc2626; box-shadow: 0 6px 16px rgba(239,68,68,.35); }
        .btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }

        .aksi-group { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            padding: 40px 20px; text-align: center; color: var(--gray-500);
        }
        .empty-state i { font-size: 36px; color: var(--gray-300); margin-bottom: 10px; display: block; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
            .page-content { padding: 20px 16px; }
            .topbar { padding: 14px 16px; }
            .search-box { width: 100%; }
            .topbar-actions { width: 100%; }
        }
    </style>
</head>
<body>

<div class="app-layout">
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">👕</span>
            <span>Konveksi<span class="brand-accent">Ku</span></span>
        </div>
        <div class="sidebar-label">Anggota 1</div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active"><i class="fas fa-user-circle" style="width:16px"></i> Profil &amp; Akun</a>
            <a href="../Karyawan/index.php"><i class="fas fa-users" style="width:16px"></i> Karyawan</a>
            <a href="../absensi/index.php"><i class="fas fa-clipboard-check" style="width:16px"></i> Absensi</a>
            <a href="../audit_log/index.php"><i class="fas fa-history" style="width:16px"></i> Audit Log</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="user-avatar">AD</div>
                <div>
                    <strong><?= htmlspecialchars($_SESSION["nama"] ?? "Pengguna") ?></strong>
                    <small>KonveksiKu</small>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="main-content">
        <header class="topbar">
            <div class="page-heading">
                <h1>Profil &amp; Manajemen Akun</h1>
                <div class="breadcrumb">Home / Profil &amp; Manajemen Akun</div>
            </div>
            <div class="topbar-actions">
                <form method="GET">
                    <input class="search-box" type="search" name="cari"
                           placeholder="Cari nama, email, role..."
                           value="<?= htmlspecialchars($cari) ?>">
                </form>
                <span class="admin-pill"><?= htmlspecialchars($_SESSION["role"] ?? "Pengguna") ?></span>
            </div>
        </header>

        <section class="page-content">
            <?php if (isset($pesanTampil[$pesan])): ?>
                <div class="alert-info">
                    <i class="fas fa-info-circle" style="margin-right:6px"></i>
                    <?= htmlspecialchars($pesanTampil[$pesan]) ?>
                </div>
            <?php endif; ?>

            <!-- ===== TABEL MANAJEMEN AKUN ===== -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-cog" style="color:var(--primary);margin-right:6px"></i>
                    Manajemen Akun
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($users->num_rows > 0): ?>
                            <?php while ($row = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row["nama"]) ?></strong></td>
                                    <td><?= htmlspecialchars($row["email"]) ?></td>
                                    <td>
                                        <span class="badge badge-primary">
                                            <i class="fas fa-user-tag" style="font-size:10px"></i>
                                            <?= htmlspecialchars($row["nama_role"] ?? "Belum diatur") ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= strtolower($row["status"]) === "aktif" ? "badge-success" : "badge-warning" ?>">
                                            <?= htmlspecialchars($row["status"]) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="aksi-group">
                                            <a class="btn btn-outline" href="edit.php?id=<?= (int)$row["id"] ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ((int)$row["id"] !== (int)$_SESSION["user_id"]): ?>
                                                <form method="POST" action="hapus.php"
                                                      onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                                    <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
                                                    <button class="btn btn-danger" type="submit">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <strong>Belum ada akun yang cocok</strong>
                                        <div style="font-size:12.5px">Coba ubah kata kunci pencarian.</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===== TABEL ROLE & PERMISSION ===== -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-shield-alt" style="color:var(--primary);margin-right:6px"></i>
                    Role &amp; Permission (Hak Akses)
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr><th style="width:25%">Aktor</th><th>Hak Akses Utama</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-purple"><i class="fas fa-crown" style="font-size:10px"></i> Owner / Pimpinan</span></td>
                                <td>Dashboard, laporan, monitoring, persetujuan tertentu</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-primary"><i class="fas fa-user-shield" style="font-size:10px"></i> Admin</span></td>
                                <td>Pengelolaan akun, karyawan, dan data operasional</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-success"><i class="fas fa-money-bill" style="font-size:10px"></i> Keuangan</span></td>
                                <td>Pemasukan, pengeluaran, payroll, laporan keuangan</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning"><i class="fas fa-boxes" style="font-size:10px"></i> Gudang</span></td>
                                <td>Barang, stok masuk/keluar, dan pemakaian bahan</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-info"><i class="fas fa-industry" style="font-size:10px"></i> Produksi</span></td>
                                <td>Job produksi, tahapan, progres, dan quality control</td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-gray"><i class="fas fa-user" style="font-size:10px"></i> Karyawan</span></td>
                                <td>Profil sendiri dan absensi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>