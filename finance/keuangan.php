<?php
// ============================================
// KONVEKSIKU — ANGGOTA 5
// HALAMAN KEUANGAN
// ============================================

// Data contoh dari desain DeepSeek.
// Belum terhubung ke database MySQL.
$totalPemasukan = 84500000;
$totalPengeluaran = 42300000;
$saldo = $totalPemasukan - $totalPengeluaran;

$pemasukan = [
    [
        'tanggal' => '22 Jun 2026',
        'kategori' => 'Pembayaran Pelanggan',
        'sumber' => '#ORD-024 DP',
        'nominal' => 5000000
    ],
    [
        'tanggal' => '21 Jun 2026',
        'kategori' => 'Pembayaran Pelanggan',
        'sumber' => '#ORD-023 Pelunasan',
        'nominal' => 9000000
    ],
    [
        'tanggal' => '20 Jun 2026',
        'kategori' => 'Pembayaran Pelanggan',
        'sumber' => '#ORD-021 DP',
        'nominal' => 4800000
    ],
    [
        'tanggal' => '18 Jun 2026',
        'kategori' => 'Pembayaran Pelanggan',
        'sumber' => '#ORD-020 Pelunasan',
        'nominal' => 2400000
    ],
];

$pengeluaran = [
    [
        'tanggal' => '22 Jun 2026',
        'kategori' => 'Pembelian Bahan',
        'tujuan' => 'Kain Katun',
        'nominal' => 3500000
    ],
    [
        'tanggal' => '21 Jun 2026',
        'kategori' => 'Biaya Operasional',
        'tujuan' => 'Listrik & Air',
        'nominal' => 1200000
    ],
    [
        'tanggal' => '20 Jun 2026',
        'kategori' => 'Pembelian Bahan',
        'tujuan' => 'Benang & Kancing',
        'nominal' => 850000
    ],
    [
        'tanggal' => '19 Jun 2026',
        'kategori' => 'Pembayaran Gaji',
        'tujuan' => 'Payroll Mei 2026',
        'nominal' => 65000000
    ],
];

function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keuangan - KonveksiKu</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #dbeafe;
    --success: #10b981;
    --danger: #ef4444;
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
    --shadow: 0 1px 3px rgba(0,0,0,.08),
              0 1px 2px rgba(0,0,0,.04);
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
    margin-bottom: 2px;
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
    background: linear-gradient(135deg, var(--primary), #06b6d4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
}

.user-info .name {
    font-size: 13.5px;
    font-weight: 600;
    color: white;
}

.user-info .role {
    font-size: 11.5px;
    color: var(--gray-400);
}

/* MAIN DAN TOPBAR */
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
    margin-top: 2px;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 14px;
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
}

/* KONTEN */
.content {
    padding: 28px 32px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 26px;
}

.stat-card {
    background: white;
    border-radius: var(--radius);
    padding: 22px;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: .25s;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    transform: translateY(-3px);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
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

.stat-icon.green {
    background: #d1fae5;
    color: var(--success);
}

.stat-icon.red {
    background: #fee2e2;
    color: var(--danger);
}

.stat-icon.blue {
    background: var(--primary-light);
    color: var(--primary);
}

.stat-icon.purple {
    background: #ede9fe;
    color: var(--purple);
}

.stat-label {
    font-size: 12.5px;
    color: var(--gray-500);
    font-weight: 500;
    margin-bottom: 4px;
}

.stat-value {
    font-size: clamp(18px, 1.8vw, 26px);
    font-weight: 800;
    color: var(--gray-900);
    letter-spacing: -.5px;
    overflow-wrap: anywhere;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    white-space: nowrap;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

/* CARD DAN TABEL */
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
    flex-wrap: wrap;
    gap: 10px;
}

.card-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--gray-900);
}

.card-body {
    padding: 22px;
}

.grid-2-equal {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-bottom: 26px;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead th {
    text-align: left;
    padding: 12px 16px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--gray-500);
    font-weight: 700;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    white-space: nowrap;
}

tbody td {
    padding: 14px 16px;
    font-size: 13px;
    color: var(--gray-700);
    border-bottom: 1px solid var(--gray-100);
    white-space: nowrap;
}

