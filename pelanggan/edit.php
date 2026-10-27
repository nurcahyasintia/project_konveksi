<?php
require_once "../config/database.php";

$error = "";
$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/* Ambil data pelanggan */
$stmt = mysqli_prepare(
    $conn,
    "SELECT kode, nama, kontak, alamat FROM customers WHERE id = ?"
);

if (!$stmt) {
    die("Gagal mengambil data pelanggan.");
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $kode_db, $nama_db, $kontak_db, $alamat_db);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: index.php");
    exit;
}

mysqli_stmt_close($stmt);

$kode = $kode_db ?? "";
$nama = $nama_db ?? "";
$kontak = $kontak_db ?? "";
$alamat = $alamat_db ?? "";

/* Proses menyimpan perubahan */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $kontak = trim($_POST["kontak"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if ($kode === "" || $nama === "") {
        $error = "Kode dan nama pelanggan wajib diisi.";
    } else {
        $update = mysqli_prepare(
            $conn,
            "UPDATE customers
             SET kode = ?, nama = ?, kontak = ?, alamat = ?
             WHERE id = ?"
        );

        if ($update) {
            mysqli_stmt_bind_param(
                $update,
                "ssssi",
                $kode,
                $nama,
                $kontak,
                $alamat,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header("Location: index.php?pesan=edit");
                exit;
            } else {
                if (mysqli_stmt_errno($update) == 1062) {
                    $error = "Kode pelanggan sudah digunakan. Silakan gunakan kode lain.";
                } else {
                    $error = "Perubahan gagal disimpan. Silakan coba kembali.";
                }
            }

            mysqli_stmt_close($update);
        } else {
            $error = "Terjadi kesalahan saat menyiapkan perubahan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan | KonveksiKu</title>

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

        .sidebar {
            width: 250px;
            min-width: 250px;
            background: #1E293B;
            color: #FFFFFF;
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

        .nav-link:hover,
        .nav-link.active {
            background: #3B82F6;
            color: #FFFFFF;
        }

        .sidebar-footer {
            display: flex !important;
            align-items: center !important;
            gap: 11px;
            border-top: 1px solid #334155;
            padding: 20px 5px 0;
            margin-top: auto !important;
            width: 100%;
        }

        .avatar {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
            border-radius: 50% !important;
            background: #2563EB !important;
            color: #FFFFFF;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px;
            font-weight: 800;
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
            font-weight: 700 !important;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .user-role {
            font-size: 12px !important;
            color: #94A3B8 !important;
            line-height: 1.4;
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            min-height: 90px;
            background: #FFFFFF;
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

        .page-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 23px;
        }

        .form-panel {
            width: 100%;
            max-width: 850px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .035);
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
            margin-right: 6px;
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
            background: #FEE2E2;
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
            border: 1px solid #E2E8F0;
            border-radius: 9px;
            background: #FFFFFF;
            color: #1E293B;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: .2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

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
            color: #FFFFFF;
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
                margin-top: 15px !important;
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
            <i class="fa-solid fa-cart-shopping"></i>
            Pesanan
        </a>

        <div class="sidebar-footer">
            <div class="avatar">PL</div>
            <div class="user-info">
                <div class="user-name">Denia Ayuna Lestari</div>
                <div class="user-role">Staff Pelanggan</div>
            </div>
        </div>

    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <h1>Edit Pelanggan</h1>
                <div class="breadcrumb">
                    Dashboard / Pelanggan / <span>Edit</span>
                </div>
            </div>

            <div class="topbar-label">
                <i class="fa-solid fa-shirt"></i>
                KonveksiKu
            </div>
        </header>

        <section class="content">

            <div class="welcome">
                <h2>Ubah Data Pelanggan</h2>
                <p>Perbarui informasi pelanggan melalui formulir berikut.</p>
            </div>

            <div class="page-actions">
                <a href="index.php" class="btn btn-back">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Pelanggan
                </a>
            </div>

            <div class="form-panel">

                <div class="form-heading">
                    <h3>
                        <i class="fa-solid fa-user-pen"></i>
                        Informasi Pelanggan
                    </h3>

                    <p>
                        Kolom bertanda <span class="required">*</span>
                        wajib diisi.
                    </p>
                </div>

                <?php if ($error !== "") { ?>
                    <div class="alert">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php } ?>

                <form method="POST" action="edit.php?id=<?= $id; ?>">

                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="form-group">
                        <label for="kode">
                            Kode Pelanggan <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="kode"
                            name="kode"
                            value="<?= htmlspecialchars($kode); ?>"
                            placeholder="Contoh: PLG001"
                            required
                        >
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
                        <label for="kontak">Kontak</label>

                        <input
                            type="text"
                            id="kontak"
                            name="kontak"
                            value="<?= htmlspecialchars($kontak); ?>"
                            placeholder="Nomor telepon pelanggan"
                        >
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            placeholder="Masukkan alamat pelanggan"
                        ><?= htmlspecialchars($alamat); ?></textarea>
                    </div>

                    <div class="form-actions">

                        <a href="index.php" class="btn btn-back">
                            <i class="fa-solid fa-xmark"></i>
                            Batal
                        </a>

                        <button type="submit" class="btn btn-save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
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
