<?php
require_once "../config/database.php";

$pesan = "";
$jenis_pesan = "";

$kode = "";
$nama = "";
$kontak = "";
$alamat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $kontak = trim($_POST["kontak"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if ($kode === "" || $nama === "" || $kontak === "" || $alamat === "") {
        $pesan = "Semua kolom wajib diisi.";
        $jenis_pesan = "error";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customers (kode, nama, kontak, alamat)
             VALUES (?, ?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $kode,
                $nama,
                $kontak,
                $alamat
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php?status=tambah_berhasil");
                exit;
            } else {
                if (mysqli_stmt_errno($stmt) == 1062) {
                    $pesan = "Kode pelanggan sudah digunakan. Silakan gunakan kode lain.";
                } else {
                    $pesan = "Data gagal disimpan. Silakan periksa kembali.";
                }

                $jenis_pesan = "error";
            }

            mysqli_stmt_close($stmt);
        } else {
            $pesan = "Terjadi kesalahan saat menyiapkan penyimpanan data.";
            $jenis_pesan = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan | KonveksiKu</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
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
            background: #1E293B;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 25px 16px 18px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            margin-bottom: 38px;
            font-size: 23px;
            font-weight: 800;
            color: #fff;
        }

        .logo i {
            color: #60A5FA;
            font-size: 25px;
        }

        .logo span {
            color: #60A5FA;
        }

        .nav-title {
            color: #94A3B8;
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
            border-radius: 9px;
            transition: .2s;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: #334155;
            color: #fff;
        }

        .nav-link.active {
            background: #3B82F6;
            color: #fff;
        }

        .sidebar-footer {
            display: flex;
            align-items: center;
            gap: 11px;
            border-top: 1px solid #334155;
            padding: 20px 8px 0;
            margin-top: auto;
        }

        .avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 50%;
            background: #2563EB;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .user-role {
            font-size: 11px;
            color: #94A3B8;
            margin-top: 4px;
        }

        /* KONTEN UTAMA */
        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 90px;
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: 20px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 23px;
            color: #1E293B;
            margin-bottom: 7px;
        }

        .breadcrumb {
            color: #64748B;
            font-size: 12px;
        }

        .breadcrumb span {
            color: #2563EB;
        }

        .topbar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #DBEAFE;
            border-radius: 9px;
            color: #1D4ED8;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .content {
            padding: 30px 32px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h2 {
            color: #1E293B;
            font-size: 23px;
            margin-bottom: 8px;
        }

        .welcome p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.7;
        }

        /* FORM */
        .form-panel {
            max-width: 850px;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .04);
        }

        .form-heading {
            margin-bottom: 25px;
            padding-bottom: 18px;
            border-bottom: 1px solid #E2E8F0;
        }

        .form-heading h3 {
            font-size: 18px;
            color: #1E293B;
            margin-bottom: 7px;
        }

        .form-heading h3 i {
            color: #2563EB;
            margin-right: 5px;
        }

        .form-heading p {
            font-size: 13px;
            color: #64748B;
            line-height: 1.6;
        }

        .alert {
            padding: 13px 15px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.6;
        }

        .alert.error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .form-group label {
            display: block;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 9px;
        }

        .required {
            color: #DC2626;
        }

        .form-group input,
        .form-group textarea {
            display: block;
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 9px;
            background: #fff;
            color: #1E293B;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: .2s;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #94A3B8;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px #DBEAFE;
        }

        .form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-help {
            display: block;
            color: #64748B;
            font-size: 11px;
            margin-top: 7px;
        }

        /* TOMBOL */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border: none;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        .btn-save {
            background: #2563EB;
            color: #fff;
        }

        .btn-save:hover {
            background: #1D4ED8;
        }

        .btn-back {
            background: #E2E8F0;
            color: #475569;
        }

        .btn-back:hover {
            background: #CBD5E1;
        }

        .footer-note {
            text-align: center;
            color: #94A3B8;
            font-size: 11px;
            padding: 24px 10px 5px;
        }

        /* TAMPILAN TABLET */
        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
            }

            .topbar,
            .content {
                padding-left: 20px;
                padding-right: 20px;
            }
        }

        /* TAMPILAN HP */
        @media (max-width: 650px) {
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

            .nav-link {
                padding: 11px 12px;
            }

            .sidebar-footer {
                margin-top: 15px;
            }

            .topbar {
                padding: 20px 16px;
                align-items: flex-start;
            }

            .topbar h1 {
                font-size: 19px;
            }

            .topbar-label {
                font-size: 10px;
                padding: 9px;
            }

            .content {
                padding: 22px 14px;
            }

            .form-panel {
                padding: 19px;
            }

            .form-actions {
                flex-direction: column-reverse;
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

        <div class="nav-title">Menu Utama</div>

        <a href="../index.php" class="nav-link">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <a href="index.php" class="nav-link active">
            <i class="fa-solid fa-users"></i>
            Pelanggan
        </a>

        <a href="../produk/index.php" class="nav-link">
            <i class="fa-solid fa-box"></i>
            Produk
        </a>

        <a href="../pesanan/index.php" class="nav-link">
            <i class="fa-solid fa-clipboard-list"></i>
            Pesanan
        </a>

        <div class="sidebar-footer">
            <div class="avatar">DP</div>
            <div>
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Pelanggan</div>
            </div>
        </div>

    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Tambah Pelanggan</h1>

                <div class="breadcrumb">
                    Beranda / Pelanggan / <span>Tambah</span>
                </div>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-circle-check"></i>
                Sistem Konveksi
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h2>Tambah Pelanggan Baru</h2>
                <p>
                    Masukkan informasi pelanggan untuk menyimpannya
                    ke dalam database KonveksiKu.
                </p>
            </div>

            <div class="form-panel">

                <div class="form-heading">
                    <h3>
                        <i class="fa-solid fa-user-plus"></i>
                        Informasi Pelanggan
                    </h3>

                    <p>
                        Kolom yang bertanda
                        <span class="required">*</span>
                        wajib diisi.
                    </p>
                </div>

                <?php if ($pesan !== "") { ?>
                    <div class="alert <?= $jenis_pesan === "error" ? "error" : ""; ?>">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($pesan); ?>
                    </div>
                <?php } ?>

                <form action="" method="POST">

                    <div class="form-group">
                        <label for="kode">
                            Kode Pelanggan <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kode"
                            name="kode"
                            value="<?= htmlspecialchars($kode); ?>"
                            placeholder="Contoh: PLG003"
                            required
                        >

                        <small class="form-help">
                            Gunakan kode pelanggan yang belum terdaftar.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="nama">
                            Nama Pelanggan <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="<?= htmlspecialchars($nama); ?>"
                            placeholder="Masukkan nama pelanggan"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="kontak">
                            Kontak <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kontak"
                            name="kontak"
                            value="<?= htmlspecialchars($kontak); ?>"
                            placeholder="Masukkan nomor telepon atau kontak"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="alamat">
                            Alamat <span class="required">*</span>
                        </label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            placeholder="Masukkan alamat lengkap pelanggan"
                            required
                        ><?= htmlspecialchars($alamat); ?></textarea>
                    </div>

                    <div class="form-actions">

                        <a href="index.php" class="btn btn-back">
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Pelanggan
                        </button>

                    </div>

                </form>

            </div>

            <div class="footer-note">
                &copy; <?= date('Y'); ?> KonveksiKu — Sistem Manajemen Konveksi
            </div>

        </section>

    </main>

</div>
</body>
</html>