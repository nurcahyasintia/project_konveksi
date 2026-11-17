<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KonveksiKu — Payroll Anggota 5</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
  --primary:#2563eb;
  --primary-dark:#1d4ed8;
  --primary-light:#dbeafe;
  --success:#10b981;
  --warning:#f59e0b;
  --danger:#ef4444;
  --info:#06b6d4;
  --purple:#8b5cf6;
  --dark:#0f172a;
  --gray-50:#f8fafc;
  --gray-100:#f1f5f9;
  --gray-200:#e2e8f0;
  --gray-300:#cbd5e1;
  --gray-400:#94a3b8;
  --gray-500:#64748b;
  --gray-600:#475569;
  --gray-700:#334155;
  --gray-800:#1e293b;
  --gray-900:#0f172a;
  --radius:14px;
  --radius-sm:10px;
  --shadow:0 1px 3px rgba(0,0,0,.08);
}

* {
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body {
  font-family:'Inter',sans-serif;
  background:var(--gray-100);
  color:var(--gray-800);
  line-height:1.6;
}

.app {
  display:flex;
  min-height:100vh;
}

/* SIDEBAR */
.sidebar {
  width:260px;
  background:var(--dark);
  color:#fff;
  display:flex;
  flex-direction:column;
  position:fixed;
  inset:0 auto 0 0;
  height:100vh;
  z-index:100;
}

.sidebar-header {
  padding:22px 20px;
  border-bottom:1px solid rgba(255,255,255,.08);
}

.logo {
  display:flex;
  align-items:center;
  gap:12px;
  font-size:20px;
  font-weight:800;
}

.logo i {
  color:var(--primary);
  background:rgba(37,99,235,.15);
  padding:8px;
  border-radius:10px;
}

.logo span {
  color:var(--primary);
}

.sidebar-nav {
  flex:1;
  padding:16px 12px;
  overflow-y:auto;
}

.nav-section-title {
  font-size:10.5px;
  text-transform:uppercase;
  letter-spacing:1.4px;
  color:var(--gray-500);
  padding:8px 12px;
  font-weight:700;
}

.nav-item {
  display:flex;
  align-items:center;
  gap:12px;
  padding:10px 12px;
  border-radius:var(--radius-sm);
  color:#94a3b8;
  text-decoration:none;
  font-size:14px;
  font-weight:500;
  margin-bottom:3px;
  transition:.2s;
}

.nav-item:hover {
  background:rgba(255,255,255,.06);
  color:#fff;
}

.nav-item.active {
  background:var(--primary);
  color:#fff;
  box-shadow:0 4px 12px rgba(37,99,235,.35);
}

.nav-item i {
  width:20px;
  text-align:center;
}

.sidebar-footer {
  padding:16px 20px;
  border-top:1px solid rgba(255,255,255,.08);
}

.user-profile {
  display:flex;
  align-items:center;
  gap:12px;
}

.user-avatar {
  width:40px;
  height:40px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--primary),var(--info));
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:700;
}

.user-info .name {
  font-size:13.5px;
  font-weight:600;
}

.user-info .role {
  font-size:11.5px;
  color:var(--gray-400);
}

/* MAIN */
.main {
  flex:1;
  margin-left:260px;
  min-height:100vh;
}

.topbar {
  background:#fff;
  padding:16px 32px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  border-bottom:1px solid var(--gray-200);
  position:sticky;
  top:0;
  z-index:50;
}

.topbar-left h1 {
  font-size:20px;
  font-weight:800;
  color:var(--gray-900);
}

.breadcrumb {
  font-size:12.5px;
  color:var(--gray-500);
}

.topbar-right {
  display:flex;
  align-items:center;
  gap:14px;
}

.search-box {
  display:flex;
  align-items:center;
  gap:8px;
  background:var(--gray-100);
  padding:9px 16px;
  border-radius:var(--radius-sm);
  width:240px;
}

