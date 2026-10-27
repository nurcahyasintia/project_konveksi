<?php
require_once "../config/database.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = "";

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nomor_order = trim($_POST['nomor_order'] ?? "");
    $customer_id = (int) ($_POST['customer_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? "";
    $deadline = $_POST['deadline'] ?? "";
    $total = $_POST['total'] ?? "";
    $dp = $_POST['dp'] ?? "";
    $status_pembayaran = $_POST['status_pembayaran'] ?? "";
    $status_produksi = $_POST['status_produksi'] ?? "";

    $status_pembayaran_valid = ["belum_lunas", "dp", "lunas"];
    $status_produksi_valid = ["menunggu", "proses", "selesai", "dikirim"];

    $data['nomor_order'] = $nomor_order;
    $data['customer_id'] = $customer_id;
    $data['tanggal'] = $tanggal;
    $data['deadline'] = $deadline;
    $data['total'] = $total;
    $data['dp'] = $dp;
    $data['status_pembayaran'] = $status_pembayaran;
    $data['status_produksi'] = $status_produksi;

    if (
        $nomor_order === "" ||
        $customer_id < 1 ||
        $tanggal === "" ||
        $deadline === "" ||
        $total === "" ||
        $dp === ""
    ) {
        $error = "Semua kolom wajib diisi.";
    } elseif (
        !is_numeric($total) ||
        !is_numeric($dp) ||
        (float)$total < 0 ||
        (float)$dp < 0
    ) {
        $error = "Total harga dan DP harus berupa angka minimal 0.";
    } elseif ((float)$dp > (float)$total) {
        $error = "DP tidak boleh melebihi total harga.";
    } elseif (
        !in_array($status_pembayaran, $status_pembayaran_valid, true) ||
        !in_array($status_produksi, $status_produksi_valid, true)
    ) {
        $error = "Status pesanan tidak valid.";
    } else {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM customers WHERE id = ?"
        );
        mysqli_stmt_bind_param($cek, "i", $customer_id);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);
        $pelanggan_ada = mysqli_num_rows($hasil_cek) > 0;
        mysqli_stmt_close($cek);

        if (!$pelanggan_ada) {
            $error = "Pelanggan yang dipilih tidak ditemukan.";
        } else {
            $total_db = (float)$total;
            $dp_db = (float)$dp;

            $update = mysqli_prepare(
                $conn,
                "UPDATE orders SET
                    nomor_order = ?,
                    customer_id = ?,
                    tanggal = ?,
                    deadline = ?,
                    total = ?,
                    dp = ?,
                    status_pembayaran = ?,
                    status_produksi = ?
                WHERE id = ?"
            );

            if ($update) {
                mysqli_stmt_bind_param(
                    $update,
                    "sissddssi",
                    $nomor_order,
                    $customer_id,
                    $tanggal,
                    $deadline,
                    $total_db,
                    $dp_db,
                    $status_pembayaran,
                    $status_produksi,
                    $id
                );

                if (mysqli_stmt_execute($update)) {
                    mysqli_stmt_close($update);
                    header("Location: index.php");
                    exit;
                }

                if (mysqli_stmt_errno($update) == 1062) {
                    $error = "Nomor pesanan sudah digunakan. Silakan gunakan nomor lain.";
                } else {
                    $error = "Pesanan gagal diperbarui. Silakan coba lagi.";
                }

                mysqli_stmt_close($update);
            } else {
                $error = "Form edit gagal diproses.";
            }
        }
    }
}

$customers = mysqli_query(
    $conn,
    "SELECT id, kode, nama FROM customers ORDER BY nama ASC"
);

