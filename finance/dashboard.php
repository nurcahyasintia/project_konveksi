<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KonveksiKu — Dashboard Anggota 5</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
  --primary: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #dbeafe;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --info: #06b6d4;
  --purple: #8b5cf6;
  --dark: #0f172a;
  --gray-50: #f8fafc;
  --gray-100: #f1f5f9;
  --gray-200: #e2e8f0;
  --gray-300: #cbd5e1;
  --gray-400: #94a3b8;
  --gray-500: #64748b;
  --gray-600: #475569;
  --gray-700: #334155;
  --gray-800: #1e293b;
  --gray-900: #0f172a;
  --radius: 14px;
  --radius-sm: 10px;
  --shadow: 0 1px 3px rgba(0,0,0,.08);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  background: var(--gray-100);
  color: var(--gray-800);
  line-height: 1.6;
}

.app {
  display: flex;
  min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
  width: 260px;
  background: var(--dark);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  inset: 0 auto 0 0;
  height: 100vh;
  z-index: 100;
}

.sidebar-header {
  padding: 22px 20px;
  border-bottom: 1px solid rgba(255,255,255,.08);
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 800;
}

.logo i {
  color: var(--primary);
  background: rgba(37,99,235,.15);
  padding: 8px;
  border-radius: 10px;
}

.logo span {
  color: var(--primary);
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  overflow-y: auto;
}

.nav-section-title {
  font-size: 10.5px;
  text-transform: uppercase;
  letter-spacing: 1.4px;
  color: var(--gray-500);
  padding: 8px 12px;
  font-weight: 700;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: var(--radius-sm);
  color: #94a3b8;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 3px;
  transition: .2s;
}

.nav-item:hover {
  background: rgba(255,255,255,.06);
  color: white;
}

.nav-item.active {
  background: var(--primary);
  color: white;
  box-shadow: 0 4px 12px rgba(37,99,235,.35);
}

.nav-item i {
  width: 20px;
  text-align: center;
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(255,255,255,.08);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg,var(--primary),var(--info));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.user-info .name {
  font-size: 13.5px;
  font-weight: 600;
}

.user-info .role {
  font-size: 11.5px;
  color: var(--gray-400);
}

/* MAIN */
.main {
  flex: 1;
  margin-left: 260px;
  min-height: 100vh;
}

.topbar {
  background: white;
  padding: 16px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--gray-200);
  position: sticky;
  top: 0;
  z-index: 50;
}

.topbar-left h1 {
  font-size: 20px;
  font-weight: 800;
  color: var(--gray-900);
}

.breadcrumb {
  font-size: 12.5px;
  color: var(--gray-500);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--gray-100);
  padding: 9px 16px;
  border-radius: var(--radius-sm);
  width: 240px;
}

.search-box input {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-family: inherit;
}

.role-switcher {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--primary-light);
  color: var(--primary-dark);
  padding: 6px 12px;
  border-radius: var(--radius-sm);
  font-size: 12px;
  font-weight: 700;
}

.role-switcher select {
  border: none;
  background: transparent;
  color: var(--primary-dark);
  font-weight: 700;
  outline: none;
}

.topbar-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.topbar-icon .dot {
  position: absolute;
  top: 7px;
  right: 7px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--danger);
  border: 2px solid white;
}

.content {
  padding: 28px 32px;
}

/* DASHBOARD */
.role-banner {
  background: linear-gradient(135deg,var(--primary-light),#e0f2fe);
  border: 1px solid var(--primary);
  border-radius: var(--radius);
  padding: 14px 20px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--primary-dark);
  font-size: 13px;
}

.role-banner i {
  font-size: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4,minmax(0,1fr));
  gap: 18px;
  margin-bottom: 26px;
}

.stat-card {
  background: white;
  border-radius: var(--radius);
  padding: 22px;
  box-shadow: var(--shadow);
  border: 1px solid var(--gray-200);
  transition: .2s;
}

.stat-card:hover {
  transform: translateY(-3px);
}

.stat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.stat-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
}

.green {
  background: #d1fae5;
  color: var(--success);
}

.red {
  background: #fee2e2;
  color: var(--danger);
}

.blue {
  background: var(--primary-light);
  color: var(--primary);
}

.orange {
  background: #fef3c7;
  color: var(--warning);
}

.badge {
  display: inline-flex;
  padding: 4px 11px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 700;
}

.badge-success {
  background: #d1fae5;
  color: #065f46;
}

.badge-danger {
  background: #fee2e2;
  color: #991b1b;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.stat-label {
  font-size: 12.5px;
  color: var(--gray-500);
  margin-bottom: 4px;
}

