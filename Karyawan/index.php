<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
/* ============================================================
   KONVEKSIKU THEME — CSS INTERNAL (Karyawan)
   ============================================================ */
:root{
    --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#dbeafe;
    --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
    --info:#06b6d4; --purple:#8b5cf6; --dark:#0f172a;
    --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
    --gray-300:#cbd5e1; --gray-400:#94a3b8; --gray-500:#64748b;
    --gray-600:#475569; --gray-700:#334155; --gray-800:#1e293b; --gray-900:#0f172a;
    --radius:14px; --radius-sm:10px;
    --shadow:0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:0 4px 12px rgba(0,0,0,.08);
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',system-ui,sans-serif;background:var(--gray-100);color:var(--gray-800);line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}

/* ===== LAYOUT ===== */
.app-layout{display:flex;min-height:100vh}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;background:var(--dark);color:#fff;display:flex;flex-direction:column;
    position:fixed;inset:0 auto 0 0;height:100vh;z-index:100;
}
.sidebar-brand{
    padding:22px 20px;border-bottom:1px solid rgba(255,255,255,.08);
    display:flex;align-items:center;gap:12px;font-size:20px;font-weight:800;letter-spacing:-.3px;
}
.brand-icon{background:rgba(37,99,235,.15);padding:8px;border-radius:10px;font-size:16px;line-height:1}
.brand-accent{color:var(--primary)}
.sidebar-label{
    font-size:10.5px;text-transform:uppercase;letter-spacing:1.4px;
    color:var(--gray-500);padding:16px 20px 8px;font-weight:700;
}
.sidebar-nav{flex:1;padding:8px 12px;overflow-y:auto}
.sidebar-nav a{
    display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:var(--radius-sm);
    color:#94a3b8;text-decoration:none;font-size:14px;font-weight:500;transition:all .2s;margin-bottom:2px;
}
.sidebar-nav a:hover{background:rgba(255,255,255,.06);color:#fff}
.sidebar-nav a.active{background:var(--primary);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.35)}
.sidebar-bottom{padding:16px 20px;border-top:1px solid rgba(255,255,255,.08)}
.user-mini{display:flex;align-items:center;gap:12px}
.user-avatar{
    width:40px;height:40px;border-radius:50%;
    background:linear-gradient(135deg,var(--primary),var(--info));
    display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff;
}
.user-mini strong{font-size:13.5px;font-weight:600;color:#fff;display:block}
.user-mini small{font-size:11.5px;color:var(--gray-500)}

/* ===== MAIN CONTENT ===== */
.main-content{flex:1;margin-left:260px;display:flex;flex-direction:column;min-height:100vh}

/* ===== TOPBAR ===== */
.topbar{
    background:#fff;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid var(--gray-200);position:sticky;top:0;z-index:50;flex-wrap:wrap;gap:12px;
}
.page-heading h1{font-size:20px;font-weight:800;color:var(--gray-900);letter-spacing:-.3px}
.breadcrumb{font-size:12.5px;color:var(--gray-500);margin-top:2px}
.topbar-actions{display:flex;align-items:center;gap:12px}
.search-box{
    padding:9px 16px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);
    font-size:13.5px;font-family:inherit;width:240px;transition:all .2s;
}
.search-box:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
.admin-pill{
    background:var(--primary-light);color:var(--primary-dark);
    font-size:12px;font-weight:700;padding:6px 14px;border-radius:20px;white-space:nowrap;
}

/* ===== PAGE CONTENT ===== */
.page-content{padding:28px 32px;flex:1}

/* ===== ALERT ===== */
.alert-info{
    padding:14px 18px;border-radius:var(--radius-sm);
    background:var(--primary-light);color:var(--primary-dark);
    border-left:3px solid var(--primary);font-size:13.5px;font-weight:500;margin-bottom:22px;
}
.alert-info.success{background:#d1fae5;color:#065f46;border-left-color:var(--success)}
.alert-info.danger{background:#fee2e2;color:#991b1b;border-left-color:var(--danger)}

/* ===== FILTER ===== */
.filter-bar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:22px}
.filter-input,.filter-select{
    padding:9px 14px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);
    font-size:13.5px;font-family:inherit;background:#fff;transition:all .2s;
}
.filter-input{min-width:220px}
.filter-input:focus,.filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}

