<?php
require_once "../config/database.php";

$pesan_error = "";
$kode = "";
$nama = "";
$kategori = "";
$harga_jual = "";
$status = "aktif";

if (isset($_POST['simpan'])) {
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga_jual = $_POST['harga_jual'] ?? '';
    $status = $_POST['status'] ?? 'aktif';

    if ($kode === '' || $nama === '' || $harga_jual === '') {
        $pesan_error = "Kode, nama, dan harga jual wajib diisi.";
    } elseif (!is_numeric($harga_jual) || (float)$harga_jual < 0) {
        $pesan_error = "Harga jual harus berupa angka dan tidak boleh negatif.";
    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $pesan_error = "Status produk tidak valid.";
    } else {
        $harga_jual = (int)$harga_jual;

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO products (kode, nama, kategori, harga_jual, status)
             VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssis",
                $kode,
                $nama,
                $kategori,
                $harga_jual,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php?pesan=tambah");
                exit;
            } else {
                if (mysqli_stmt_errno($stmt) == 1062) {
                    $pesan_error = "Kode produk sudah digunakan. Gunakan kode lain.";
                } else {
                    $pesan_error = "Data produk gagal disimpan.";
                }

                mysqli_stmt_close($stmt);
            }
        } else {
            $pesan_error = "Formulir belum bisa diproses.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            min-width: 250px;
            min-height: 100vh;
            padding: 25px 16px;
            background: #1E293B;
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 35px 10px;
            color: #FFFFFF;
            font-size: 22px;
            font-weight: 800;
        }

        .logo i,
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
            gap: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            border-radius: 9px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-link i {
            width: 18px;
            flex-shrink: 0;
            text-align: center;
        }

        .nav-link:hover {
            background: #334155;
            color: #FFFFFF;
        }

        .nav-link.active {
            background: #3B82F6;
            color: #FFFFFF;
            font-weight: 700;
        }

        /* PROFIL BAWAH SIDEBAR */
        .sidebar-footer {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            width: 100% !important;
            margin-top: auto !important;
            padding: 18px 4px 0 !important;
            border-top: 1px solid #334155 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .avatar {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 42px !important;
            height: 42px !important;
            min-width: 42px !important;
            flex: 0 0 42px !important;
            border-radius: 50% !important;
            background: #2563EB !important;
            color: #FFFFFF !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .user-info {
            display: flex !important;
            flex: 1 1 auto !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: center !important;
            min-width: 0 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .user-name {
            display: block !important;
            width: 100% !important;
            color: #FFFFFF !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            line-height: 1.5 !important;
            overflow-wrap: anywhere !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .user-role {
            display: block !important;
            margin-top: 3px !important;
            color: #94A3B8 !important;
            font-size: 12px !important;
            line-height: 1.5 !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* BAGIAN KONTEN */
        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 75px;
            padding: 20px 30px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .breadcrumb {
            color: #94A3B8;
            font-size: 13px;
        }

        .breadcrumb span {
            color: #2563EB;
            font-weight: 700;
        }

        .topbar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 8px;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 13px;
            font-weight: 700;
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
            color: #94A3B8;
            font-size: 14px;
            line-height: 1.6;
        }

        /* TOMBOL */
        .page-actions {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563EB;
            color: #FFFFFF;
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

        /* FORMULIR */
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
            font-size: 20px;
        }

        .form-heading h2 i {
            margin-right: 7px;
            color: #2563EB;
        }

        .form-heading p {
            color: #94A3B8;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1E293B;
            font-size: 14px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            display: block;
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #475569;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
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

        /* RESPONSIF */
        @media (max-width: 800px) {
            .sidebar {
                width: 220px;
                min-width: 220px;
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
                min-height: auto;
            }

            .sidebar-footer {
                margin-top: 20px !important;
            }

            .content {
                padding: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .form-panel {
                padding: 18px;
            }

            .page-actions,
            .form-footer {
                flex-direction: column;
            }

            .btn {
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
                Dashboard / Produk / <span>Tambah Produk</span>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-box"></i>
                KonveksiKu
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h1>Tambah Produk</h1>
                <p>Tambahkan data produk baru ke dalam sistem konveksi.</p>
            </div>

            <div class="page-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Data Produk
                </a>
            </div>

            <div class="form-panel">

                <div class="form-heading">
                    <h2>
                        <i class="fa-solid fa-box"></i>
                        Formulir Produk
                    </h2>
                    <p>Lengkapi informasi produk yang akan disimpan.</p>
                </div>

                <?php if ($pesan_error !== ''): ?>
                    <div class="error-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($pesan_error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="form-group">
                        <label for="kode">Kode Produk</label>
                        <input
                            type="text"
                            id="kode"
                            name="kode"
                            value="<?= htmlspecialchars($kode); ?>"
                            placeholder="Contoh: PRD001"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= htmlspecialchars($nama); ?>"
                            placeholder="Masukkan nama produk"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input
                            type="text"
                            id="kategori"
                            name="kategori"
                            value="<?= htmlspecialchars($kategori); ?>"
                            placeholder="Contoh: Kaos atau Kemeja"
                        >
                    </div>

                    <div class="form-group">
                        <label for="harga_jual">Harga Jual (Rp)</label>
                        <input
                            type="number"
                            id="harga_jual"
                            name="harga_jual"
                            min="0"
                            value="<?= htmlspecialchars((string)$harga_jual); ?>"
                            placeholder="Masukkan harga jual"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status Produk</label>
                        <select id="status" name="status" required>
                            <option value="aktif"
                                <?= $status === 'aktif' ? 'selected' : ''; ?>>
                                Aktif
                            </option>
                            <option value="nonaktif"
                                <?= $status === 'nonaktif' ? 'selected' : ''; ?>>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="form-footer">
                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Produk
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
