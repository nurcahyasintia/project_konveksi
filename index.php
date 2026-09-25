<?php
// dashboard.php
session_start();

// Jika ingin menggunakan login, bagian ini bisa diaktifkan
// if (!isset($_SESSION['user'])) {
//     header("Location: login.php");
//     exit;
// }

// Contoh data sementara
$totalProduk = 25;
$totalKategori = 8;
$totalTransaksi = 120;
$totalPendapatan = 15750000;

// Data transaksi terbaru
$transaksi = [
    [
        "id" => "TRX001",
        "tanggal" => "25-09-2026",
        "pelanggan" => "Andi",
        "total" => 75000,
        "status" => "Selesai"
    ],
    [
        "id" => "TRX002",
        "tanggal" => "25-09-2026",
        "pelanggan" => "Siti",
        "total" => 125000,
        "status" => "Selesai"
    ],
    [
        "id" => "TRX003",
        "tanggal" => "24-09-2026",
        "pelanggan" => "Budi",
        "total" => 50000,
        "status" => "Pending"
    ],
    [
        "id" => "TRX004",
        "tanggal" => "24-09-2026",
        "pelanggan" => "Rina",
        "total" => 95000,
        "status" => "Selesai"
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Kasir UMKM</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            background-color: #f5f6fa;
            font-family: Arial, sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #4f46e5, #6366f1);
            color: white;
            padding: 20px;
        }

        .sidebar .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar i {
            width: 25px;
        }

        /* CONTENT */
        .content {
            margin-left: 250px;
            padding: 30px;
        }

        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h4 {
            margin: 0;
            font-weight: bold;
        }

        /* CARD STATISTIK */
        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 20px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }

        .blue {
            background: #4f46e5;
        }

        .green {
            background: #16a34a;
        }

        .orange {
            background: #f59e0b;
        }

        .red {
            background: #dc2626;
        }

        .stat-title {
            color: #777;
            font-size: 14px;
            margin-top: 15px;
        }

        .stat-number {
            font-size: 25px;
            font-weight: bold;
        }

        /* TABLE */
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .table-card h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .badge-selesai {
            background: #dcfce7;
            color: #15803d;
            padding: 6px 10px;
            border-radius: 20px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
            padding: 6px 10px;
            border-radius: 20px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 15px 10px;
            }

            .sidebar .logo span,
            .sidebar a span {
                display: none;
            }

            .sidebar a {
                text-align: center;
            }

            .content {
                margin-left: 70px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-store"></i>
        <span>Kasir UMKM</span>
    </div>

    <a href="dashboard.php" class="active">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
    </a>

    <a href="produk.php">
        <i class="fa-solid fa-box"></i>
        <span>Produk</span>
    </a>

    <a href="kategori.php">
        <i class="fa-solid fa-layer-group"></i>
        <span>Kategori</span>
    </a>

    <a href="transaksi.php">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Transaksi</span>
    </a>

    <a href="laporan.php">
        <i class="fa-solid fa-file-lines"></i>
        <span>Laporan</span>
    </a>

    <a href="logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>

</div>


<!-- CONTENT -->
<div class="content">

    <!-- TOPBAR -->
    <div class="topbar">

        <div>
            <h4>Dashboard</h4>
            <small class="text-muted">
                Selamat datang di Sistem Kasir UMKM
            </small>
        </div>

        <div>
            <i class="fa-solid fa-user-circle fa-2x text-primary"></i>
        </div>

    </div>


    <!-- STATISTIK -->
    <div class="row g-4">

        <!-- PRODUK -->
        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="stat-title">
                            Total Produk
                        </div>

                        <div class="stat-number">
                            <?= $totalProduk ?>
                        </div>
                    </div>

                    <div class="icon-box blue">
                        <i class="fa-solid fa-box"></i>
                    </div>

                </div>

            </div>

        </div>


        <!-- KATEGORI -->
        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="stat-title">
                            Total Kategori
                        </div>

                        <div class="stat-number">
                            <?= $totalKategori ?>
                        </div>
                    </div>

                    <div class="icon-box green">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>

                </div>

            </div>

        </div>


        <!-- TRANSAKSI -->
        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="stat-title">
                            Total Transaksi
                        </div>

                        <div class="stat-number">
                            <?= $totalTransaksi ?>
                        </div>
                    </div>

                    <div class="icon-box orange">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>

                </div>

            </div>

        </div>


        <!-- PENDAPATAN -->
        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <div class="stat-title">
                            Pendapatan
                        </div>

                        <div class="stat-number">
                            Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                        </div>
                    </div>

                    <div class="icon-box red">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- TRANSAKSI TERBARU -->
    <div class="table-card">

        <h5>
            <i class="fa-solid fa-clock-rotate-left text-primary"></i>
            Transaksi Terbaru
        </h5>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($transaksi as $data): ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td>
                            <strong><?= $data['id'] ?></strong>
                        </td>

                        <td>
                            <?= $data['tanggal'] ?>
                        </td>

                        <td>
                            <?= $data['pelanggan'] ?>
                        </td>

                        <td>
                            Rp <?= number_format($data['total'], 0, ',', '.') ?>
                        </td>

                        <td>

                            <?php if ($data['status'] == 'Selesai'): ?>

                                <span class="badge-selesai">
                                    Selesai
                                </span>

                            <?php else: ?>

                                <span class="badge-pending">
                                    Pending
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>