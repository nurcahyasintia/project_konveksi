<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$employee) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $jabatan = trim($_POST["jabatan"] ?? "");
    $divisi = trim($_POST["divisi"] ?? "");
    $tanggal = $_POST["tanggal_masuk"] ?? "";
    $status = $_POST["status"] ?? "Aktif";

    if ($kode === "" || $nama === "" || $jabatan === "" ||
        $divisi === "" || $tanggal === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!in_array($status, ["Aktif", "Nonaktif", "Cuti"], true)) {
        $error = "Status tidak valid.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE employees
             SET kode=?, nama=?, jabatan=?, divisi=?, tanggal_masuk=?, status=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "ssssssi",
            $kode, $nama, $jabatan, $divisi, $tanggal, $status, $id
        );

        if ($stmt->execute()) {
            $stmt->close();

            $uid = (int) $_SESSION["user_id"];
            $aktivitas = "Mengubah data karyawan";
            $referensi = "Employee ID: " . $id;
            $log = $conn->prepare(
                "INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu)
                 VALUES (?, ?, ?, NOW())"
            );
            if ($log) {
                $log->bind_param("iss", $uid, $aktivitas, $referensi);
                $log->execute();
                $log->close();
            }

            header("Location: index.php?pesan=berhasil_edit");
            exit;
        }

        $error = "Data gagal disimpan. Periksa apakah kode karyawan sudah digunakan.";
        $stmt->close();
    }

    $employee = array_merge($employee, [
        "kode" => $kode,
        "nama" => $nama,
        "jabatan" => $jabatan,
        "divisi" => $divisi,
        "tanggal_masuk" => $tanggal,
        "status" => $status
    ]);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Karyawan - KonveksiKu</title>
    <link rel="stylesheet" href="../assets/konveksiku.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">👕</span>
            <span>Konveksi<span class="brand-accent">Ku</span></span>
        </div>
        <div class="sidebar-label">Anggota 1</div>
        <nav class="sidebar-nav">
            <a href="../user/index.php">♙ Profil &amp; Akun</a>
            <a href="index.php" class="active">♟ Karyawan</a>
            <a href="../absensi/index.php">▣ Absensi</a>
            <a href="../audit_log/index.php">◴ Audit Log</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="user-avatar">AD</div>
                <div><strong><?= htmlspecialchars($_SESSION["nama"] ?? "Pengguna") ?></strong><small>KonveksiKu</small></div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="page-heading">
                <h1>Edit Karyawan</h1>
                <div class="breadcrumb">Home / Karyawan / Edit</div>
            </div>
            <div class="topbar-actions">
                <span class="admin-pill"><?= htmlspecialchars($_SESSION["role"] ?? "Pengguna") ?></span>
            </div>
        </header>

        <section class="page-content">
            <div class="card">
                <div class="card-header">Informasi Karyawan</div>
                <div class="card-body">
                    <?php if ($error !== ""): ?>
                        <div class="alert-info"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="kode">Kode Karyawan</label>
                                <input id="kode" name="kode" required value="<?= htmlspecialchars($employee["kode"] ?? "") ?>">
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input id="nama" name="nama" required value="<?= htmlspecialchars($employee["nama"] ?? "") ?>">
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input id="jabatan" name="jabatan" required value="<?= htmlspecialchars($employee["jabatan"] ?? "") ?>">
                            </div>
                            <div class="form-group">
                                <label for="divisi">Divisi</label>
                                <select id="divisi" name="divisi" required>
                                    <?php foreach (["Produksi","Gudang","Keuangan","Admin","HRD","Quality Control"] as $d): ?>
                                        <option value="<?= $d ?>" <?= ($employee["divisi"] ?? "") === $d ? "selected" : "" ?>>
                                            <?= $d ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_masuk">Tanggal Masuk</label>
                                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required value="<?= htmlspecialchars($employee["tanggal_masuk"] ?? "") ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <?php foreach (["Aktif","Nonaktif","Cuti"] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($employee["status"] ?? "") === $s ? "selected" : "" ?>>
                                            <?= $s ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                        <a class="btn btn-outline" href="index.php">Batal</a>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
