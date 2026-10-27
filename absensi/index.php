<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ============================================================
           KONVEKSIKU THEME — CSS INTERNAL (Absensi)
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

        /* ===== FILTER BAR ===== */
        .filter-bar {
            display: flex; gap: 10px; align-items: center;
            flex-wrap: wrap; margin-bottom: 22px;
        }
        .filter-bar input[type="date"],
        .filter-bar input[type="search"] {
            padding: 9px 14px; border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 13.5px;
            font-family: inherit; transition: all .2s;
        }
        .filter-bar input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 600; border: none;
            cursor: pointer; transition: all .2s; text-decoration: none;
            font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 16px rgba(37,99,235,.35);
        }
        .btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px; margin-bottom: 8px;
        }
        .stat-card {
            background: #fff; border-radius: var(--radius); padding: 20px;
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            transition: all .25s;
        }
        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
        .stat-label { font-size: 12.5px; color: var(--gray-500); font-weight: 500; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; color: var(--gray-900); letter-spacing: -.5px; }

        /* ===== CARD ===== */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            overflow: hidden;
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
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-primary { background: var(--primary-light); color: var(--primary-dark); }

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
            <a href="../user/index.php"><i class="fas fa-user-circle" style="width:16px"></i> Profil &amp; Akun</a>
            <a href="../Karyawan/index.php"><i class="fas fa-users" style="width:16px"></i> Karyawan</a>
            <a href="index.php" class="active"><i class="fas fa-clipboard-check" style="width:16px"></i> Absensi</a>
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
                <h1>Absensi</h1>
                <div class="breadcrumb">Home / Absensi</div>
            </div>

            <div class="topbar-actions">
                <form method="GET">
                    <input type="hidden" name="tanggal"
                           value="<?= htmlspecialchars($tanggal) ?>">
                    <input class="search-box" type="search" name="cari"
                           placeholder="Cari karyawan..."
                           value="<?= htmlspecialchars($cari) ?>">
                </form>
                <span class="admin-pill">
                    <?= htmlspecialchars($_SESSION["role"] ?? "Pengguna") ?>
                </span>
            </div>
        </header>

        <section class="page-content">
            <?php if ($pesan === "berhasil"): ?>
                <div class="alert-info">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i>
                    Data absensi berhasil disimpan.
                </div>
            <?php endif; ?>

            <div class="alert-info">
                <i class="fas fa-info-circle" style="margin-right:6px"></i>
                Silakan periksa dan kelola catatan kehadiran karyawan.
            </div>

            <!-- ===== FILTER ===== -->
            <form method="GET" class="filter-bar">
                <input type="date" name="tanggal"
                       value="<?= htmlspecialchars($tanggal) ?>">
                <input type="search" name="cari"
                       placeholder="Cari nama atau kode..."
                       value="<?= htmlspecialchars($cari) ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset
                </a>
                <a href="tambah.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Absensi
                </a>
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
                        <?= (int)($rekap["sakit"] ?? 0) ?>
                        /
                        <?= (int)($rekap["alpa"] ?? 0) ?>
                    </div>
                </div>
            </div>

            <!-- ===== TABEL ===== -->
            <div class="card" style="margin-top:24px">
                <div class="card-header">
                    <i class="fas fa-clipboard-list" style="color:var(--primary);margin-right:6px"></i>
                    Riwayat Absensi —
                    <?= htmlspecialchars(date("d-m-Y", strtotime($tanggal))) ?>
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
                                    <td>
                                        <span class="badge <?= $badge ?>">
                                            <?= htmlspecialchars($row["status"]) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px">
                                    <div style="font-size:36px;color:var(--gray-300);margin-bottom:10px">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <strong style="color:var(--gray-700)">Belum ada data absensi</strong>
                                    <div style="font-size:12.5px;color:var(--gray-500)">
                                        Tidak ada catatan absensi untuk tanggal ini.
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