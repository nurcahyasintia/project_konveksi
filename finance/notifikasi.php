<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KonveksiKu — Notifikasi</title>

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
  --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', system-ui, sans-serif;
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
  color: #fff;
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
  letter-spacing: -.3px;
}

.logo i {
  color: var(--primary);
  background: rgba(37,99,235,.15);
  padding: 8px;
  border-radius: 10px;
  font-size: 16px;
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
  transition: all .2s;
  margin-bottom: 2px;
}

.nav-item:hover {
  background: rgba(255,255,255,.06);
  color: #fff;
}

.nav-item.active {
  background: var(--primary);
  color: #fff;
  box-shadow: 0 4px 12px rgba(37,99,235,.35);
}

.nav-item i {
  width: 20px;
  text-align: center;
  font-size: 15px;
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
  background: linear-gradient(135deg, var(--primary), var(--info));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: #fff;
}

.user-info .name {
  font-size: 13.5px;
  font-weight: 600;
  color: #fff;
}

.user-info .role {
  font-size: 11.5px;
  color: var(--gray-500);
}

/* KONTEN UTAMA */
.main {
  flex: 1;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.topbar {
  background: #fff;
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
  letter-spacing: -.3px;
}

.breadcrumb {
  font-size: 12.5px;
  color: var(--gray-500);
  margin-top: 2px;
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
  width: 280px;
}

.search-box i {
  color: var(--gray-400);
  font-size: 13px;
}

.search-box input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13.5px;
  width: 100%;
  color: var(--gray-700);
  font-family: inherit;
}

.topbar-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--gray-100);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--gray-600);
  position: relative;
}

.topbar-icon .dot {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: var(--danger);
  border-radius: 50%;
  border: 2px solid #fff;
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
  font-weight: 700;
  color: var(--primary-dark);
  font-size: 12px;
  outline: none;
  cursor: pointer;
  font-family: inherit;
}

.content {
  padding: 28px 32px;
  flex: 1;
}

/* CARD */
.card {
  background: #fff;
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
  flex-wrap: wrap;
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

/* NOTIFIKASI */
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
  transition: all .2s;
}

.notification-item:hover {
  background: var(--gray-100);
  transform: translateX(3px);
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
  font-size: 13px;
  flex-shrink: 0;
}

.notification-icon.blue {
  background: var(--primary-light);
  color: var(--primary);
}

.notification-icon.orange {
  background: #fef3c7;
  color: var(--warning);
}

.notification-icon.red {
  background: #fee2e2;
  color: var(--danger);
}

.notification-icon.green {
  background: #d1fae5;
  color: var(--success);
}

.notification-content .title {
  font-size: 13px;
  font-weight: 700;
  color: var(--gray-800);
}

.notification-content .desc {
  font-size: 11.5px;
  color: var(--gray-500);
}