.search-box input {
  width:100%;
  border:none;
  outline:none;
  background:transparent;
  font-family:inherit;
}

.role-switcher {
  display:flex;
  align-items:center;
  gap:6px;
  background:var(--primary-light);
  color:var(--primary-dark);
  padding:6px 12px;
  border-radius:var(--radius-sm);
  font-size:12px;
  font-weight:700;
}

.role-switcher select {
  border:none;
  background:transparent;
  color:var(--primary-dark);
  font-weight:700;
  outline:none;
}

.topbar-icon {
  width:40px;
  height:40px;
  border-radius:var(--radius-sm);
  background:var(--gray-100);
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
  color:var(--gray-700);
  text-decoration:none;
}

.topbar-icon .dot {
  position:absolute;
  top:7px;
  right:7px;
  width:8px;
  height:8px;
  border-radius:50%;
  background:var(--danger);
  border:2px solid white;
}

.content {
  padding:28px 32px;
}

/* FILTER */
.filter-bar {
  display:flex;
  gap:10px;
  align-items:center;
  margin-bottom:18px;
  flex-wrap:wrap;
}

.filter-bar .spacer {
  flex:1;
}

.form-select {
  padding:10px 14px;
  border:1px solid var(--gray-300);
  border-radius:var(--radius-sm);
  font-size:13.5px;
  font-family:inherit;
  color:var(--gray-800);
  background:#fff;
  outline:none;
}

.form-select:focus {
  border-color:var(--primary);
  box-shadow:0 0 0 3px var(--primary-light);
}

/* BUTTONS */
.btn {
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  padding:10px 18px;
  border-radius:var(--radius-sm);
  font-size:13.5px;
  font-weight:600;
  border:none;
  cursor:pointer;
  transition:.2s;
  text-decoration:none;
  font-family:inherit;
}

.btn-primary {
  background:var(--primary);
  color:#fff;
}

.btn-primary:hover {
  background:var(--primary-dark);
}

.btn-outline {
  background:#fff;
  color:var(--gray-700);
  border:1px solid var(--gray-300);
}

.btn-outline:hover {
  background:var(--gray-50);
}

.btn-sm {
  padding:6px 12px;
  font-size:12.5px;
}

/* STATISTICS */
.stats-grid {
  display:grid;
  grid-template-columns:repeat(3,minmax(0,1fr));
  gap:18px;
  margin-bottom:26px;
}

.stat-card {
  background:#fff;
  border-radius:var(--radius);
  padding:22px;
  box-shadow:var(--shadow);
  border:1px solid var(--gray-200);
}

.stat-header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:14px;
}

.stat-icon {
  width:46px;
  height:46px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:19px;
}

.blue {
  background:var(--primary-light);
  color:var(--primary);
}

.green {
  background:#d1fae5;
  color:var(--success);
}

.orange {
  background:#fef3c7;
  color:var(--warning);
}

.stat-label {
  font-size:12.5px;
  color:var(--gray-500);
  margin-bottom:4px;
}

.stat-value {
  font-size:26px;
  font-weight:800;
  color:var(--gray-900);
  overflow-wrap:anywhere;
}

/* TABLE */
.card {
  background:#fff;
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  border:1px solid var(--gray-200);
  overflow:hidden;
}

.card-header {
  padding:18px 22px;
  border-bottom:1px solid var(--gray-200);
  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:10px;
}

.card-header h3 {
  font-size:15px;
  font-weight:700;
  color:var(--gray-900);
}

.table-container {
  overflow-x:auto;
}

table {
  width:100%;
  border-collapse:collapse;
}

thead th {
  text-align:left;
  padding:12px 16px;
  font-size:11px;
  text-transform:uppercase;
  letter-spacing:.6px;
  color:var(--gray-500);
  font-weight:700;
  border-bottom:1px solid var(--gray-200);
  background:var(--gray-50);
  white-space:nowrap;
}