/* ===== STATS ===== */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:22px}
.stat-card{
    background:#fff;border-radius:var(--radius);padding:20px;
    box-shadow:var(--shadow);border:1px solid var(--gray-200);transition:all .25s;
}
.stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-3px)}
.stat-icon{
    width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;
    font-size:18px;margin-bottom:12px;
}
.stat-icon.blue{background:var(--primary-light);color:var(--primary)}
.stat-icon.green{background:#d1fae5;color:var(--success)}
.stat-icon.orange{background:#fef3c7;color:var(--warning)}
.stat-icon.red{background:#fee2e2;color:var(--danger)}
.stat-label{font-size:12.5px;color:var(--gray-500);font-weight:500;margin-bottom:4px}
.stat-value{font-size:24px;font-weight:800;color:var(--gray-900);letter-spacing:-.5px}

/* ===== CARD ===== */
.card{
    background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);
    border:1px solid var(--gray-200);overflow:hidden;margin-bottom:22px;
}
.card-header{
    padding:18px 22px;border-bottom:1px solid var(--gray-200);
    font-size:15px;font-weight:700;color:var(--gray-900);letter-spacing:-.2px;
}

/* ===== TABEL ===== */
.table-container{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:760px}
thead th{
    text-align:left;padding:12px 16px;font-size:11px;text-transform:uppercase;
    letter-spacing:.6px;color:var(--gray-500);font-weight:700;
    border-bottom:1px solid var(--gray-200);background:var(--gray-50);white-space:nowrap;
}
tbody td{
    padding:14px 16px;font-size:13.5px;color:var(--gray-700);
    border-bottom:1px solid var(--gray-100);vertical-align:middle;
}
tbody tr{transition:background .15s}
tbody tr:hover{background:var(--gray-50)}
tbody tr:last-child td{border-bottom:none}

/* ===== BADGE ===== */
.badge{
    display:inline-flex;align-items:center;gap:4px;padding:4px 11px;border-radius:20px;
    font-size:11.5px;font-weight:700;white-space:nowrap;
}
.badge-success{background:#d1fae5;color:#065f46}
.badge-warning{background:#fef3c7;color:#92400e}
.badge-danger{background:#fee2e2;color:#991b1b}
.badge-info{background:#cffafe;color:#155e75}
.badge-primary{background:var(--primary-light);color:var(--primary-dark)}
.badge-gray{background:var(--gray-200);color:var(--gray-600)}

/* ===== BUTTON ===== */
.btn{
    display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:var(--radius-sm);
    font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .2s;
    text-decoration:none;font-family:inherit;
}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-dark);box-shadow:0 6px 16px rgba(37,99,235,.35)}
.btn-danger{background:var(--danger);color:#fff}
.btn-danger:hover{background:#dc2626}
.btn-outline{background:#fff;color:var(--gray-700);border:1px solid var(--gray-300)}
.btn-outline:hover{background:var(--gray-50)}
.btn-sm{padding:6px 12px;font-size:12.5px}

.aksi-group{display:flex;gap:8px;flex-wrap:wrap}

/* ===== EMPTY STATE ===== */
.empty-state{padding:40px 20px;text-align:center;color:var(--gray-500)}
.empty-state i{font-size:36px;color:var(--gray-300);margin-bottom:10px;display:block}

/* ===== RESPONSIVE ===== */
@media(max-width:1100px){
    .sidebar{width:220px}
    .main-content{margin-left:220px}
    .stats-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:768px){
    .sidebar{width:0;overflow:hidden}
    .main-content{margin-left:0}
    .page-content{padding:20px 16px}
    .topbar{padding:14px 16px}
    .search-box{width:100%}
    .topbar-actions{width:100%}
    .stats-grid{grid-template-columns:1fr}
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
            <a href="index.php" class="active"><i class="fas fa-users" style="width:16px"></i> Karyawan</a>
            <a href="../absensi/index.php"><i class="fas fa-clipboard-check" style="width:16px"></i> Absensi</a>
            <a href="../audit_log/index.php"><i class="fas fa-history" style="width:16px"></i> Audit Log</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="user-avatar">AD</div>
                <div>
                    <strong><?= e($_SESSION["nama"] ?? "Pengguna") ?></strong>
                    <small><?= e($_SESSION["role"] ?? "Pengguna") ?></small>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <main class="main-content">
        <header class="topbar">
            <div class="page-heading">
                <h1>Karyawan</h1>
                <div class="breadcrumb">Home / Karyawan</div>
            </div>
            <div class="topbar-actions">
                <form method="GET">
                    <input class="search-box" type="search" name="cari"
                           placeholder="Cari karyawan..."
                           value="<?= e($cari) ?>">
                    <input type="hidden" name="divisi" value="<?= e($divisi) ?>">
                </form>
                <span class="admin-pill"><?= e($_SESSION["role"] ?? "Admin") ?></span>
            </div>
        </header>

        <section class="page-content">
            <?php if ($pesan === "berhasil_tambah"): ?>
                <div class="alert-info success">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i>
                    Karyawan berhasil ditambahkan.
                </div>
            <?php elseif ($pesan === "berhasil_edit"): ?>
                <div class="alert-info success">
                    <i class="fas fa-check-circle" style="margin-right:6px"></i>
                    Data karyawan berhasil diperbarui.
                </div>
            <?php elseif ($pesan === "gagal_tambah"): ?>
                <div class="alert-info danger">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>
                    Karyawan gagal ditambahkan. Periksa kembali data dan struktur database.
                </div>
            <?php endif; ?>

            <!-- ===== FILTER ===== -->
            <form method="GET" class="filter-bar">
                <input class="filter-input" type="search" name="cari"
                       placeholder="Cari karyawan..." value="<?= e($cari) ?>">
                <select class="filter-select" name="divisi">
                    <option value="">Semua Divisi</option>
                    <?php while ($d = $divisiResult->fetch_assoc()): ?>
                        <option value="<?= e($d["divisi"]) ?>" <?= $divisi === $d["divisi"] ? "selected" : "" ?>>
                            <?= e($d["divisi"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button class="btn btn-outline" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a class="btn btn-primary" href="tambah.php">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </a>
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
                    <i class="fas fa-users" style="color:var(--primary);margin-right:6px"></i>
                    Data Karyawan
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
                                        <a class="btn btn-outline btn-sm" href="edit.php?id=<?= (int)$row["id"] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="hapus.php"
                                              onsubmit="return confirm('Yakin ingin menghapus data karyawan ini?')">
                                            <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
                                            <button class="btn btn-danger btn-sm" type="submit">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
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