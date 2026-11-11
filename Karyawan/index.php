<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================================
// VARIABEL UNTUK SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'karyawan';
// ==========================================

$cari = trim($_GET["cari"] ?? "");
$divisi = trim($_GET["divisi"] ?? "");

// Ringkasan karyawan
$total = (int) ($conn->query("SELECT COUNT(*) AS jumlah FROM employees")->fetch_assoc()["jumlah"] ?? 0);
$aktif = (int) ($conn->query("SELECT COUNT(*) AS jumlah FROM employees WHERE status = 'Aktif'")->fetch_assoc()["jumlah"] ?? 0);

$stmt = $conn->prepare(
    "SELECT COUNT(DISTINCT employee_id) AS jumlah
     FROM attendance
     WHERE tanggal = ? AND status = 'Hadir'"
);
$hariIni = date("Y-m-d");
$stmt->bind_param("s", $hariIni);
$stmt->execute();
$hadir = (int) ($stmt->get_result()->fetch_assoc()["jumlah"] ?? 0);
$stmt->close();

// Pilihan divisi dari database
$divisiResult = $conn->query(
    "SELECT DISTINCT divisi FROM employees
     WHERE divisi IS NOT NULL AND divisi <> ''
     ORDER BY divisi"
);

// Daftar karyawan
$sql = "SELECT id, kode, nama, jabatan, divisi, tanggal_masuk, status
        FROM employees WHERE 1=1";
$types = "";
$params = [];

if ($cari !== "") {
    $sql .= " AND (kode LIKE ? OR nama LIKE ? OR jabatan LIKE ?)";
    $keyword = "%$cari%";
    $types .= "sss";
    array_push($params, $keyword, $keyword, $keyword);
}

if ($divisi !== "") {
    $sql .= " AND divisi = ?";
    $types .= "s";
    $params[] = $divisi;
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$karyawan = $stmt->get_result();

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$pesan = $_GET["pesan"] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Karyawan - KonveksiKu</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
/* ============================================================
   1. CSS GLOBAL & SIDEBAR (STANDAR ANGGOTA 2)
   ============================================================ */
* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --sidebar-bg: #1E293B;
    --sidebar-active: #3B82F6;
    --primary-blue: #2563EB;
    --main-bg: #F8FAFC;
    --text-dark: #1E293B;
    --text-light: #94A3B8;
    --white: #FFFFFF;
    --table-text: #475569;
    --border-color: #E2E8F0;
    /* Tambahan dari Anggota 1 untuk Badge & Stats */
    --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
    --info: #06b6d4; --gray-50: #f8fafc; --gray-100: #f1f5f9;
    --gray-200: #e2e8f0; --gray-300: #cbd5e1; --gray-500: #64748b;
    --gray-700: #334155; --gray-900: #0f172a;
    --radius: 14px; --radius-sm: 10px;
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
}

body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; background: var(--main-bg); color: var(--text-dark); line-height: 1.6; }
a { text-decoration: none; color: inherit; }

.app { display: flex; min-height: 100vh; }

/* SIDEBAR */
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

/* MAIN CONTENT WRAPPER */
.main { flex: 1; min-width: 0; }

/* ============================================================
   2. CSS KONTEN SPESIFIK (PUNYA ANGGOTA 1 - DIPERTAHANKAN)
   ============================================================ */
.topbar {
    min-height: 90px; background: var(--white);
    border-bottom: 1px solid var(--border-color); padding: 20px 32px;
    display: flex; justify-content: space-between; align-items: center;
    gap: 15px; flex-wrap: wrap;
}
.page-heading h1 { font-size: 23px; font-weight: 800; color: var(--text-dark); margin-bottom: 7px; letter-spacing: -.3px; }
.breadcrumb { font-size: 12px; color: var(--text-light); }
.breadcrumb span { color: var(--primary-blue); }

.topbar-actions { display: flex; align-items: center; gap: 12px; }
.search-box {
    padding: 9px 16px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm);
    font-size: 13.5px; font-family: inherit; width: 240px; transition: all .2s;
}
.search-box:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
.admin-pill {
    background: #EFF6FF; color: var(--primary-blue);
    font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; white-space: nowrap;
}