@media (max-width: 768px) {
  .sidebar {
    width: 0;
    overflow: hidden;
  }

  .main {
    margin-left: 0;
  }

  .search-box {
    display: none;
  }

  .content {
    padding: 20px 16px;
  }

  .topbar {
    padding: 14px 16px;
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

      <a class="nav-item" href="dashboard.php">
        <i class="fas fa-th-large"></i> Dashboard
      </a>

      <a class="nav-item" href="payroll.php">
        <i class="fas fa-money-bill-wave"></i> Payroll
      </a>

      <a class="nav-item" href="keuangan.php">
        <i class="fas fa-wallet"></i> Keuangan
      </a>

      <a class="nav-item" href="laporan.php">
        <i class="fas fa-file-invoice"></i> Laporan
      </a>

      <a class="nav-item active" href="notifikasi.php">
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

  <!-- KONTEN -->
  <main class="main">

    <header class="topbar">
      <div class="topbar-left">
        <h1>Notifikasi</h1>
        <div class="breadcrumb">Home / Notifikasi</div>
      </div>

      <div class="topbar-right">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" id="searchNotification" placeholder="Cari notifikasi...">
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

        <div class="topbar-icon">
          <i class="fas fa-bell"></i>
          <span class="dot"></span>
        </div>
      </div>
    </header>

    <div class="content">
      <div class="card">

        <div class="card-header">
          <h3>Notifikasi</h3>
          <a class="card-action" href="#" id="markAllRead">
            Tandai Semua Dibaca
          </a>
        </div>

        <div class="card-body">
          <div class="notification-list" id="notificationList">

            <div class="notification-item danger">
              <div class="notification-icon red">
                <i class="fas fa-box"></i>
              </div>
              <div class="notification-content">
                <div class="title">Stok Minimum</div>
                <div class="desc">Kain Katun Putih — sisa 15 m (min. 50 m)</div>
              </div>
            </div>

            <div class="notification-item danger">
              <div class="notification-icon red">
                <i class="fas fa-box"></i>
              </div>
              <div class="notification-content">
                <div class="title">Stok Minimum</div>
                <div class="desc">Kancing Baju — sisa 8 lusin (min. 20 lusin)</div>
              </div>
            </div>

            <div class="notification-item warning">
              <div class="notification-icon orange">
                <i class="fas fa-clock"></i>
              </div>
              <div class="notification-content">
                <div class="title">Deadline Order</div>
                <div class="desc">#ORD-024 — CV Maju Jaya — 2 hari lagi</div>
              </div>
            </div>

            <div class="notification-item">
              <div class="notification-icon blue">
                <i class="fas fa-industry"></i>
              </div>
              <div class="notification-content">
                <div class="title">Produksi Tertunda</div>
                <div class="desc">Job #JOB-012 melewati target</div>
              </div>
            </div>

            <div class="notification-item success">
              <div class="notification-icon green">
                <i class="fas fa-money-bill"></i>
              </div>
              <div class="notification-content">
                <div class="title">Jadwal Gaji</div>
                <div class="desc">Payroll Juni 2026 siap diproses</div>
              </div>
            </div>

            <div class="notification-item warning">
              <div class="notification-icon orange">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="notification-content">
                <div class="title">Transaksi Perlu Persetujuan</div>
                <div class="desc">Pengeluaran Rp 3.500.000 — Pembelian Kain Katun</div>
              </div>
            </div>

          </div>
          <p id="emptyMessage" style="display:none;color:#64748b;font-size:13px;margin-top:16px">
            Notifikasi tidak ditemukan.
          </p>
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

// Ganti nama dan jabatan sesuai role yang dipilih
document.getElementById('roleSwitcher').addEventListener('change', function () {
  const user = roleConfig[this.value];

  document.getElementById('userName').textContent = user.name;
  document.getElementById('userRole').textContent = user.role;
  document.getElementById('userAvatar').textContent = user.avatar;
});

// Pencarian notifikasi
document.getElementById('searchNotification').addEventListener('input', function () {
  const keyword = this.value.toLowerCase();
  const items = document.querySelectorAll('.notification-item');
  let visibleCount = 0;

  items.forEach(function (item) {
    const text = item.textContent.toLowerCase();
    const match = text.includes(keyword);

    item.style.display = match ? 'flex' : 'none';

    if (match) visibleCount++;
  });

  document.getElementById('emptyMessage').style.display =
    visibleCount === 0 ? 'block' : 'none';
});

// Tandai semua notifikasi sebagai dibaca
document.getElementById('markAllRead').addEventListener('click', function (event) {
  event.preventDefault();

  document.querySelectorAll('.notification-item').forEach(function (item) {
    item.style.borderLeftColor = '#cbd5e1';
    item.style.background = '#ffffff';
  });

  document.querySelector('.topbar-icon .dot').style.display = 'none';

  this.textContent = 'Semua Sudah Dibaca';
});
</script>

</body>
</html>
