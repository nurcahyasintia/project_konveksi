<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KonveksiKu — Laporan</title>

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
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,.08);
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
    -webkit-font-smoothing: antialiased;
}

.app {
    display: flex;
    min-height: 100vh;
}

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

.nav-section {
    margin-bottom: 14px;
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
    cursor: pointer;
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
    background: linear-gradient(135deg,var(--primary),var(--info));
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

.topbar-left .breadcrumb {
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

.search-box:focus-within {
    background: #fff;
    box-shadow: 0 0 0 3px var(--primary-light);
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

.filter-bar {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.filter-bar .form-input,
.filter-bar .form-select {
    padding: 8px 12px;
    font-size: 13px;
}

.filter-bar .spacer {
    flex: 1;
}

.form-input,
.form-select {
    padding: 10px 14px;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 13.5px;
    font-family: inherit;
    color: var(--gray-800);
    transition: all .2s;
    background: #fff;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-light);
}

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
    letter-spacing: -.2px;
}

.card-body {
    padding: 22px;
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
    font-size: 13.5px;
    color: var(--gray-700);
    border-bottom: 1px solid var(--gray-100);
}

tbody tr {
    transition: background .15s;
}

tbody tr:hover {
    background: var(--gray-50);
}

tbody tr:last-child td {
    border-bottom: none;
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

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #cffafe;
    color: #155e75;
}

.badge-primary {
    background: var(--primary-light);
    color: var(--primary-dark);
}

.badge-gray {
    background: var(--gray-200);
    color: var(--gray-600);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--radius-sm);
    font-size: 13.5px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    font-family: inherit;
}

.btn-primary {
    background: var(--primary);
    color: #fff;
}

.btn-primary:hover {
    background: var(--primary-dark);
    box-shadow: 0 6px 16px rgba(37,99,235,.35);
}

.btn-outline {
    background: #fff;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-outline:hover {
    background: var(--gray-50);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12.5px;
}

.grid-3 {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 22px;
    margin-bottom: 26px;
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

.stat-icon.blue {
    background: var(--primary-light);
    color: var(--primary);
}

.stat-icon.green {
    background: #d1fae5;
    color: var(--success);
}

.stat-icon.orange {
    background: #fef3c7;
    color: var(--warning);
}

.stat-icon.purple {
    background: #ede9fe;
    color: var(--purple);
}

.stat-icon.cyan {
    background: #cffafe;
    color: var(--info);
}

@media (max-width: 1200px) {
    .grid-3 {
        grid-template-columns: repeat(2,1fr);
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

    .grid-3 {
        grid-template-columns: 1fr;
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

@media print {
    .sidebar,
    .topbar,
    .filter-bar,
    .card button {
        display: none !important;
    }

    .main {
        margin-left: 0;
    }

    .content {
        padding: 0;
    }

    body {
        background: #fff;
    }
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

                <a class="nav-item" href="dashboard.php">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>

                <a class="nav-item" href="payroll.php">
                    <i class="fas fa-money-bill-wave"></i> Payroll
                </a>

                <a class="nav-item" href="keuangan.php">
                    <i class="fas fa-wallet"></i> Keuangan
                </a>

                <a class="nav-item active" href="laporan.php">
                    <i class="fas fa-file-invoice"></i> Laporan
                </a>

                <a class="nav-item" href="notifikasi.php">
                    <i class="fas fa-bell"></i> Notifikasi
                </a>
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
                <h1>Laporan</h1>
                <div class="breadcrumb">Home / Laporan</div>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari laporan...">
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

            <!-- FILTER LAPORAN: DIAMBIL DARI KODE ASLI -->
            <div class="filter-bar">
                <select class="form-select" id="periode">
                    <option value="Juni 2026">Juni 2026</option>
                    <option value="Mei 2026">Mei 2026</option>
                </select>

                <select class="form-select" id="jenisLaporan">
                    <option value="Semua">Semua Laporan</option>
                    <option value="Order">Laporan Order</option>
                    <option value="Produksi">Laporan Produksi</option>
                    <option value="Stok">Laporan Stok</option>
                    <option value="Pemasukan">Laporan Pemasukan</option>
                    <option value="Pengeluaran">Laporan Pengeluaran</option>
                    <option value="Payroll">Laporan Payroll</option>
                    <option value="Absensi">Laporan Absensi</option>
                    <option value="Laba/Rugi">Laporan Laba/Rugi</option>
                </select>

                <div class="spacer"></div>

                <button class="btn btn-outline" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>

                <button class="btn btn-outline" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Excel
                </button>

                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>

            <!-- LAPORAN LABA RUGI: ISI ASLI -->
            <div class="card" id="laporanLabaRugi">
                <div class="card-header">
                    <h3>📊 Laporan Laba / Rugi Sederhana — Juni 2026</h3>
                </div>

                <div class="card-body">
                    <table>
                        <tbody>
                            <tr>
                                <td><strong>PENDAPATAN</strong></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px">Penjualan Produk</td>
                                <td></td>
                                <td style="text-align:right"><strong>Rp 84.500.000</strong></td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px"><strong>Total Pendapatan</strong></td>
                                <td></td>
                                <td style="text-align:right">
                                    <strong style="color:var(--success)">Rp 84.500.000</strong>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3" style="padding:8px 0">
                                    <hr style="border:none;border-top:1px dashed var(--gray-300)">
                                </td>
                            </tr>

                            <tr>
                                <td><strong>BIAYA &amp; PENGELUARAN</strong></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px">Pembelian Bahan Baku</td>
                                <td></td>
                                <td style="text-align:right">Rp 15.200.000</td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px">Gaji Karyawan</td>
                                <td></td>
                                <td style="text-align:right">Rp 65.000.000</td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px">Biaya Operasional</td>
                                <td></td>
                                <td style="text-align:right">Rp 4.500.000</td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px">Lain-lain</td>
                                <td></td>
                                <td style="text-align:right">Rp 1.200.000</td>
                            </tr>

                            <tr>
                                <td style="padding-left:32px"><strong>Total Pengeluaran</strong></td>
                                <td></td>
                                <td style="text-align:right">
                                    <strong style="color:var(--danger)">Rp 85.900.000</strong>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3" style="padding:8px 0">
                                    <hr style="border:none;border-top:2px solid var(--gray-300)">
                                </td>
                            </tr>

                            <tr style="background:var(--gray-50)">
                                <td><strong>LABA / RUGI BERSIH</strong></td>
                                <td></td>
                                <td style="text-align:right">
                                    <strong style="color:var(--danger);font-size:16px">
                                        - Rp 1.400.000
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- KARTU LAPORAN: DIAMBIL DARI KODE ASLI -->
            <div class="grid-3" style="margin-top:22px">

                <div class="card report-card" data-jenis="Order">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon green" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Order</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">148 order bulan ini</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Order')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Produksi">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon orange" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-industry"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Produksi</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">23 job berjalan</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Produksi')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Stok">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon blue" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Stok</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">24 item gudang</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Stok')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Payroll">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon green" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-money-bill"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Payroll</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">45 karyawan</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Payroll')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Absensi">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon purple" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Absensi</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">93% kehadiran</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Absensi')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Laba/Rugi">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon cyan" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laba / Rugi</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">Laporan keuangan</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Laba/Rugi')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Pemasukan">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon green" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Pemasukan</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">Ringkasan transaksi masuk</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Pemasukan')">Lihat</button>
                    </div>
                </div>

                <div class="card report-card" data-jenis="Pengeluaran">
                    <div class="card-body" style="text-align:center">
                        <div class="stat-icon orange" style="margin:0 auto 12px;width:56px;height:56px;font-size:22px">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--gray-800)">Laporan Pengeluaran</div>
                        <div style="font-size:12px;color:var(--gray-500);margin-bottom:12px">Ringkasan transaksi keluar</div>
                        <button class="btn btn-outline btn-sm" onclick="lihatLaporan('Pengeluaran')">Lihat</button>
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

roleSwitcher.addEventListener('change', function() {
    const role = roleConfig[this.value];

    document.getElementById('userName').textContent = role.name;
    document.getElementById('userRole').textContent = role.role;
    document.getElementById('userAvatar').textContent = role.avatar;
});

function lihatLaporan(jenis) {
    alert(
        'Laporan ' + jenis +
        '\\nPeriode: ' + document.getElementById('periode').value +
        '\\n\\nData pada halaman ini masih berupa contoh dari desain asli.'
    );
}

document.getElementById('jenisLaporan').addEventListener('change', function() {
    const jenis = this.value;
    const kartu = document.querySelectorAll('.report-card');
    const laporanLabaRugi = document.getElementById('laporanLabaRugi');

    kartu.forEach(function(card) {
        card.style.display =
            jenis === 'Semua' || card.dataset.jenis === jenis
                ? ''
                : 'none';
    });

    laporanLabaRugi.style.display =
        jenis === 'Semua' || jenis === 'Laba/Rugi'
            ? ''
            : 'none';
});

document.getElementById('periode').addEventListener('change', function() {
    document.querySelector('#laporanLabaRugi .card-header h3').textContent =
        '📊 Laporan Laba / Rugi Sederhana — ' + this.value;
});

document.getElementById('searchInput').addEventListener('input', function() {
    const kata = this.value.toLowerCase();

    document.querySelectorAll('.report-card').forEach(function(card) {
        card.style.display = card.textContent.toLowerCase().includes(kata)
            ? ''
            : 'none';
    });
});

function exportPDF() {
    alert('Untuk membuat PDF, klik Cetak lalu pilih "Save as PDF" pada tujuan printer.');
}

function exportExcel() {
    const rows = [
        ['Kategori', 'Keterangan', 'Nominal'],
        ['Pendapatan', 'Penjualan Produk', '84500000'],
        ['Pengeluaran', 'Pembelian Bahan Baku', '15200000'],
        ['Pengeluaran', 'Gaji Karyawan', '65000000'],
        ['Pengeluaran', 'Biaya Operasional', '4500000'],
        ['Pengeluaran', 'Lain-lain', '1200000'],
        ['Laba/Rugi', 'Laba/Rugi Bersih', '-1400000']
    ];

    const csv = rows.map(row =>
        row.map(value => '"' + value.replace(/"/g, '""') + '"').join(',')
    ).join('\\r\\n');

    const blob = new Blob(['\\uFEFF' + csv], {
        type: 'text/csv;charset=utf-8;'
    });

    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'laporan_keuangan.csv';
    link.click();
    URL.revokeObjectURL(link.href);
}
</script>
</body>
</html>