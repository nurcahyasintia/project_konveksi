<?php
require_once "../config/database.php";

$query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$total_produk = $query ? mysqli_num_rows($query) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--main-bg);
            color: var(--text-dark);
        }

        a {
            text-decoration: none;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            min-width: 250px;
            background: var(--sidebar-bg);
            color: var(--white);
            padding: 25px 16px 18px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            margin-bottom: 38px;
            font-size: 23px;
            font-weight: 800;
            color: var(--white);
        }

        .logo i {
            color: var(--primary-blue);
            font-size: 25px;
        }

        .logo span {
            color: #60A5FA;
        }

        .nav-title {
            color: var(--text-light);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 0 13px;
            margin-bottom: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 7px;
            color: #CBD5E1;
            font-size: 14px;
            font-weight: 500;
            border-radius: 9px;
            transition: .2s;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .nav-link:hover {
            background: #334155;
            color: var(--white);
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(59, 130, 246, .2);
        }

        /* PROFIL SIDEBAR */
        .sidebar-footer {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-top: auto;
            padding: 20px 8px 0;
            border-top: 1px solid #334155;
            min-width: 0;
        }

        .avatar {
            width: 42px;
            height: 42px;
            min-width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary-blue);
            color: var(--white);
            font-size: 14px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .user-info {
            min-width: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--white);
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .user-role {
            color: var(--text-light);
            font-size: 11px;
            margin-top: 3px;
        }

        /* KONTEN */
        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 90px;
            padding: 20px 32px;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .topbar h1 {
            font-size: 23px;
            color: var(--text-dark);
            margin-bottom: 7px;
        }

        .breadcrumb {
            color: var(--text-light);
            font-size: 12px;
        }

        .breadcrumb span {
            color: var(--primary-blue);
        }

        .topbar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #EFF6FF;
            border-radius: 9px;
            color: var(--primary-blue);
            font-size: 12px;
            font-weight: 700;
        }

        .content {
            padding: 30px 32px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h2 {
            color: var(--text-dark);
            font-size: 23px;
            margin-bottom: 8px;
        }

        .welcome p {
            color: var(--text-light);
            font-size: 13px;
            line-height: 1.8;
        }

        /* RINGKASAN PRODUK */
        .summary-card {
            background: var(--white);
            padding: 22px;
            border-radius: 13px;
            border: 1px solid var(--border-color);
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
        }

        .summary-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #DBEAFE;
            color: var(--primary-blue);
            font-size: 22px;
        }

        .summary-info p {
            color: var(--text-light);
            font-size: 12px;
            margin-bottom: 7px;
        }

        .summary-info h3 {
            color: var(--text-dark);
            font-size: 27px;
            font-weight: 800;
        }

        /* TOMBOL */
        .page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: .2s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #1D4ED8;
        }

        .btn-secondary {
            background: #E2E8F0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #CBD5E1;
        }

        /* TABEL PRODUK */
        .table-panel {
            background: var(--white);
            padding: 24px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
            overflow-x: auto;
        }

        .table-heading {
            margin-bottom: 20px;
        }

        .table-heading h2 {
            margin-bottom: 7px;
            color: var(--text-dark);
            font-size: 18px;
        }

        .table-heading p {
            color: var(--text-light);
            font-size: 12px;
            line-height: 1.6;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .product-table th {
            padding: 14px 12px;
            text-align: left;
            background: #F1F5F9;
            color: var(--table-text);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .product-table td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--border-color);
            color: var(--table-text);
            font-size: 12px;
            vertical-align: middle;
        }

        .product-table tbody tr:hover {
            background: #F8FAFC;
        }

        .product-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-code {
            color: var(--primary-blue) !important;
            font-weight: 800;
            white-space: nowrap;
        }

        .product-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #DBEAFE;
            color: #1D4ED8;
            font-size: 11px;
            font-weight: 700;
        }

        .aksi {
            white-space: nowrap;
        }

        .aksi a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            margin-right: 4px;
            margin-bottom: 3px;
        }

        .btn-edit {
            color: #1D4ED8;
            background: #DBEAFE;
        }

        .btn-edit:hover {
            background: #BFDBFE;
        }

        .btn-delete {
            color: #B91C1C;
            background: #FEE2E2;
        }

        .btn-delete:hover {
            background: #FECACA;
        }

        .empty-data {
            padding: 35px !important;
            text-align: center;
            color: var(--text-light) !important;
            line-height: 1.9;
        }

        .empty-data i {
            font-size: 28px;
            color: #93C5FD;
        }

        /* RESPONSIF */
        @media (max-width: 800px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
            }

            .content {
                padding: 22px;
            }

            .topbar {
                padding: 18px 22px;
            }
        }

        @media (max-width: 600px) {
            .app {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 0;
                padding: 18px 14px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .sidebar-footer {
                margin-top: 15px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
                padding: 20px 16px;
            }

            .topbar-label {
                display: none;
            }

            .content {
                padding: 16px;
            }

            .table-panel {
                padding: 15px;
            }

            .page-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="app">

    <aside class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-shirt"></i>
            <div>Konveksi<span>Ku</span></div>
        </div>

        <div class="nav-title">Menu Utama</div>

        <a href="../index.php" class="nav-link">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <a href="../pelanggan/index.php" class="nav-link">
            <i class="fa-solid fa-users"></i>
            Pelanggan
        </a>

        <a href="index.php" class="nav-link active">
            <i class="fa-solid fa-box"></i>
            Produk
        </a>

        <a href="../pesanan/index.php" class="nav-link">
            <i class="fa-solid fa-clipboard-list"></i>
            Pesanan
        </a>

        <div class="sidebar-footer">
            <div class="avatar">PD</div>
            <div class="user-info">
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Produk</div>
            </div>
        </div>
    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Data Produk</h1>
                <div class="breadcrumb">
                    Beranda / <span>Produk</span>
                </div>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-circle-check"></i>
                Sistem Konveksi
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h2>Kelola Produk</h2>
                <p>
                    Lihat dan kelola informasi produk konveksi di halaman ini.
                </p>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div class="summary-info">
                    <p>Total Produk</p>
                    <h3><?= $total_produk; ?></h3>
                </div>
            </div>

            <div class="page-actions">
                <a href="tambah.php" class="btn-action btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Produk
                </a>

                <a href="../index.php" class="btn-action btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="table-panel">

                <div class="table-heading">
                    <h2>Daftar Produk</h2>
                    <p>Data produk yang tersimpan di database.</p>
                </div>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($query && $total_produk > 0) { ?>
                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td class="product-code">
                                    <?= htmlspecialchars($row['kode'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['kategori'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td>
                                    Rp <?= number_format(
                                        (float) ($row['harga_jual'] ?? 0),
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </td>

                                <td>
                                    <span class="product-status">
                                        <?= htmlspecialchars($row['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>

                                <td class="aksi">
                                    <a class="btn-edit"
                                       href="edit.php?id=<?= (int) $row['id']; ?>">
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </a>

                                    <a class="btn-delete"
                                       href="hapus.php?id=<?= (int) $row['id']; ?>"
                                       onclick="return confirm('Yakin ingin menghapus produk ini?');">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="empty-data">
                                <i class="fa-solid fa-box-open"></i>
                                <br>
                                Belum ada data produk.
                                <br>
                                Klik tombol Tambah Produk untuk menambahkan data.
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

            </div>
        </section>
    </main>

</div>
</body>
</html>