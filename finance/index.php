
<?php
require_once 'koneksi.php';

// Total pemasukan
$queryPemasukan = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(nominal), 0) AS total
     FROM income_transactions"
);
$dataPemasukan = mysqli_fetch_assoc($queryPemasukan);
$totalPemasukan = $dataPemasukan['total'];

// Total pengeluaran
$queryPengeluaran = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(nominal), 0) AS total
     FROM expense_transactions"
);
$dataPengeluaran = mysqli_fetch_assoc($queryPengeluaran);
$totalPengeluaran = $dataPengeluaran['total'];

// Saldo
$saldo = $totalPemasukan - $totalPengeluaran;

// Format rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>KonveksiKu — Anggota 5: Finance, Payroll & Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
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
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--gray-100); color: var(--gray-800); line-height: 1.6; -webkit-font-smoothing: antialiased; }
  .app { display: flex; min-height: 100vh; }

  .sidebar { width: 260px; background: var(--dark); color: #fff; display: flex; flex-direction: column; position: fixed; inset: 0 auto 0 0; height: 100vh; z-index: 100; }
  .sidebar-header { padding: 22px 20px; border-bottom: 1px solid rgba(255,255,255,.08); }
  .logo { display: flex; align-items: center; gap: 12px; font-size: 20px; font-weight: 800; letter-spacing: -.3px; }
  .logo i { color: var(--primary); background: rgba(37,99,235,.15); padding: 8px; border-radius: 10px; font-size: 16px; }
  .logo span { color: var(--primary); }
  .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
  .nav-section { margin-bottom: 14px; }
  .nav-section-title { font-size: 10.5px; text-transform: uppercase; letter-spacing: 1.4px; color: var(--gray-500); padding: 8px 12px; font-weight: 700; }
  .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all .2s; cursor: pointer; margin-bottom: 2px; }
  .nav-item:hover { background: rgba(255,255,255,.06); color: #fff; }
  .nav-item.active { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.35); }
  .nav-item i { width: 20px; text-align: center; font-size: 15px; }
  .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08); }
  .user-profile { display: flex; align-items: center; gap: 12px; }
  .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--info)); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #fff; }
  .user-info .name { font-size: 13.5px; font-weight: 600; color: #fff; }
  .user-info .role { font-size: 11.5px; color: var(--gray-500); }

  .main { flex: 1; margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh; }
  .topbar { background: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--gray-200); position: sticky; top: 0; z-index: 50; }
  .topbar-left h1 { font-size: 20px; font-weight: 800; color: var(--gray-900); letter-spacing: -.3px; }
  .topbar-left .breadcrumb { font-size: 12.5px; color: var(--gray-500); margin-top: 2px; }
  .topbar-right { display: flex; align-items: center; gap: 14px; }
  .search-box { display: flex; align-items: center; gap: 8px; background: var(--gray-100); padding: 9px 16px; border-radius: var(--radius-sm); width: 280px; transition: all .2s; }
  .search-box:focus-within { background: #fff; box-shadow: 0 0 0 3px var(--primary-light); }
  .search-box i { color: var(--gray-400); font-size: 13px; }
  .search-box input { border: none; background: transparent; outline: none; font-size: 13.5px; width: 100%; color: var(--gray-700); font-family: inherit; }
  .topbar-icon { width: 40px; height: 40px; border-radius: var(--radius-sm); background: var(--gray-100); display: flex; align-items: center; justify-content: center; color: var(--gray-600); cursor: pointer; transition: all .2s; position: relative; }
  .topbar-icon:hover { background: var(--gray-200); color: var(--gray-900); }
  .topbar-icon .dot { position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; border: 2px solid #fff; }
  .role-switcher { display: flex; align-items: center; gap: 6px; background: var(--primary-light); color: var(--primary-dark); padding: 6px 12px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 700; }
  .role-switcher select { border: none; background: transparent; font-weight: 700; color: var(--primary-dark); font-size: 12px; outline: none; cursor: pointer; font-family: inherit; }

  .content { padding: 28px 32px; flex: 1; }
  .page { display: none; animation: fadeIn .3s ease; }
  .page.active { display: block; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px; margin-bottom: 26px; }
  .stat-card { background: #fff; border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); transition: all .25s; border: 1px solid var(--gray-200); }
  .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }
  .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 19px; }
  .stat-icon.blue { background: var(--primary-light); color: var(--primary); }
  .stat-icon.green { background: #d1fae5; color: var(--success); }
  .stat-icon.orange { background: #fef3c7; color: var(--warning); }
  .stat-icon.red { background: #fee2e2; color: var(--danger); }
  .stat-icon.purple { background: #ede9fe; color: var(--purple); }
  .stat-icon.cyan { background: #cffafe; color: var(--info); }
  .stat-label { font-size: 12.5px; color: var(--gray-500); font-weight: 500; margin-bottom: 4px; }
  .stat-value { font-size: 26px; font-weight: 800; color: var(--gray-900); letter-spacing: -.5px; }
  .stat-change { font-size: 11.5px; font-weight: 600; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
  .stat-change.up { color: var(--success); }
  .stat-change.down { color: var(--danger); }

  .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 22px; margin-bottom: 26px; }
  .grid-2-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; margin-bottom: 26px; }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-bottom: 26px; }

  .card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--gray-200); overflow: hidden; }
  .card-header { padding: 18px 22px; border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
  .card-header h3 { font-size: 15px; font-weight: 700; color: var(--gray-900); letter-spacing: -.2px; }
  .card-action { font-size: 12.5px; color: var(--primary); font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 4px; }
  .card-body { padding: 22px; }

  .table-container { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  thead th { text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: var(--gray-500); font-weight: 700; border-bottom: 1px solid var(--gray-200); background: var(--gray-50); white-space: nowrap; }
  tbody td { padding: 14px 16px; font-size: 13.5px; color: var(--gray-700); border-bottom: 1px solid var(--gray-100); }
  tbody tr { transition: background .15s; }
  tbody tr:hover { background: var(--gray-50); }
  tbody tr:last-child td { border-bottom: none; }

  .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
  .badge-success { background: #d1fae5; color: #065f46; }
  .badge-warning { background: #fef3c7; color: #92400e; }
  .badge-danger { background: #fee2e2; color: #991b1b; }
  .badge-info { background: #cffafe; color: #155e75; }
  .badge-primary { background: var(--primary-light); color: var(--primary-dark); }
  .badge-gray { background: var(--gray-200); color: var(--gray-600); }

  .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: var(--radius-sm); font-size: 13.5px; font-weight: 600; border: none; cursor: pointer; transition: all .2s; text-decoration: none; font-family: inherit; }
  .btn-primary { background: var(--primary); color: #fff; }
  .btn-primary:hover { background: var(--primary-dark); box-shadow: 0 6px 16px rgba(37,99,235,.35); }
  .btn-success { background: var(--success); color: #fff; }
  .btn-success:hover { background: #059669; }
  .btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
  .btn-outline:hover { background: var(--gray-50); }
  .btn-sm { padding: 6px 12px; font-size: 12.5px; }

  .chart-container { display: flex; align-items: flex-end; gap: 14px; height: 200px; padding: 20px 0 0; }
  .chart-bar { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; height: 100%; justify-content: flex-end; }
  .chart-bar .bar { width: 60%; border-radius: 6px 6px 0 0; transition: height .3s; min-height: 4px; }
  .chart-bar .bar.income { background: linear-gradient(180deg, var(--success), #34d399); }
  .chart-bar .bar.expense { background: linear-gradient(180deg, var(--danger), #f87171); }
  .chart-bar .label { font-size: 11px; color: var(--gray-500); font-weight: 600; }
  .chart-legend { display: flex; gap: 20px; margin-top: 16px; }
  .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--gray-600); font-weight: 500; }
  .legend-dot { width: 12px; height: 12px; border-radius: 3px; }
  .legend-dot.green { background: var(--success); }
  .legend-dot.red { background: var(--danger); }

  .notification-list { display: flex; flex-direction: column; gap: 10px; }
  .notification-item { display: flex; gap: 12px; padding: 12px; border-radius: var(--radius-sm); background: var(--gray-50); border-left: 3px solid var(--primary); transition: all .2s; cursor: pointer; }
  .notification-item:hover { background: var(--gray-100); transform: translateX(3px); }
  .notification-item.warning { border-left-color: var(--warning); }
  .notification-item.danger { border-left-color: var(--danger); }
  .notification-item.success { border-left-color: var(--success); }
  .notification-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
  .notification-icon.blue { background: var(--primary-light); color: var(--primary); }
  .notification-icon.orange { background: #fef3c7; color: var(--warning); }
  .notification-icon.red { background: #fee2e2; color: var(--danger); }
  .notification-icon.green { background: #d1fae5; color: var(--success); }
  .notification-content .title { font-size: 13px; font-weight: 700; color: var(--gray-800); }
  .notification-content .desc { font-size: 11.5px; color: var(--gray-500); }

  .payroll-summary { display: flex; flex-direction: column; gap: 12px; }
  .payroll-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed var(--gray-200); }
  .payroll-row:last-child { border-bottom: none; }
  .payroll-row .label { font-size: 13px; color: var(--gray-600); }
  .payroll-row .value { font-size: 14px; font-weight: 700; color: var(--gray-900); }
  .payroll-row.total { background: var(--primary-light); margin: 8px -22px -22px; padding: 16px 22px; border-radius: 0 0 var(--radius) var(--radius); }
  .payroll-row.total .label { color: var(--primary-dark); font-weight: 700; }
  .payroll-row.total .value { color: var(--primary-dark); font-size: 18px; }

  .filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 18px; flex-wrap: wrap; }
  .filter-bar .form-input, .filter-bar .form-select { padding: 8px 12px; font-size: 13px; }
  .filter-bar .spacer { flex: 1; }

  .form-input, .form-select { padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); font-size: 13.5px; font-family: inherit; color: var(--gray-800); transition: all .2s; background: #fff; }
  .form-input:focus, .form-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

  .role-banner { background: linear-gradient(135deg, var(--primary-light), #e0f2fe); border-radius: var(--radius); padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; border: 1px solid var(--primary); }
  .role-banner i { color: var(--primary); font-size: 20px; }
  .role-banner .text { font-size: 13px; color: var(--primary-dark); font-weight: 500; }
  .role-banner strong { font-weight: 800; }

  @media (max-width: 1200px) {
    .grid-2 { grid-template-columns: 1fr; }
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
    .grid-2-equal { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .sidebar { width: 0; overflow: hidden; }
    .main { margin-left: 0; }
    .grid-3 { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: 1fr; }
    .search-box { display: none; }
    .content { padding: 20px 16px; }
    .topbar { padding: 14px 16px; }
  }
</style>
</head>
<body>

<div class="app">
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <i class="fas fa-tshirt"></i>
        <div>Konveksi<span>Ku</span></div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-title">Anggota 5</div>
        <a class="nav-item active" data-page="dashboard"><i class="fas fa-th-large"></i> Dashboard</a>
        <a class="nav-item" data-page="payroll"><i class="fas fa-money-bill-wave"></i> Payroll</a>
        <a class="nav-item" data-page="keuangan"><i class="fas fa-wallet"></i> Keuangan</a>
        <a class="nav-item" data-page="laporan"><i class="fas fa-file-invoice"></i> Laporan</a>
        <a class="nav-item" data-page="notifikasi"><i class="fas fa-bell"></i> Notifikasi</a>
      </div>
    </nav>
    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar" id="userAvatar">KU</div>
        <div class="user-info">
          <div class="name" id="userName">Siti Rahayu</div>
          <div class="role" id="userRole">Staff Keuangan</div>
        </div>
      </div>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="topbar-left">
        <h1 id="pageTitle">Dashboard</h1>
        <div class="breadcrumb" id="pageBreadcrumb">Home / Dashboard</div>
      </div>
      <div class="topbar-right">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Cari..." />
        </div>
        <div class="role-switcher">
          <i class="fas fa-user-shield"></i>
          <select id="roleSwitcher">
            <option value="admin">Admin</option>
            <option value="owner">Owner</option>
            <option value="keuangan" selected>Keuangan</option>
            <option value="gudang">Gudang</option>
            <option value="produksi">Produksi</option>
            <option value="karyawan">Karyawan</option>
          </select>
        </div>
        <div class="topbar-icon"><i class="fas fa-bell"></i><span class="dot"></span></div>
      </div>
    </header>

    <div class="content">

      <!-- DASHBOARD -->
      <section class="page active" id="page-dashboard">
        <div class="role-banner">
          <i class="fas fa-info-circle"></i>
          <div class="text">Anda login sebagai <strong id="roleBannerName">Staff Keuangan</strong>.</div>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon green"><i class="fas fa-arrow-down"></i></div>
              <span class="badge badge-success">+8.5%</span>
            </div>
            <div class="stat-label">Pemasukan</div>
            <div class="stat-value">Rp 84.5jt</div>
            <div class="stat-change up"><i class="fas fa-arrow-up"></i> Naik dari bulan lalu</div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon red"><i class="fas fa-arrow-up"></i></div>
              <span class="badge badge-danger">-5%</span>
            </div>
            <div class="stat-label">Pengeluaran</div>
            <div class="stat-value">Rp 42.3jt</div>
            <div class="stat-change down"><i class="fas fa-arrow-down"></i> Turun dari bulan lalu</div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon blue"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="stat-label">Saldo / Arus Kas</div>
            <div class="stat-value">Rp 42.2jt</div>
            <div class="stat-change up"><i class="fas fa-check"></i> Sehat</div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <div class="stat-icon orange"><i class="fas fa-shopping-cart"></i></div>
              <span class="badge badge-warning">Aktif</span>
            </div>
            <div class="stat-label">Order Aktif</div>
            <div class="stat-value">23</div>
            <div class="stat-change down"><i class="fas fa-clock"></i> 5 mendekati deadline</div>
          </div>
        </div>

        <div class="grid-2">
          <div class="card">
            <div class="card-header">
              <h3>Ringkasan Keuangan</h3>
              <a class="card-action" data-page="laporan">Lihat Detail <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <div class="chart-bar"><div class="bar income" style="height:60%"></div><div class="bar expense" style="height:35%"></div><span class="label">Jan</span></div>
                <div class="chart-bar"><div class="bar income" style="height:75%"></div><div class="bar expense" style="height:45%"></div><span class="label">Feb</span></div>
                <div class="chart-bar"><div class="bar income" style="height:55%"></div><div class="bar expense" style="height:30%"></div><span class="label">Mar</span></div>
                <div class="chart-bar"><div class="bar income" style="height:90%"></div><div class="bar expense" style="height:50%"></div><span class="label">Apr</span></div>
                <div class="chart-bar"><div class="bar income" style="height:70%"></div><div class="bar expense" style="height:40%"></div><span class="label">Mei</span></div>
                <div class="chart-bar"><div class="bar income" style="height:85%"></div><div class="bar expense" style="height:55%"></div><span class="label">Jun</span></div>
              </div>
              <div class="chart-legend">
                <div class="legend-item"><div class="legend-dot green"></div> Pemasukan</div>
                <div class="legend-item"><div class="legend-dot red"></div> Pengeluaran</div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3>Notifikasi</h3>
              <a class="card-action" data-page="notifikasi">Semua</a>
            </div>
            <div class="card-body">
              <div class="notification-list">
                <div class="notification-item danger">
                  <div class="notification-icon red"><i class="fas fa-box"></i></div>
                  <div class="notification-content">
                    <div class="title">Stok Minimum</div>
                    <div class="desc">Kain Katun — sisa 15 m</div>
                  </div>
                </div>
                <div class="notification-item warning">
                  <div class="notification-icon orange"><i class="fas fa-clock"></i></div>
                  <div class="notification-content">
                    <div class="title">Deadline Order #ORD-024</div>
                    <div class="desc">2 hari lagi</div>
                  </div>
                </div>
                <div class="notification-item success">
                  <div class="notification-icon green"><i class="fas fa-money-bill"></i></div>
                  <div class="notification-content">
                    <div class="title">Jadwal Gaji</div>
                    <div class="desc">Payroll Juni siap diproses</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- PAYROLL -->
      <section class="page" id="page-payroll">
        <div class="filter-bar">
          <select class="form-select"><option>Periode: Juni 2026</option><option>Periode: Mei 2026</option></select>
          <div class="spacer"></div>
          <button class="btn btn-primary"><i class="fas fa-play"></i> Proses Payroll</button>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon blue"><i class="fas fa-users"></i></div></div>
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-value">45</div>
          </div>
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon green"><i class="fas fa-money-bill"></i></div></div>
            <div class="stat-label">Total Gaji Bersih</div>
            <div class="stat-value">Rp 68.2jt</div>
          </div>
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon orange"><i class="fas fa-clock"></i></div></div>
            <div class="stat-label">Status</div>
            <div class="stat-value" style="font-size:18px">Draft</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3>Rincian Payroll — Juni 2026</h3></div>
          <div class="table-container">
            <table>
              <thead>
                <tr><th>Karyawan</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Lembur</th><th>Bonus</th><th>Potongan</th><th>Gaji Bersih</th><th>Status</th></tr>
              </thead>
              <tbody>
                <tr><td><strong>Budi Santoso</strong></td><td>Supervisor</td><td>Rp 5.500.000</td><td>Rp 500.000</td><td>Rp 400.000</td><td>Rp 300.000</td><td>Rp 150.000</td><td><strong>Rp 6.550.000</strong></td><td><span class="badge badge-success">Dibayar</span></td></tr>
                <tr><td><strong>Siti Rahayu</strong></td><td>Admin Keuangan</td><td>Rp 4.500.000</td><td>Rp 400.000</td><td>—</td><td>Rp 200.000</td><td>Rp 100.000</td><td><strong>Rp 5.000.000</strong></td><td><span class="badge badge-success">Dibayar</span></td></tr>
                <tr><td><strong>Agus Wijaya</strong></td><td>Operator Jahit</td><td>Rp 3.200.000</td><td>Rp 200.000</td><td>Rp 350.000</td><td>Rp 150.000</td><td>Rp 80.000</td><td><strong>Rp 3.820.000</strong></td><td><span class="badge badge-warning">Pending</span></td></tr>
                <tr><td><strong>Dewi Lestari</strong></td><td>Staff Gudang</td><td>Rp 3.000.000</td><td>Rp 200.000</td><td>—</td><td>Rp 100.000</td><td>Rp 60.000</td><td><strong>Rp 3.240.000</strong></td><td><span class="badge badge-warning">Pending</span></td></tr>
                <tr><td><strong>Rudi Hartono</strong></td><td>QC</td><td>Rp 3.500.000</td><td>Rp 250.000</td><td>Rp 200.000</td><td>Rp 150.000</td><td>Rp 100.000</td><td><strong>Rp 4.000.000</strong></td><td><span class="badge badge-gray">Cuti</span></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- KEUANGAN -->
      <section class="page" id="page-keuangan">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon green"><i class="fas fa-arrow-down"></i></div><span class="badge badge-success">+12%</span></div>
            <div class="stat-label">Total Pemasukan</div>
            <div class="stat-value">Rp 84.500.000</div>
          </div>
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon red"><i class="fas fa-arrow-up"></i></div><span class="badge badge-danger">-5%</span></div>
            <div class="stat-label">Total Pengeluaran</div>
            <div class="stat-value">Rp 42.300.000</div>
          </div>
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon blue"><i class="fas fa-wallet"></i></div></div>
            <div class="stat-label">Saldo / Arus Kas</div>
            <div class="stat-value">Rp 42.200.000</div>
          </div>
          <div class="stat-card">
            <div class="stat-header"><div class="stat-icon purple"><i class="fas fa-chart-line"></i></div></div>
            <div class="stat-label">Laba Bersih</div>
            <div class="stat-value">Rp 42.200.000</div>
          </div>
        </div>

        <div class="card" style="margin-bottom:22px">
          <div class="card-header"><h3>Kategori Transaksi</h3></div>
          <div class="card-body">
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <span class="badge badge-success">Pemasukan: Pembayaran Pelanggan</span>
              <span class="badge badge-success">Pemasukan: Lain-lain</span>
              <span class="badge badge-danger">Pengeluaran: Pembelian Bahan</span>
              <span class="badge badge-danger">Pengeluaran: Biaya Operasional</span>
              <span class="badge badge-danger">Pengeluaran: Pembayaran Gaji</span>
            </div>
          </div>
        </div>

        <div class="grid-2-equal">
          <div class="card">
            <div class="card-header"><h3>💚 Pemasukan</h3><button class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Catat</button></div>
            <div class="table-container">
              <table>
                <thead><tr><th>Tanggal</th><th>Kategori</th><th>Sumber</th><th>Nominal</th></tr></thead>
                <tbody>
                  <tr><td>22 Jun 2026</td><td>Pembayaran Pelanggan</td><td>#ORD-024 DP</td><td><strong style="color:var(--success)">Rp 5.000.000</strong></td></tr>
                  <tr><td>21 Jun 2026</td><td>Pembayaran Pelanggan</td><td>#ORD-023 Pelunasan</td><td><strong style="color:var(--success)">Rp 9.000.000</strong></td></tr>
                  <tr><td>20 Jun 2026</td><td>Pembayaran Pelanggan</td><td>#ORD-021 DP</td><td><strong style="color:var(--success)">Rp 4.800.000</strong></td></tr>
                  <tr><td>18 Jun 2026</td><td>Pembayaran Pelanggan</td><td>#ORD-020 Pelunasan</td><td><strong style="color:var(--success)">Rp 2.400.000</strong></td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card">
            <div class="card-header"><h3>❤️ Pengeluaran</h3><button class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Catat</button></div>
            <div class="table-container">
              <table>
                <thead><tr><th>Tanggal</th><th>Kategori</th><th>Tujuan</th><th>Nominal</th></tr></thead>
                <tbody>
                  <tr><td>22 Jun 2026</td><td>Pembelian Bahan</td><td>Kain Katun</td><td><strong style="color:var(--danger)">- Rp 3.500.000</strong></td></tr>
                  <tr><td>21 Jun 2026</td><td>Biaya Operasional</td><td>Listrik & Air</td><td><strong style="color:var(--danger)">- Rp 1.200.000</strong></td></tr>
                  <tr><td>20 Jun 2026</td><td>Pembelian Bahan</td><td>Benang & Kancing</td><td><strong style="color:var(--danger)">- Rp 850.000</strong></td></tr>
                  <tr><td>19 Jun 2026</td><td>Pembayaran Gaji</td><td>Payroll Mei 2026</td><td><strong style="color:var(--danger)">- Rp 65.000.000</strong></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- LAPORAN -->
      <section class="page" id="page-laporan">
        <div class="filter-bar">
          <select class="form-select"><option>Juni 2026</option><option>Mei 2026</option></select>
          <select class="form-select">
            <option>Semua Laporan</option>
            <option>Laporan Order</option>
            <option>Laporan Produksi</option>
            <option>Laporan Stok</option>
            <option>Laporan Pemasukan</option>
            <option>Laporan Pengeluaran</option>
            <option>Laporan Payroll</option>
            <option>Laporan Absensi</option>
            <option>Laporan Laba/Rugi</option>
          </select>
          <div class="spacer"></div>
          <button class="btn btn-outline"><i class="fas fa-file-pdf"></i> PDF</button>
          <button class="btn btn-outline"><i class="fas fa-file-excel"></i> Excel</button>
          <button class="btn btn-primary"><i class="fas fa-print"></i> Cetak</button>
        </div>

        <div class="card">
          <div class="card-header"><h3>📊 Laporan Laba / Rugi Sederhana — Juni 2026</h3></div>
          <div class="card-body">
            <table>
              <tbody>
                <tr><td><strong>PENDAPATAN</strong></td><td></td><td></td></tr>
                <tr><td style="padding-left:32px">Penjualan Produk</td><td></td><td style="text-align:right"><strong>Rp 84.500.000</strong></td></tr>
                <tr><td style="padding-left:32px"><strong>Total Pendapatan</strong></td><td></td><td style="text-align:right"><strong style="color:var(--success)">Rp 84.500.000</strong></td></tr>
                <tr><td colspan="3" style="padding:8px 0"><hr style="border:none;border-top:1px dashed var(--gray-300)"></td></tr>
                <tr><td><strong>BIAYA & PENGELUARAN</strong></td><td></td><td></td></tr>
                <tr><td style="padding-left:32px">Pembelian Bahan Baku</td><td></td><td style="text-align:right">Rp 15.200.000</td></tr>
                <tr><td style="padding-left:32px">Gaji Karyawan</td><td></td><td style="text-align:right">Rp 65.000.000</td></tr>
                <tr><td style="padding-left:32px">Biaya Operasional</td><td></td><td style="text-align:right">Rp 4.500.000</td></tr>
                <tr><td style="padding-left:32px">Lain-lain</td><td></td><td style="text-align:right">Rp 1.200.000</td></tr>
                <tr><td style="padding-left:32px"><strong>Total Pengeluaran</strong></td><td></td><td style="text-align:right"><strong style="color:var(--danger)">Rp 85.900.000</strong></td></tr>
                <tr><td colspan="3" style="padding:8px 0"><hr style="border:none;border-top:2px solid var(--gray-300)"></td></tr>
                <tr style="background:var(--gray-50)"><td><strong>LABA / RUGI BERSIH</strong></td><td></td><td style="text-align:right"><strong style="color:var(--danger);font-size:16px">- Rp 1.400.000</strong></td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="grid-3" style="margin-top:22px">
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon green" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-file-invoice"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Order</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">148 order bulan ini</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon orange" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-industry"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Produksi</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">23 job berjalan</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon blue" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-boxes"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Stok</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">24 item gudang</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon green" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-money-bill"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Payroll</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">45 karyawan</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon purple" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-user-check"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Absensi</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">93% kehadiran</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
          <div class="card">
            <div class="card-body" style="text-align:center">
              <div class="stat-icon cyan" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px"><i class="fas fa-chart-line"></i></div>
              <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laba / Rugi</div>
              <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">Laporan keuangan</div>
              <button class="btn btn-outline btn-sm">Lihat</button>
            </div>
          </div>
        </div>
      </section>

      <!-- NOTIFIKASI -->
      <section class="page" id="page-notifikasi">
        <div class="card">
          <div class="card-header"><h3>Notifikasi</h3><a class="card-action">Tandai Semua Dibaca</a></div>
          <div class="card-body">
            <div class="notification-list">
              <div class="notification-item danger">
                <div class="notification-icon red"><i class="fas fa-box"></i></div>
                <div class="notification-content">
                  <div class="title">Stok Minimum</div>
                  <div class="desc">Kain Katun Putih — sisa 15 m (min. 50 m)</div>
                </div>
              </div>
              <div class="notification-item danger">
                <div class="notification-icon red"><i class="fas fa-box"></i></div>
                <div class="notification-content">
                  <div class="title">Stok Minimum</div>
                  <div class="desc">Kancing Baju — sisa 8 lusin (min. 20 lusin)</div>
                </div>
              </div>
              <div class="notification-item warning">
                <div class="notification-icon orange"><i class="fas fa-clock"></i></div>
                <div class="notification-content">
                  <div class="title">Deadline Order</div>
                  <div class="desc">#ORD-024 — CV Maju Jaya — 2 hari lagi</div>
                </div>
              </div>
              <div class="notification-item">
                <div class="notification-icon blue"><i class="fas fa-industry"></i></div>
                <div class="notification-content">
                  <div class="title">Produksi Tertunda</div>
                  <div class="desc">Job #JOB-012 melewati target</div>
                </div>
              </div>
              <div class="notification-item success">
                <div class="notification-icon green"><i class="fas fa-money-bill"></i></div>
                <div class="notification-content">
                  <div class="title">Jadwal Gaji</div>
                  <div class="desc">Payroll Juni 2026 siap diproses</div>
                </div>
              </div>
              <div class="notification-item warning">
                <div class="notification-icon orange"><i class="fas fa-check-circle"></i></div>
                <div class="notification-content">
                  <div class="title">Transaksi Perlu Persetujuan</div>
                  <div class="desc">Pengeluaran Rp 3.500.000 — Pembelian Kain Katun</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>
</div>

<script>
  const roleConfig = {
    admin:     { name: 'Admin Utama',   role: 'Administrator',         avatar: 'AD' },
    owner:     { name: 'Bpk. Hendra',   role: 'Owner / Pimpinan',      avatar: 'OW' },
    keuangan:  { name: 'Siti Rahayu',   role: 'Staff Keuangan',        avatar: 'KU' },
    gudang:    { name: 'Dewi Lestari',  role: 'Staff Gudang',          avatar: 'GD' },
    produksi:  { name: 'Budi Santoso',  role: 'Supervisor Produksi',   avatar: 'PR' },
    karyawan:  { name: 'Agus Wijaya',   role: 'Operator Jahit',        avatar: 'AG' }
  };

  const pageNames = {
    dashboard: 'Dashboard',
    payroll: 'Payroll',
    keuangan: 'Keuangan',
    laporan: 'Laporan',
    notifikasi: 'Notifikasi'
  };

  const navItems = document.querySelectorAll('.nav-item');
  const pages = document.querySelectorAll('.page');
  const pageTitle = document.getElementById('pageTitle');
  const pageBreadcrumb = document.getElementById('pageBreadcrumb');
  const roleSwitcher = document.getElementById('roleSwitcher');
  const userName = document.getElementById('userName');
  const userRole = document.getElementById('userRole');
  const userAvatar = document.getElementById('userAvatar');
  const roleBannerName = document.getElementById('roleBannerName');

  function applyRole(role) {
    const cfg = roleConfig[role];
    if (!cfg) return;
    userName.textContent = cfg.name;
    userRole.textContent = cfg.role;
    userAvatar.textContent = cfg.avatar;
    if (roleBannerName) roleBannerName.textContent = cfg.role;
  }

  function showPage(pageId) {
    pages.forEach(p => p.classList.remove('active'));
    const target = document.getElementById('page-' + pageId);
    if (target) target.classList.add('active');
    navItems.forEach(n => n.classList.remove('active'));
    const activeNav = document.querySelector(`.nav-item[data-page="${pageId}"]`);
    if (activeNav) activeNav.classList.add('active');
    const name = pageNames[pageId] || 'Dashboard';
    pageTitle.textContent = name;
    pageBreadcrumb.textContent = 'Home / ' + name;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.querySelectorAll('[data-page]').forEach(el => {
    el.addEventListener('click', () => showPage(el.getAttribute('data-page')));
  });

  roleSwitcher.addEventListener('change', (e) => applyRole(e.target.value));
  applyRole('keuangan');
  console.log('✅ Anggota 5 — Finance, Payroll & Dashboard loaded');
</script>

</body>
</html>