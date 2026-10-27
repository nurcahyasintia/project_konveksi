<?php
require_once "../config/database.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?pesan=id_tidak_valid");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, kode, nama, kategori, harga_jual, status
     FROM products
     WHERE id = ?"
);

if (!$stmt) {
    die("Gagal mengambil data produk.");
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=data_tidak_ditemukan");
    exit;
}

$pesan_error = "";

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga_input = $_POST['harga_jual'] ?? '';
    $status = $_POST['status'] ?? '';

    // Pertahankan input jika validasi gagal.
    $data['kode'] = $kode;
    $data['nama'] = $nama;
    $data['kategori'] = $kategori;
    $data['harga_jual'] = $harga_input;
    $data['status'] = $status;

    if ($kode === '' || $nama === '' || $harga_input === '') {
        $pesan_error = "Kode, nama, dan harga jual wajib diisi.";
    } elseif (!is_numeric($harga_input) || (float) $harga_input < 0) {
        $pesan_error = "Harga jual harus berupa angka dan tidak boleh negatif.";
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $pesan_error = "Status produk tidak valid.";
    } else {
        $harga_jual = (int) $harga_input;

        $update = mysqli_prepare(
            $conn,
            "UPDATE products
             SET kode = ?, nama = ?, kategori = ?, harga_jual = ?, status = ?
             WHERE id = ?"
        );

        if ($update) {
            mysqli_stmt_bind_param(
                $update,
                "sssisi",
                $kode,
                $nama,
                $kategori,
                $harga_jual,
                $status,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                if (mysqli_stmt_errno($update) == 1062) {
                    $pesan_error = "Kode produk sudah digunakan. Silakan pakai kode lain.";
                } else {
                    $pesan_error = "Data produk gagal diperbarui. Silakan periksa kembali.";
                }

                mysqli_stmt_close($update);
            }
        } else {
            $pesan_error = "Formulir belum bisa diproses. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
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
            padding: 25px 16px;
            background: #1E293B;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .logo {
            margin: 0 0 35px 10px;
            font-size: 23px;
            font-weight: bold;
        }

        .logo i {
            margin-right: 8px;
            color: #60A5FA;
        }

        .logo span {
            color: #60A5FA;
        }

        .nav-title {
            margin: 0 0 12px 12px;
            color: #94A3B8;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        nav {
            display: flex;
            flex-direction: column;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 6px;
            color: #CBD5E1;
            text-decoration: none;
            border-radius: 9px;
            font-size: 14px;
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
            display: flex !important;
            align-items: center !important;
            gap: 11px;
            margin-top: auto !important;
            padding: 20px 5px 0;
            border-top: 1px solid #334155;
            width: 100%;
        }

        .avatar {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            background: #2563EB !important;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .user-info {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .user-name {
            color: #FFFFFF !important;
            font-size: 13px !important;
            font-weight: bold !important;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .user-role {
            color: #94A3B8 !important;
            font-size: 12px !important;
            line-height: 1.4;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 90px;
            padding: 20px 30px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .breadcrumb {
            color: #64748B;
            font-size: 13px;
        }

        .topbar-label {
            color: #2563EB;
            font-size: 13px;
            font-weight: bold;
        }

        .topbar-label i {
            margin-right: 5px;
        }

        .content {
            padding: 30px;
        }

        .welcome {
            margin-bottom: 22px;
        }

        .welcome h1 {
            margin-bottom: 8px;
            color: #1E293B;
            font-size: 25px;
        }

        .welcome p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
        }

        .page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
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
            width: 100%;
            max-width: 700px;
            padding: 25px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }

        .form-heading {
            margin-bottom: 24px;
        }

        .form-heading h2 {
            margin-bottom: 8px;
            color: #1E293B;
            font-size: 20px;
        }

        .form-heading h2 i {
            margin-right: 7px;
            color: #2563EB;
        }

        .form-heading p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #1E293B;
            font-family: inherit;
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
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 8px;
            background: #FEE2E2;
            color: #B91C1C;
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 18px 20px;
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
                margin-top: 15px !important;
            }

            .content {
                padding: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-panel {
                padding: 17px;
            }

            .page-actions,
            .form-footer {
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
            Konveksi<span>Ku</span>
        </div>

        <p class="nav-title">Menu Utama</p>

        <nav>
            <a href="../index.php" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="../pelanggan/index.php" class="nav-link">
                <i class="fa-solid fa-users"></i>
                <span>Pelanggan</span>
            </a>

            <a href="index.php" class="nav-link active">
                <i class="fa-solid fa-box"></i>
                <span>Produk</span>
            </a>

            <a href="../pesanan/index.php" class="nav-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Pesanan</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="avatar">PR</div>

            <div class="user-info">
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Produk</div>
            </div>
        </div>
    </aside>

    <main class="main">

        <header class="topbar">
            <div class="breadcrumb">
                Dashboard / Produk / Edit Produk
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-shirt"></i>
                KonveksiKu
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h1>Edit Produk</h1>
                <p>Perbarui informasi produk yang tersimpan di sistem.</p>
            </div>

            <div class="page-actions">
                <a href="index.php" class="btn-action btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Data Produk
                </a>
            </div>

            <div class="form-panel">

                <div class="form-heading">
                    <h2>
                        <i class="fa-solid fa-pen-to-square"></i>
                        Formulir Edit Produk
                    </h2>
                    <p>Ubah data produk sesuai informasi terbaru.</p>
                </div>

                <?php if ($pesan_error !== '') { ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($pesan_error); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="form-group">
                        <label for="kode">Kode Produk</label>
                        <input
                            type="text"
                            id="kode"
                            name="kode"
                            value="<?= htmlspecialchars((string) $data['kode']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= htmlspecialchars((string) $data['nama']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input
                            type="text"
                            id="kategori"
                            name="kategori"
                            value="<?= htmlspecialchars((string) ($data['kategori'] ?? '')); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="harga_jual">Harga Jual (Rp)</label>
                        <input
                            type="number"
                            id="harga_jual"
                            name="harga_jual"
                            min="0"
                            step="1"
                            value="<?= htmlspecialchars((string) $data['harga_jual']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status Produk</label>
                        <select id="status" name="status" required>
                            <option value="aktif"
                                <?= $data['status'] === 'aktif' ? 'selected' : ''; ?>>
                                Aktif
                            </option>

                            <option value="nonaktif"
                                <?= $data['status'] === 'nonaktif' ? 'selected' : ''; ?>>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="form-footer">
                        <button type="submit" name="simpan"
                                class="btn-action btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn-action btn-secondary">
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