.stat-value {
  font-size: 25px;
  font-weight: 800;
  color: var(--gray-900);
  overflow-wrap: anywhere;
}

.stat-change {
  font-size: 11.5px;
  font-weight: 600;
  margin-top: 8px;
}

.stat-change.up {
  color: var(--success);
}

.stat-change.down {
  color: var(--danger);
}

/* CARDS AND CHART */
.grid-2 {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 22px;
}

.card {
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  border: 1px solid var(--gray-200);
  overflow: hidden;
}

.card-header {
  padding: 18px 22px;
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.card-header h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-900);
}

.card-action {
  font-size: 12.5px;
  color: var(--primary);
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
}

.card-body {
  padding: 22px;
}

.chart-container {
  display: flex;
  align-items: flex-end;
  gap: 14px;
  height: 200px;
  padding-top: 20px;
}

.chart-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  height: 100%;
  justify-content: flex-end;
}

.bar-pair {
  height: 100%;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 5px;
}

.bar {
  width: 35%;
  border-radius: 6px 6px 0 0;
  min-height: 4px;
}

.bar.income {
  background: linear-gradient(180deg,var(--success),#34d399);
}

.bar.expense {
  background: linear-gradient(180deg,var(--danger),#f87171);
}

.chart-bar .label {
  font-size: 11px;
  color: var(--gray-500);
  font-weight: 600;
}

.chart-legend {
  display: flex;
  gap: 20px;
  margin-top: 16px;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  color: var(--gray-600);
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 3px;
}

.legend-dot.green {
  background: var(--success);
}

.legend-dot.red {
  background: var(--danger);
}

/* NOTIFICATIONS */
.notification-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.notification-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: var(--radius-sm);
  background: var(--gray-50);
  border-left: 3px solid var(--primary);
}

.notification-item.warning {
  border-left-color: var(--warning);
}

.notification-item.danger {
  border-left-color: var(--danger);
}

.notification-item.success {
  border-left-color: var(--success);
}

.notification-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-icon.blue {
  background: var(--primary-light);
}

.notification-icon.orange {
  background: #fef3c7;
}

.notification-icon.red {
  background: #fee2e2;
}

.notification-icon.green {
  background: #d1fae5;
}

.notification-content .title {
  font-size: 13px;
  font-weight: 700;
}

.notification-content .desc {
  font-size: 11.5px;
  color: var(--gray-500);
}

@media(max-width:1200px) {
  .stats-grid {
    grid-template-columns: repeat(2,minmax(0,1fr));
  }

  .grid-2 {
    grid-template-columns: 1fr;
  }
}

@media(max-width:768px) {
  .sidebar {
    width: 0;
    overflow: hidden;
  }

  .main {
    margin-left: 0;
  }

  .topbar {
    padding: 14px 16px;
  }

  .content {
    padding: 20px 16px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .search-box {
    display: none;
  }
}
</style>
</head>

<body>
<div class="app">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <i class="fas fa-tshirt"></i>
        <div>Konveksi<span>Ku</span></div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Anggota 5</div>

      <a href="dashboard.php" class="nav-item active">
        <i class="fas fa-th-large"></i> Dashboard
      </a>

      <a href="payroll.php" class="nav-item">
        <i class="fas fa-money-bill-wave"></i> Payroll
      </a>

      <a href="keuangan.php" class="nav-item">
        <i class="fas fa-wallet"></i> Keuangan
      </a>

      <a href="laporan.php" class="nav-item">
        <i class="fas fa-file-invoice"></i> Laporan
      </a>

      <a href="notifikasi.php" class="nav-item">
        <i class="fas fa-bell"></i> Notifikasi
      </a>
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

  <!-- MAIN CONTENT -->
  <main class="main">
    <header class="topbar">
      <div class="topbar-left">
        <h1>Dashboard</h1>
        <div class="breadcrumb">Home / Dashboard</div>
      </div>

      <div class="topbar-right">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Cari...">
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

        <a href="notifikasi.php" class="topbar-icon" title="Notifikasi">
          <i class="fas fa-bell"></i>
          <span class="dot"></span>
        </a>
      </div>
    </header>

    <div class="content">

      <div class="role-banner">
        <i class="fas fa-info-circle"></i>
        <div>
          Anda login sebagai
          <strong id="roleBannerName">Staff Keuangan</strong>.
        </div>
      </div>

      <!-- STATISTIK -->
      <div class="stats-grid">

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon green">
              <i class="fas fa-arrow-down"></i>
            </div>
            <span class="badge badge-success">+8.5%</span>
          </div>
          <div class="stat-label">Pemasukan</div>
          <div class="stat-value">Rp 84.5jt</div>
          <div class="stat-change up">
            <i class="fas fa-arrow-up"></i>
            Naik dari bulan lalu
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon red">
              <i class="fas fa-arrow-up"></i>
            </div>
            <span class="badge badge-danger">-5%</span>
          </div>
          <div class="stat-label">Pengeluaran</div>
          <div class="stat-value">Rp 42.3jt</div>
          <div class="stat-change down">
            <i class="fas fa-arrow-down"></i>
            Turun dari bulan lalu
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon blue">
              <i class="fas fa-wallet"></i>
            </div>
          </div>
          <div class="stat-label">Saldo / Arus Kas</div>
          <div class="stat-value">Rp 42.2jt</div>
          <div class="stat-change up">
            <i class="fas fa-check"></i> Sehat
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon orange">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <span class="badge badge-warning">Aktif</span>
          </div>
          <div class="stat-label">Order Aktif</div>
          <div class="stat-value">23</div>
          <div class="stat-change down">
            <i class="fas fa-clock"></i>
            5 mendekati deadline
          </div>
        </div>

      </div>

      <!-- GRAFIK DAN NOTIFIKASI -->
      <div class="grid-2">

        <div class="card">
          <div class="card-header">
            <h3>Ringkasan Keuangan</h3>
            <a href="laporan.php" class="card-action">
              Lihat Detail <i class="fas fa-arrow-right"></i>
            </a>
          </div>

          <div class="card-body">
            <div class="chart-container">

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:60%"></div>
                  <div class="bar expense" style="height:35%"></div>
                </div>
                <span class="label">Jan</span>
              </div>

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:75%"></div>
                  <div class="bar expense" style="height:45%"></div>
                </div>
                <span class="label">Feb</span>
              </div>

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:55%"></div>
                  <div class="bar expense" style="height:30%"></div>
                </div>
                <span class="label">Mar</span>
              </div>

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:90%"></div>
                  <div class="bar expense" style="height:50%"></div>
                </div>
                <span class="label">Apr</span>
              </div>

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:70%"></div>
                  <div class="bar expense" style="height:40%"></div>
                </div>
                <span class="label">Mei</span>
              </div>

              <div class="chart-bar">
                <div class="bar-pair">
                  <div class="bar income" style="height:85%"></div>
                  <div class="bar expense" style="height:55%"></div>
                </div>
                <span class="label">Jun</span>
              </div>

            </div>

            <div class="chart-legend">
              <div class="legend-item">
                <div class="legend-dot green"></div>
                Pemasukan
              </div>

              <div class="legend-item">
                <div class="legend-dot red"></div>
                Pengeluaran
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3>Notifikasi</h3>
            <a href="notifikasi.php" class="card-action">Semua</a>
          </div>

          <div class="card-body">
            <div class="notification-list">

              <div class="notification-item danger">
                <div class="notification-icon red">
                  <i class="fas fa-box"></i>
                </div>
                <div class="notification-content">
                  <div class="title">Stok Minimum</div>
                  <div class="desc">Kain Katun — sisa 15 m</div>
                </div>
              </div>

              <div class="notification-item warning">
                <div class="notification-icon orange">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="notification-content">
                  <div class="title">Deadline Order #ORD-024</div>
                  <div class="desc">2 hari lagi</div>
                </div>
              </div>

              <div class="notification-item success">
                <div class="notification-icon green">
                  <i class="fas fa-money-bill"></i>
                </div>
                <div class="notification-content">
                  <div class="title">Jadwal Gaji</div>
                  <div class="desc">Payroll Juni siap diproses</div>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<script>
const roleConfig = {
  admin: {
    name: 'Admin Utama',
    role: 'Administrator',
    avatar: 'AD'
  },
  owner: {
    name: 'Bpk. Hendra',
    role: 'Owner / Pimpinan',
    avatar: 'OW'
  },
  keuangan: {
    name: 'Siti Rahayu',
    role: 'Staff Keuangan',
    avatar: 'KU'
  },
  gudang: {
    name: 'Dewi Lestari',
    role: 'Staff Gudang',
    avatar: 'GD'
  },
  produksi: {
    name: 'Budi Santoso',
    role: 'Supervisor Produksi',
    avatar: 'PR'
  },
  karyawan: {
    name: 'Agus Wijaya',
    role: 'Operator Jahit',
    avatar: 'AG'
  }
};

const roleSwitcher = document.getElementById('roleSwitcher');

roleSwitcher.addEventListener('change', function () {
  const role = roleConfig[this.value];

  if (!role) return;

  document.getElementById('userName').textContent = role.name;
  document.getElementById('userRole').textContent = role.role;
  document.getElementById('userAvatar').textContent = role.avatar;
  document.getElementById('roleBannerName').textContent = role.role;
});
</script>

</body>
</html>