tbody tr:hover {
    background: var(--gray-50);
}

tbody tr:last-child td {
    border-bottom: none;
}

.text-green {
    color: var(--success) !important;
}

.text-red {
    color: var(--danger) !important;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-outline {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.category-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-2-equal {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 0;
        overflow: hidden;
    }

    .main {
        margin-left: 0;
    }

    .stats-grid {
        grid-template-columns: 1fr;
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

            <a class="nav-item active" href="keuangan.php">
                <i class="fas fa-wallet"></i> Keuangan
            </a>

            <a class="nav-item" href="laporan.php">
                <i class="fas fa-file-invoice"></i> Laporan
            </a>

            <a class="nav-item" href="notifikasi.php">
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

    <!-- KONTEN UTAMA -->
    <main class="main">

        <header class="topbar">
            <div class="topbar-left">
                <h1>Keuangan</h1>
                <div class="breadcrumb">Home / Keuangan</div>
            </div>

            <div class="topbar-right">
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
                </div>
            </div>
        </header>

        <div class="content">

            <!-- KARTU STATISTIK -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <span class="badge badge-success">+12%</span>
                    </div>
                    <div class="stat-label">Total Pemasukan</div>
                    <div class="stat-value">
                        <?= formatRupiah($totalPemasukan) ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon red">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <span class="badge badge-danger">-5%</span>
                    </div>
                    <div class="stat-label">Total Pengeluaran</div>
                    <div class="stat-value">
                        <?= formatRupiah($totalPengeluaran) ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="stat-label">Saldo / Arus Kas</div>
                    <div class="stat-value">
                        <?= formatRupiah($saldo) ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon purple">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="stat-label">Laba Bersih</div>
                    <div class="stat-value">
                        <?= formatRupiah($saldo) ?>
                    </div>
                </div>
            </section>

            <!-- KATEGORI TRANSAKSI -->
            <section class="card" style="margin-bottom:22px">
                <div class="card-header">
                    <h3>Kategori Transaksi</h3>
                </div>

                <div class="card-body">
                    <div class="category-list">
                        <span class="badge badge-success">Pemasukan: Pembayaran Pelanggan</span>
                        <span class="badge badge-success">Pemasukan: Lain-lain</span>
                        <span class="badge badge-danger">Pengeluaran: Pembelian Bahan</span>
                        <span class="badge badge-danger">Pengeluaran: Biaya Operasional</span>
                        <span class="badge badge-danger">Pengeluaran: Pembayaran Gaji</span>
                    </div>
                </div>
            </section>

            <!-- TABEL PEMASUKAN DAN PENGELUARAN -->
            <section class="grid-2-equal">

                <div class="card">
                    <div class="card-header">
                        <h3>💚 Pemasukan</h3>
                        <button class="btn btn-success btn-sm" onclick="pesanFitur('pemasukan')">
                            <i class="fas fa-plus"></i> Catat
                        </button>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Sumber</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pemasukan as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($item['kategori']) ?></td>
                                    <td><?= htmlspecialchars($item['sumber']) ?></td>
                                    <td>
                                        <strong class="text-green">
                                            <?= formatRupiah($item['nominal']) ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>❤️ Pengeluaran</h3>
                        <button class="btn btn-outline btn-sm" onclick="pesanFitur('pengeluaran')">
                            <i class="fas fa-plus"></i> Catat
                        </button>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Tujuan</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pengeluaran as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($item['kategori']) ?></td>
                                    <td><?= htmlspecialchars($item['tujuan']) ?></td>
                                    <td>
                                        <strong class="text-red">
                                            - <?= formatRupiah($item['nominal']) ?>
                                        </strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

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

document.getElementById('roleSwitcher').addEventListener('change', function () {
    const role = roleConfig[this.value];

    document.getElementById('userName').textContent = role.name;
    document.getElementById('userRole').textContent = role.role;
    document.getElementById('userAvatar').textContent = role.avatar;
});

function pesanFitur(jenis) {
    alert(
        'Fitur Catat ' + jenis +
        ' belum dihubungkan ke database MySQL.'
    );
}
</script>

</body>
</html>