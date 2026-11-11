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
$active_page = 'absensi';
// ==========================================

$tanggal = $_GET["tanggal"] ?? date("Y-m-d");
$cari = trim($_GET["cari"] ?? "");

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = date("Y-m-d");
}

/* Ringkasan absensi pada tanggal yang dipilih. */
$stmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'Hadir') AS hadir,
        SUM(status = 'Izin') AS izin,
        SUM(status = 'Sakit') AS sakit,
        SUM(status = 'Alpa') AS alpa
     FROM attendance
     WHERE tanggal = ?"
);
$stmt->bind_param("s", $tanggal);
$stmt->execute();
$rekap = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* Data tabel absensi. */
$sql = "SELECT a.id, a.tanggal, a.jam_masuk, a.jam_pulang, a.status,
               e.kode, e.nama
        FROM attendance a
        JOIN employees e ON e.id = a.employee_id
        WHERE a.tanggal = ?";

if ($cari !== "") {
    $sql .= " AND (e.nama LIKE ? OR e.kode LIKE ? OR a.status LIKE ?)";
}

$sql .= " ORDER BY a.tanggal DESC, a.id DESC";

$stmt = $conn->prepare($sql);

if ($cari !== "") {
    $keyword = "%" . $cari . "%";
    $stmt->bind_param("ssss", $tanggal, $keyword, $keyword, $keyword);
} else {
    $stmt->bind_param("s", $tanggal);
}

$stmt->execute();
$dataAbsensi = $stmt->get_result();

$pesan = $_GET["pesan"] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi - KonveksiKu</title>
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
    font-size: 13.5px; font-weight: 500; margin-bottom: 16px;
}

.filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 22px; }
.filter-bar input[type="date"], .filter-bar input[type="search"] {
    padding: 9px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm);
    font-size: 13.5px; font-family: inherit; transition: all .2s;
}
.filter-bar input:focus { outline: none; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37,99,235,.15); }

.btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: var(--radius-sm);
    font-size: 13.5px; font-weight: 600; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; font-family: inherit;
}
.btn-primary { background: var(--primary-blue); color: #fff; }
.btn-primary:hover { background: #1D4ED8; box-shadow: 0 6px 16px rgba(37,99,235,.35); }
.btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
.btn-outline:hover { background: var(--gray-50); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 8px; }
.stat-card {
    background: #fff; border-radius: var(--radius); padding: 20px;
    box-shadow: var(--shadow); border: 1px solid var(--gray-200); transition: all .25s;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); transform: translateY(-3px); }
.stat-label { font-size: 12.5px; color: var(--text-light); font-weight: 500; margin-bottom: 4px; }
.stat-value { font-size: 24px; font-weight: 800; color: var(--text-dark); letter-spacing: -.5px; }

.card {
    background: #fff; border-radius: var(--radius); box-shadow: var(--shadow);
    border: 1px solid var(--border-color); overflow: hidden; margin-top: 24px;
}
.card-header {
    padding: 18px 22px; border-bottom: 1px solid var(--border-color);
    font-size: 15px; font-weight: 700; color: var(--text-dark); letter-spacing: -.2px;
}

.table-wrapper { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead th {
    text-align: left; padding: 14px 16px; font-size: 11px; text-transform: uppercase;
    letter-spacing: .4px; color: var(--table-text); font-weight: 700;
    border-bottom: 1px solid var(--border-color); background: var(--gray-50); white-space: nowrap;
}
tbody td {
    padding: 14px 16px; font-size: 13.5px; color: var(--table-text);
    border-bottom: 1px solid var(--border-color);
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
                <h1>Absensi</h1>
                <div class="breadcrumb">Beranda / <span>Absensi</span></div>
            </div>

            <div class="topbar-actions">
                <form method="GET">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input class="search-box" type="search" name="cari" placeholder="Cari karyawan..." value="<?= htmlspecialchars($cari) ?>">
                </form>
                <span class="admin-pill"><?= htmlspecialchars($_SESSION["role"] ?? "Pengguna") ?></span>
            </div>
        </header>

        <section class="content">
            <?php if ($pesan === "berhasil"): ?>
                <div class="alert-info">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i> Data absensi berhasil disimpan.
                </div>
            <?php endif; ?>

            <div class="alert-info">
                <i class="fas fa-info-circle" style="margin-right:6px"></i> Silakan periksa dan kelola catatan kehadiran karyawan.
            </div>

            <!-- ===== FILTER ===== -->
            <form method="GET" class="filter-bar">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <input type="search" name="cari" placeholder="Cari nama atau kode..." value="<?= htmlspecialchars($cari) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                <a href="index.php" class="btn btn-outline"><i class="fas fa-undo"></i> Reset</a>
                <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Absensi</a>
            </form>

            <!-- ===== STATS ===== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Catatan</div>
                    <div class="stat-value"><?= (int)($rekap["total"] ?? 0) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value" style="color:var(--success)"><?= (int)($rekap["hadir"] ?? 0) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Izin</div>
                    <div class="stat-value" style="color:var(--warning)"><?= (int)($rekap["izin"] ?? 0) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sakit / Alpa</div>
                    <div class="stat-value" style="color:var(--danger)">
                        <?= (int)($rekap["sakit"] ?? 0) ?> / <?= (int)($rekap["alpa"] ?? 0) ?>
                    </div>
                </div>
            </div>

            <!-- ===== TABEL ===== -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list" style="color:var(--primary-blue);margin-right:6px"></i>
                    Riwayat Absensi — <?= htmlspecialchars(date("d-m-Y", strtotime($tanggal))) ?>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($dataAbsensi->num_rows > 0): ?>
                            <?php while ($row = $dataAbsensi->fetch_assoc()): ?>
                                <?php
                                $badge = "badge-primary";
                                if ($row["status"] === "Hadir") $badge = "badge-success";
                                elseif ($row["status"] === "Izin" || $row["status"] === "Sakit") $badge = "badge-warning";
                                elseif ($row["status"] === "Alpa") $badge = "badge-danger";
                                ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($row["kode"]) ?></span></td>
                                    <td><strong><?= htmlspecialchars($row["nama"]) ?></strong></td>
                                    <td><?= htmlspecialchars(date("d-m-Y", strtotime($row["tanggal"]))) ?></td>
                                    <td><?= htmlspecialchars($row["jam_masuk"] ?: "—") ?></td>
                                    <td><?= htmlspecialchars($row["jam_pulang"] ?: "—") ?></td>
                                    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($row["status"]) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px">
                                    <div style="font-size:36px;color:var(--gray-300);margin-bottom:10px"><i class="fas fa-inbox"></i></div>
                                    <strong style="color:var(--gray-700)">Belum ada data absensi</strong>
                                    <div style="font-size:12.5px;color:var(--text-light)">Tidak ada catatan absensi untuk tanggal ini.</div>
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