if (!$customers) {
    die("Gagal mengambil data pelanggan.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #F8FAFC;
            color: #1E293B;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-width: 250px;
            background: #1E293B;
            color: white;
            padding: 25px 16px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            padding: 0 12px;
            margin-bottom: 38px;
        }

        .logo i,
        .logo span {
            color: #60A5FA;
        }

        .logo i {
            margin-right: 8px;
        }

        .nav-title {
            color: #94A3B8;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
            padding: 0 12px;
            margin-bottom: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 14px;
            margin-bottom: 6px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
            border-radius: 9px;
            transition: 0.2s;
        }

        .nav-link i {
            width: 18px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #3B82F6;
            color: white;
        }

        .sidebar-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #334155;
            margin-top: auto;
            padding: 20px 8px 0;
        }

        .avatar {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #2563EB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .user-name {
            font-size: 13px;
            font-weight: bold;
            color: #F8FAFC;
        }

        .user-role {
            color: #94A3B8;
            font-size: 12px;
            margin-top: 4px;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: white;
            min-height: 85px;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #E2E8F0;
        }

        .topbar h1 {
            color: #1E293B;
            font-size: 22px;
            margin-bottom: 7px;
        }

        .breadcrumb {
            font-size: 12px;
            color: #64748B;
        }

        .topbar-label {
            color: #2563EB;
            background: #DBEAFE;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 12px;
            white-space: nowrap;
        }

        .content {
            padding: 30px;
        }

        .page-heading {
            margin-bottom: 22px;
        }

        .page-heading h2 {
            color: #1E293B;
            font-size: 22px;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #64748B;
            font-size: 13px;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #2563EB;
            color: white;
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

        .form-panel {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 13px;
            padding: 25px;
            max-width: 850px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04);
        }

        .form-heading {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #E2E8F0;
        }

        .form-heading h3 {
            color: #1E293B;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .form-heading h3 i {
            color: #2563EB;
            margin-right: 8px;
        }

        .form-heading p {
            color: #64748B;
            font-size: 13px;
            line-height: 1.6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            min-width: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            display: block;
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background: white;
            color: #1E293B;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .form-footer {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .error-message {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }

        .back-area {
            margin-bottom: 22px;
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
                padding: 20px 10px;
            }

            .topbar,
            .content {
                padding: 20px;
            }
        }

        @media (max-width: 600px) {
            .app {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 0;
                padding: 18px;
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
            }

            .content {
                padding: 18px;
            }

            .form-panel {
                padding: 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-footer {
                flex-direction: column;
            }

            .form-footer .btn {
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
            Konveksi<span>Ku</span>
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
            <div>
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Pesanan</div>
            </div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Edit Pesanan</h1>
                <div class="breadcrumb">
                    Dashboard / Pesanan / Edit Pesanan
                </div>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-circle-check"></i>
                Sistem Konveksi
            </div>
        </header>

        <section class="content">
            <div class="page-heading">
                <h2>Edit Pesanan</h2>
                <p>Ubah informasi pesanan sesuai kebutuhan.</p>
            </div>

            <div class="back-area">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Data Pesanan
                </a>
            </div>

            <div class="form-panel">
                <div class="form-heading">
                    <h3>
                        <i class="fa-solid fa-pen-to-square"></i>
                        Form Edit Pesanan
                    </h3>
                    <p>Pastikan data sudah benar sebelum menyimpan perubahan.</p>
                </div>

                <?php if ($error !== ""): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= e($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-grid">

                        <div class="form-group">
                            <label for="nomor_order">Nomor Pesanan</label>
                            <input
                                type="text"
                                id="nomor_order"
                                name="nomor_order"
                                value="<?= e($data['nomor_order']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="customer_id">Pelanggan</label>
                            <select id="customer_id" name="customer_id" required>
                                <option value="">-- Pilih Pelanggan --</option>

                                <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                                    <option
                                        value="<?= (int)$customer['id']; ?>"
                                        <?= (int)$customer['id'] === (int)$data['customer_id'] ? 'selected' : ''; ?>
                                    >
                                        <?= e($customer['kode']); ?> -
                                        <?= e($customer['nama']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal">Tanggal Pesanan</label>
                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= e($data['tanggal']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="deadline">Batas Waktu Pengerjaan</label>
                            <input
                                type="date"
                                id="deadline"
                                name="deadline"
                                value="<?= e($data['deadline']); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="total">Total Harga (Rp)</label>
                            <input
                                type="number"
                                id="total"
                                name="total"
                                value="<?= e($data['total']); ?>"
                                min="0"
                                step="1"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="dp">Uang Muka / DP (Rp)</label>
                            <input
                                type="number"
                                id="dp"
                                name="dp"
                                value="<?= e($data['dp']); ?>"
                                min="0"
                                step="1"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select id="status_pembayaran" name="status_pembayaran" required>
                                <option value="belum_lunas" <?= $data['status_pembayaran'] === 'belum_lunas' ? 'selected' : ''; ?>>
                                    Belum Lunas
                                </option>
                                <option value="dp" <?= $data['status_pembayaran'] === 'dp' ? 'selected' : ''; ?>>
                                    DP
                                </option>
                                <option value="lunas" <?= $data['status_pembayaran'] === 'lunas' ? 'selected' : ''; ?>>
                                    Lunas
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status_produksi">Status Produksi</label>
                            <select id="status_produksi" name="status_produksi" required>
                                <option value="menunggu" <?= $data['status_produksi'] === 'menunggu' ? 'selected' : ''; ?>>
                                    Menunggu
                                </option>
                                <option value="proses" <?= $data['status_produksi'] === 'proses' ? 'selected' : ''; ?>>
                                    Proses
                                </option>
                                <option value="selesai" <?= $data['status_produksi'] === 'selesai' ? 'selected' : ''; ?>>
                                    Selesai
                                </option>
                                <option value="dikirim" <?= $data['status_produksi'] === 'dikirim' ? 'selected' : ''; ?>>
                                    Dikirim
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="form-footer">
                        <button type="submit" name="simpan" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>
