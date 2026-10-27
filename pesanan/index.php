<?php
require_once "../config/database.php";

$query = mysqli_query($conn, "
    SELECT
        orders.*,
        customers.nama AS nama_pelanggan
    FROM orders
    LEFT JOIN customers ON orders.customer_id = customers.id
    ORDER BY orders.id DESC
");

if (!$query) {
    die("Gagal mengambil data pesanan: " . mysqli_error($conn));
}

$total_pesanan = mysqli_num_rows($query);

function amanPesanan($nilai) {
    return htmlspecialchars((string) ($nilai ?? "-"), ENT_QUOTES, "UTF-8");
}

function labelStatusPembayaranPesanan($status) {
    $label = [
        "belum_lunas" => "Belum Lunas",
        "dp" => "DP",
        "lunas" => "Lunas"
    ];

    return $label[$status] ?? ucfirst(str_replace("_", " ", (string) $status));
}

function labelStatusProduksiPesanan($status) {
    $label = [
        "menunggu" => "Menunggu",
        "proses" => "Sedang Diproses",
        "selesai" => "Selesai",
        "dikirim" => "Dikirim"
    ];

    return $label[$status] ?? ucfirst(str_replace("_", " ", (string) $status));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
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
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
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
            border-top: 1px solid #334155;
            margin-top: auto;
            padding: 20px 8px 0;
            min-width: 0;
        }

        .avatar {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;
            background: var(--primary-blue);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* KONTEN UTAMA */
        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: var(--white);
            min-height: 90px;
            padding: 20px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .topbar h1 {
            color: var(--text-dark);
            font-size: 23px;
            margin-bottom: 7px;
        }

        .breadcrumb {
            font-size: 12px;
            color: var(--text-light);
        }

        .breadcrumb span {
            color: var(--primary-blue);
        }

        .topbar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-blue);
            background: #EFF6FF;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .content {
            padding: 30px 32px;
        }

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-heading h2 {
            color: var(--text-dark);
            font-size: 22px;
            margin-bottom: 8px;
        }

        .page-heading p {
            font-size: 12px;
            color: var(--text-light);
            line-height: 1.7;
        }

        /* RINGKASAN */
        .summary-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 13px;
            padding: 22px;
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
            border-radius: 13px;
            background: #DBEAFE;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .summary-card p {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 7px;
        }

        .summary-card h3 {
            color: var(--text-dark);
            font-size: 27px;
            font-weight: 800;
        }

        /* TOMBOL */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 15px;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
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

        /* TABEL */
        .panel {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
        }

        .panel-heading {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            font-size: 17px;
            font-weight: 700;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        .data-table th,
        .data-table td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            font-size: 12px;
            white-space: nowrap;
        }

        .data-table th {
            background: #F1F5F9;
            color: var(--table-text);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-size: 11px;
        }

        .data-table td {
            color: var(--table-text);
        }

        .data-table tbody tr:hover td {
            background: #F8FAFC;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-number {
            color: var(--primary-blue) !important;
            font-weight: 800;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-belum-lunas,
        .status-menunggu {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-dp,
        .status-proses {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .status-lunas,
        .status-selesai {
            background: #DCFCE7;
            color: #166534;
        }

        .status-dikirim {
            background: #E2E8F0;
            color: #475569;
        }

        .action-buttons {
            display: flex;
            gap: 7px;
        }

        .btn-edit,
        .btn-delete {
            width: 33px;
            height: 33px;
            min-width: 33px;
            padding: 0;
            border-radius: 7px;
        }

        .btn-edit {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .btn-edit:hover {
            background: #BFDBFE;
        }

        .btn-delete {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-delete:hover {
            background: #FECACA;
        }

        .empty-data {
            text-align: center !important;
            padding: 35px !important;
            color: var(--text-light) !important;
            line-height: 1.8;
        }

        .empty-data i {
            font-size: 28px;
            color: #93C5FD;
        }

        .back-button {
            margin-top: 22px;
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
                padding: 20px 10px;
            }

            .topbar,
            .content {
                padding: 22px;
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
                margin-top: 18px;
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
                padding: 18px 14px;
            }

            .page-heading {
                align-items: flex-start;
            }

            .panel-heading {
                padding: 18px;
            }

            .back-button .btn {
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

        <a href="../produk/index.php" class="nav-link">
            <i class="fa-solid fa-box"></i>
            Produk
        </a>

        <a href="index.php" class="nav-link active">
            <i class="fa-solid fa-clipboard-list"></i>
            Pesanan
        </a>

        <div class="sidebar-footer">
            <div class="avatar">PS</div>
            <div class="user-info">
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Pesanan</div>
            </div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Data Pesanan</h1>
                <div class="breadcrumb">
                    Dashboard / <span>Pesanan</span>
                </div>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-circle-check"></i>
                Sistem Konveksi
            </div>
        </header>

        <section class="content">

            <div class="page-heading">
                <div>
                    <h2>Data Pesanan</h2>
                    <p>Kelola semua pesanan pelanggan konveksi.</p>
                </div>

                <a href="tambah.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Pesanan
                </a>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <p>Total Pesanan</p>
                    <h3><?= $total_pesanan; ?></h3>
                </div>
            </div>

            <div class="panel">
                <div class="panel-heading">
                    <i class="fa-solid fa-list"></i>
                    Daftar Pesanan
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nomor Pesanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Deadline</th>
                                <th>Total</th>
                                <th>DP</th>
                                <th>Status Pembayaran</th>
                                <th>Status Produksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($total_pesanan > 0): ?>
                            <?php $no = 1; ?>

                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <?php
                                $status_pembayaran = $row['status_pembayaran'] ?? 'belum_lunas';
                                $status_produksi = $row['status_produksi'] ?? 'menunggu';

                                $kelas_pembayaran = str_replace('_', '-', $status_pembayaran);
                                ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td class="order-number">
                                        <?= amanPesanan($row['nomor_order']); ?>
                                    </td>

                                    <td>
                                        <?= amanPesanan($row['nama_pelanggan']); ?>
                                    </td>

                                    <td><?= amanPesanan($row['tanggal']); ?></td>

                                    <td><?= amanPesanan($row['deadline']); ?></td>

                                    <td>
                                        Rp <?= number_format(
                                            (float) ($row['total'] ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td>
                                        Rp <?= number_format(
                                            (float) ($row['dp'] ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="status status-<?= amanPesanan($kelas_pembayaran); ?>">
                                            <?= amanPesanan(labelStatusPembayaranPesanan($status_pembayaran)); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="status status-<?= amanPesanan($status_produksi); ?>">
                                            <?= amanPesanan(labelStatusProduksiPesanan($status_produksi)); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="action-buttons">
                                            <a
                                                href="edit.php?id=<?= (int) $row['id']; ?>"
                                                class="btn btn-edit"
                                                title="Edit pesanan"
                                                aria-label="Edit pesanan">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <a
                                                href="hapus.php?id=<?= (int) $row['id']; ?>"
                                                class="btn btn-delete"
                                                title="Hapus pesanan"
                                                aria-label="Hapus pesanan"
                                                onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="empty-data">
                                    <i class="fa-solid fa-inbox"></i>
                                    <p style="margin-top: 10px;">
                                        Belum ada data pesanan.
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="back-button">
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>

        </section>
    </main>
</div>
</body>
</html>