.content { padding: 30px 32px; }

.alert-info {
    padding: 14px 18px; border-radius: var(--radius-sm);
    background: #DBEAFE; color: #1D4ED8; border-left: 3px solid var(--primary-blue);
    font-size: 13.5px; font-weight: 500; margin-bottom: 22px;
}
.alert-info.success { background: #d1fae5; color: #065f46; border-left-color: var(--success); }
.alert-info.danger { background: #fee2e2; color: #991b1b; border-left-color: var(--danger); }

.filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 22px; }
.filter-input, .filter-select {
    padding: 9px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm);
    font-size: 13.5px; font-family: inherit; background: #fff; transition: all .2s;
}
.filter-input { min-width: 220px; }
.filter-input:focus, .filter-select:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 22px; }
.stat-card {
    background: #fff; border-radius: var(--radius); padding: 20px;
    box-shadow: var(--shadow); border: 1px solid var(--gray-200); transition: all .25s;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); transform: translateY(-3px); }
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 12px;
}
.stat-icon.blue { background: #DBEAFE; color: var(--primary-blue); }
.stat-icon.green { background: #d1fae5; color: var(--success); }
.stat-icon.orange { background: #fef3c7; color: var(--warning); }
.stat-icon.red { background: #fee2e2; color: var(--danger); }
.stat-label { font-size: 12.5px; color: var(--text-light); font-weight: 500; margin-bottom: 4px; }
.stat-value { font-size: 24px; font-weight: 800; color: var(--text-dark); letter-spacing: -.5px; }

.card {
    background: #fff; border-radius: var(--radius); box-shadow: var(--shadow);
    border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 22px;
}
.card-header {
    padding: 18px 22px; border-bottom: 1px solid var(--border-color);
    font-size: 15px; font-weight: 700; color: var(--text-dark); letter-spacing: -.2px;
}

.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; min-width: 760px; }
thead th {
    text-align: left; padding: 14px 16px; font-size: 11px; text-transform: uppercase;
    letter-spacing: .4px; color: var(--table-text); font-weight: 700;
    border-bottom: 1px solid var(--border-color); background: var(--gray-50); white-space: nowrap;
}
tbody td {
    padding: 14px 16px; font-size: 13.5px; color: var(--table-text);
    border-bottom: 1px solid var(--border-color); vertical-align: middle;
}
tbody tr { transition: background .15s; }
tbody tr:hover { background: var(--main-bg); }
tbody tr:last-child td { border-bottom: none; }

.badge {
    display: inline-flex; align-items: center; gap: 4px; padding: 4px 11px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700; white-space: nowrap;
}
.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-primary { background: #DBEAFE; color: #1D4ED8; }
.badge-gray { background: var(--gray-200); color: var(--gray-700); }

.btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; font-family: inherit;
}
.btn-primary { background: var(--primary-blue); color: #fff; }
.btn-primary:hover { background: #1D4ED8; box-shadow: 0 6px 16px rgba(37,99,235,.35); }
.btn-danger { background: var(--danger); color: #fff; }
.btn-danger:hover { background: #dc2626; }
.btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
.btn-outline:hover { background: var(--gray-50); }
.btn-sm { padding: 6px 12px; font-size: 12.5px; }

.aksi-group { display: flex; gap: 8px; flex-wrap: wrap; }

.empty-state { padding: 40px 20px; text-align: center; color: var(--text-light); }
.empty-state i { font-size: 36px; color: var(--gray-300); margin-bottom: 10px; display: block; }

@media (max-width: 1000px) {
    .topbar, .content { padding-left: 22px; padding-right: 22px; }
}
@media (max-width: 650px) {
    .app { flex-direction: column; }
    .sidebar { width: 100%; min-width: 0; padding: 18px 14px; }
    .logo { margin-bottom: 20px; }
    .nav-link { margin-bottom: 4px; padding: 11px 12px; }
    .sidebar-footer { margin-top: 15px; }
    .stats-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="app">
    <!-- ===== SIDEBAR UNIVERSAL ===== -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main">
        <header class="topbar">
            <div class="page-heading">
                <h1>Karyawan</h1>
                <div class="breadcrumb">Beranda / <span>Karyawan</span></div>
            </div>
            <div class="topbar-actions">
                <form method="GET">
                    <input class="search-box" type="search" name="cari" placeholder="Cari karyawan..." value="<?= e($cari) ?>">
                    <input type="hidden" name="divisi" value="<?= e($divisi) ?>">
                </form>
                <span class="admin-pill"><?= e($_SESSION["role"] ?? "Admin") ?></span>
            </div>
        </header>

        <section class="content">
            <?php if ($pesan === "berhasil_tambah"): ?>
                <div class="alert-info success">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i> Karyawan berhasil ditambahkan.
                </div>
            <?php elseif ($pesan === "berhasil_edit"): ?>
                <div class="alert-info success">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i> Data karyawan berhasil diperbarui.
                </div>
            <?php elseif ($pesan === "gagal_tambah"): ?>
                <div class="alert-info danger">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px"></i> Karyawan gagal ditambahkan. Periksa kembali data dan struktur database.
                </div>
            <?php endif; ?>

            <!-- ===== FILTER ===== -->
            <form method="GET" class="filter-bar">
                <input class="filter-input" type="search" name="cari" placeholder="Cari karyawan..." value="<?= e($cari) ?>">
                <select class="filter-select" name="divisi">
                    <option value="">Semua Divisi</option>
                    <?php while ($d = $divisiResult->fetch_assoc()): ?>
                        <option value="<?= e($d["divisi"]) ?>" <?= $divisi === $d["divisi"] ? "selected" : "" ?>>
                            <?= e($d["divisi"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button class="btn btn-outline" type="submit"><i class="fas fa-search"></i> Cari</button>
                <a class="btn btn-primary" href="tambah.php"><i class="fas fa-plus"></i> Tambah Karyawan</a>
            </form>

            <!-- ===== STATS ===== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Total Karyawan</div>
                    <div class="stat-value"><?= $total ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                    <div class="stat-label">Karyawan Aktif</div>
                    <div class="stat-value"><?= $aktif ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                    <div class="stat-label">Hadir Hari Ini</div>
                    <div class="stat-value"><?= $hadir ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-user-slash"></i></div>
                    <div class="stat-label">Belum Hadir Hari Ini</div>
                    <div class="stat-value"><?= max(0, $aktif - $hadir) ?></div>
                </div>
            </div>

            <!-- ===== TABEL ===== -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users" style="color:var(--primary-blue);margin-right:6px"></i> Data Karyawan
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Divisi</th>
                                <th>Tgl Masuk</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($karyawan->num_rows > 0): ?>
                            <?php while ($row = $karyawan->fetch_assoc()): ?>
                            <?php
                                $statusClass = "badge-gray";
                                if (strtolower($row["status"]) === "aktif") $statusClass = "badge-success";
                                elseif (strtolower($row["status"]) === "cuti") $statusClass = "badge-warning";
                                elseif (strtolower($row["status"]) === "nonaktif") $statusClass = "badge-danger";
                            ?>
                            <tr>
                                <td><span class="badge badge-primary"><?= e($row["kode"]) ?></span></td>
                                <td><strong><?= e($row["nama"]) ?></strong></td>
                                <td><?= e($row["jabatan"]) ?></td>
                                <td><?= e($row["divisi"]) ?></td>
                                <td><?= !empty($row["tanggal_masuk"]) ? e(date("d M Y", strtotime($row["tanggal_masuk"]))) : "—" ?></td>
                                <td><span class="badge <?= $statusClass ?>"><?= e($row["status"]) ?></span></td>
                                <td>
                                    <div class="aksi-group">
                                        <a class="btn btn-outline btn-sm" href="edit.php?id=<?= (int)$row["id"] ?>"><i class="fas fa-edit"></i> Edit</a>
                                        <form method="POST" action="hapus.php" onsubmit="return confirm('Yakin ingin menghapus data karyawan ini?')">
                                            <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
                                            <button class="btn btn-danger btn-sm" type="submit"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <strong>Data karyawan tidak ditemukan</strong>
                                        <div style="font-size:12.5px">Coba ubah kata kunci atau filter divisi.</div>
                                    </div>
                                </td>
                            </tr>
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