tbody td {
  padding:14px 16px;
  font-size:13px;
  color:var(--gray-700);
  border-bottom:1px solid var(--gray-100);
  white-space:nowrap;
}

tbody tr:hover {
  background:var(--gray-50);
}

tbody tr:last-child td {
  border-bottom:none;
}

.badge {
  display:inline-flex;
  align-items:center;
  gap:4px;
  padding:4px 11px;
  border-radius:20px;
  font-size:11.5px;
  font-weight:700;
  white-space:nowrap;
}

.badge-success {
  background:#d1fae5;
  color:#065f46;
}

.badge-warning {
  background:#fef3c7;
  color:#92400e;
}

.badge-gray {
  background:var(--gray-200);
  color:var(--gray-600);
}

.notice {
  margin-bottom:18px;
  padding:12px 16px;
  border-radius:var(--radius-sm);
  background:var(--primary-light);
  color:var(--primary-dark);
  font-size:13px;
}

@media(max-width:1200px) {
  .stats-grid {
    grid-template-columns:repeat(2,minmax(0,1fr));
  }
}

@media(max-width:768px) {
  .sidebar {
    width:0;
    overflow:hidden;
  }

  .main {
    margin-left:0;
  }

  .topbar {
    padding:14px 16px;
  }

  .content {
    padding:20px 16px;
  }

  .stats-grid {
    grid-template-columns:1fr;
  }

  .search-box {
    display:none;
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

      <a href="dashboard.php" class="nav-item">
        <i class="fas fa-th-large"></i> Dashboard
      </a>

      <a href="payroll.php" class="nav-item active">
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

  <!-- MAIN -->
  <main class="main">
    <header class="topbar">
      <div class="topbar-left">
        <h1>Payroll</h1>
        <div class="breadcrumb">Home / Payroll</div>
      </div>

      <div class="topbar-right">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="searchInput" placeholder="Cari karyawan...">
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

      <!-- FILTER PERIODE -->
      <div class="filter-bar">
        <select class="form-select" id="periodSelect">
          <option value="Juni 2026">Periode: Juni 2026</option>
          <option value="Mei 2026">Periode: Mei 2026</option>
        </select>

        <div class="spacer"></div>

        <button type="button" class="btn btn-primary" id="processButton">
          <i class="fas fa-play"></i> Proses Payroll
        </button>
      </div>

      <div class="notice" id="notice">
        Data berikut adalah contoh tampilan payroll, belum tersimpan ke database.
      </div>

      <!-- STATISTIK -->
      <div class="stats-grid">

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon blue">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="stat-label">Total Karyawan Ditampilkan</div>
          <div class="stat-value" id="totalEmployees">5</div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon green">
              <i class="fas fa-money-bill"></i>
            </div>
          </div>
          <div class="stat-label">Total Gaji Bersih</div>
          <div class="stat-value" id="totalSalary">Rp 22.610.000</div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div class="stat-icon orange">
              <i class="fas fa-clock"></i>
            </div>
          </div>
          <div class="stat-label">Status Periode</div>
          <div class="stat-value" style="font-size:20px" id="periodStatus">Draft</div>
        </div>

      </div>

      <!-- RINCIAN PAYROLL -->
      <div class="card">
        <div class="card-header">
          <h3 id="tableTitle">Rincian Payroll — Juni 2026</h3>
          <button type="button" class="btn btn-outline btn-sm" id="resetButton">
            <i class="fas fa-undo"></i> Reset Tampilan
          </button>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Karyawan</th>
                <th>Jabatan</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Lembur</th>
                <th>Bonus</th>
                <th>Potongan</th>
                <th>Gaji Bersih</th>
                <th>Status</th>
              </tr>
            </thead>

            <tbody id="payrollTable">
              <tr data-status="Dibayar">
                <td><strong>Budi Santoso</strong></td>
                <td>Supervisor</td>
                <td>Rp 5.500.000</td>
                <td>Rp 500.000</td>
                <td>Rp 400.000</td>
                <td>Rp 300.000</td>
                <td>Rp 150.000</td>
                <td><strong>Rp 6.550.000</strong></td>
                <td><span class="badge badge-success">Dibayar</span></td>
              </tr>

              <tr data-status="Dibayar">
                <td><strong>Siti Rahayu</strong></td>
                <td>Admin Keuangan</td>
                <td>Rp 4.500.000</td>
                <td>Rp 400.000</td>
                <td>—</td>
                <td>Rp 200.000</td>
                <td>Rp 100.000</td>
                <td><strong>Rp 5.000.000</strong></td>
                <td><span class="badge badge-success">Dibayar</span></td>
              </tr>

              <tr data-status="Pending">
                <td><strong>Agus Wijaya</strong></td>
                <td>Operator Jahit</td>
                <td>Rp 3.200.000</td>
                <td>Rp 200.000</td>
                <td>Rp 350.000</td>
                <td>Rp 150.000</td>
                <td>Rp 80.000</td>
                <td><strong>Rp 3.820.000</strong></td>
                <td><span class="badge badge-warning">Pending</span></td>
              </tr>

              <tr data-status="Pending">
                <td><strong>Dewi Lestari</strong></td>
                <td>Staff Gudang</td>
                <td>Rp 3.000.000</td>
                <td>Rp 200.000</td>
                <td>—</td>
                <td>Rp 100.000</td>
                <td>Rp 60.000</td>
                <td><strong>Rp 3.240.000</strong></td>
                <td><span class="badge badge-warning">Pending</span></td>
              </tr>

              <tr data-status="Cuti">
                <td><strong>Rudi Hartono</strong></td>
                <td>QC</td>
                <td>Rp 3.500.000</td>
                <td>Rp 250.000</td>
                <td>Rp 200.000</td>
                <td>Rp 150.000</td>
                <td>Rp 100.000</td>
                <td><strong>Rp 4.000.000</strong></td>
                <td><span class="badge badge-gray">Cuti</span></td>
              </tr>
            </tbody>
          </table>
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

roleSwitcher.addEventListener('change', function() {
  const role = roleConfig[this.value];

  if (!role) return;

  document.getElementById('userName').textContent = role.name;
  document.getElementById('userRole').textContent = role.role;
  document.getElementById('userAvatar').textContent = role.avatar;
});

const periodSelect = document.getElementById('periodSelect');
const tableTitle = document.getElementById('tableTitle');
const notice = document.getElementById('notice');
const processButton = document.getElementById('processButton');
const periodStatus = document.getElementById('periodStatus');
const payrollTable = document.getElementById('payrollTable');
const originalRows = payrollTable.innerHTML;

periodSelect.addEventListener('change', function() {
  tableTitle.textContent = 'Rincian Payroll — ' + this.value;
  notice.textContent =
    'Periode tampilan diubah menjadi ' + this.value +
    '. Data karyawan masih berupa contoh.';
});

document.getElementById('searchInput').addEventListener('input', function() {
  const keyword = this.value.toLowerCase();

  payrollTable.querySelectorAll('tr').forEach(function(row) {
    row.style.display = row.textContent.toLowerCase().includes(keyword)
      ? ''
      : 'none';
  });
});

processButton.addEventListener('click', function() {
  notice.textContent =
    'Tampilan contoh saja: proses payroll belum terhubung ke database. ' +
    'Belum ada gaji yang disimpan atau dibayarkan.';
});

document.getElementById('resetButton').addEventListener('click', function() {
  payrollTable.innerHTML = originalRows;
  document.getElementById('searchInput').value = '';
  periodSelect.value = 'Juni 2026';
  tableTitle.textContent = 'Rincian Payroll — Juni 2026';
  periodStatus.textContent = 'Draft';
  notice.textContent =
    'Data berikut adalah contoh tampilan payroll, belum tersimpan ke database.';
});
</script>

</body>
</html>
