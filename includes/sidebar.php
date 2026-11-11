<?php
// File: includes/sidebar.php
$base_path   = $base_path ?? '../';
$active_page = $active_page ?? '';
$user_nama   = $_SESSION['nama'] ?? 'User Konveksi';
$user_role   = $_SESSION['role'] ?? 'Staff';
$inisial     = strtoupper(substr($user_nama, 0, 2));

?>

<aside class="sidebar">
    <!-- Logo Area -->
    <div class="logo">
        <i class="fa-solid fa-shirt"></i>
        <div>Konveksi<span>Ku</span></div>
    </div>

    <!-- 1. MENU UTAMA -->
    <div class="nav-category">Menu Utama</div>
    
    <a href="<?= $base_path ?>index.php" class="nav-link <?= $active_page === 'dashboard' ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
    </a>
    
    <a href="<?= $base_path ?>pelanggan/index.php" class="nav-link <?= $active_page === 'pelanggan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-tie"></i>
        <span>Pelanggan</span>
    </a>
    
    <a href="<?= $base_path ?>produk/index.php" class="nav-link <?= $active_page === 'produk' ? 'active' : ''; ?>">
        <i class="fa-solid fa-box"></i>
        <span>Produk</span>
    </a>
    
    <a href="<?= $base_path ?>pesanan/index.php" class="nav-link <?= $active_page === 'pesanan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Pesanan</span>
        <?php if (!empty($notif_pesanan)): ?>
            <span class="badge"><?= $notif_pesanan ?></span>
        <?php endif; ?>
    </a>

    <!-- 2. OPERASIONAL -->
    <div class="nav-category">Operasional</div>
    
    <a href="<?= $base_path ?>produksi/index.php" class="nav-link <?= $active_page === 'produksi' ? 'active' : ''; ?>">
        <i class="fa-solid fa-industry"></i>
        <span>Produksi</span>
        <?php if (!empty($notif_produksi)): ?>
            <span class="badge"><?= $notif_produksi ?></span>
        <?php endif; ?>
    </a>
    
    <a href="<?= $base_path ?>gudang/index.php" class="nav-link <?= $active_page === 'gudang' ? 'active' : ''; ?>">
        <i class="fa-solid fa-warehouse"></i>
        <span>Gudang & Stok</span>
    </a>
    
    <!-- Dipisah: Karyawan -->
    <a href="<?= $base_path ?>karyawan/index.php" class="nav-link <?= $active_page === 'karyawan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-users"></i>
        <span>Karyawan</span>
    </a>

    <!-- Dipisah: Absensi -->
    <a href="<?= $base_path ?>absensi/index.php" class="nav-link <?= $active_page === 'absensi' ? 'active' : ''; ?>">
        <i class="fa-solid fa-clock"></i>
        <span>Absensi</span>
    </a>
    
    <a href="<?= $base_path ?>payroll/index.php" class="nav-link <?= $active_page === 'payroll' ? 'active' : ''; ?>">
        <i class="fa-solid fa-money-bill-wave"></i>
        <span>Payroll</span>
    </a>

    <!-- 3. KEUANGAN -->
    <div class="nav-category">Keuangan</div>
    
    <a href="<?= $base_path ?>keuangan/index.php" class="nav-link <?= $active_page === 'keuangan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-wallet"></i>
        <span>Pemasukan & Pengeluaran</span>
    </a>
    
    <a href="<?= $base_path ?>laporan/index.php" class="nav-link <?= $active_page === 'laporan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan</span>
    </a>

    <!-- 4. PENGATURAN -->
    <div class="nav-category">Pengaturan</div>
    
    <a href="<?= $base_path ?>pengaturan/index.php" class="nav-link <?= $active_page === 'pengaturan' ? 'active' : ''; ?>">
        <i class="fa-solid fa-gear"></i>
        <span>Pengaturan</span>
    </a>
    
    <a href="<?= $base_path ?>audit_log/index.php" class="nav-link <?= $active_page === 'audit_log' ? 'active' : ''; ?>">
        <i class="fa-solid fa-history"></i>
        <span>Audit Log</span>
    </a>

    <!-- User Profile Footer -->
    <div class="sidebar-footer">
        <div class="avatar"><?= $inisial ?></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($user_nama); ?></div>
            <div class="user-role"><?= htmlspecialchars($user_role); ?></div>
        </div>
        <a href="<?= $base_path ?>logout.php" class="logout-btn" title="Keluar">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</aside>