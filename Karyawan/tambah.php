
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$errors = [];
$kode = "";
$nama = "";
$jabatan = "";
$divisi = "";
$tanggalMasuk = date("Y-m-d");
$status = "Aktif";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $jabatan = trim($_POST["jabatan"] ?? "");
    $divisi = trim($_POST["divisi"] ?? "");
    $tanggalMasuk = trim($_POST["tanggal_masuk"] ?? "");
    $status = trim($_POST["status"] ?? "Aktif");

    if ($kode === "" || $nama === "" || $jabatan === "" || $divisi === "" || $tanggalMasuk === "") {
        $errors[] = "Semua kolom wajib diisi.";
    }

    if (!in_array($status, ["Aktif", "Cuti", "Nonaktif"], true)) {
        $errors[] = "Status karyawan tidak valid.";
    }

    $date = DateTime::createFromFormat("!Y-m-d", $tanggalMasuk);
    if (!$date || $date->format("Y-m-d") !== $tanggalMasuk) {
        $errors[] = "Tanggal masuk tidak valid.";
    }

    if (!$errors) {
        // Pastikan kode karyawan belum digunakan.
        $cek = $conn->prepare("SELECT id FROM employees WHERE kode = ? LIMIT 1");
        $cek->bind_param("s", $kode);
        $cek->execute();
        $sudahAda = $cek->get_result()->fetch_assoc();
        $cek->close();

        if ($sudahAda) {
            $errors[] = "Kode karyawan sudah digunakan. Gunakan kode yang berbeda.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO employees (kode, nama, jabatan, divisi, tanggal_masuk, status)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssss",
                $kode, $nama, $jabatan, $divisi, $tanggalMasuk, $status
            );

            if ($stmt->execute()) {
                $employeeId = $conn->insert_id;
                $stmt->close();

                // Catat aktivitas jika tabel activity_logs tersedia.
                $userId = (int) $_SESSION["user_id"];
                $aktivitas = "Menambahkan data karyawan";
                $referensi = "Employee ID: " . $employeeId;

                $log = $conn->prepare(
                    "INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu)
                     VALUES (?, ?, ?, NOW())"
                );

                if ($log) {
                    $log->bind_param("iss", $userId, $aktivitas, $referensi);
                    $log->execute();
                    $log->close();
                }

                header("Location: index.php?pesan=berhasil_tambah");
                exit;
            } else {
                $errors[] = "Gagal menyimpan data. Pastikan kolom database sesuai dengan form ini.";
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Karyawan - KonveksiKu</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f0f4f9;color:#172b4d}
a{text-decoration:none;color:inherit}
.app{display:flex;min-height:100vh}
.sidebar{width:260px;background:#101a30;color:#a9bdd8;position:fixed;inset:0 auto 0 0;display:flex;flex-direction:column}
.brand{height:84px;display:flex;align-items:center;gap:13px;padding:0 25px;color:white;font-size:24px;font-weight:bold;border-bottom:1px solid #263147}
.brand-icon{background:#1b2e52;color:#3977ff;border-radius:13px;padding:10px}
.brand span b{color:#3977ff}
.side-label{padding:32px 25px 14px;font-size:12px;font-weight:bold;letter-spacing:2px;color:#7891b5}
.nav{padding:0 12px}
.nav a{display:block;padding:17px 18px;margin-bottom:6px;border-radius:13px;font-size:15px}
.nav a:hover{background:#1c2b48}
.nav a.active{background:#2864ed;color:white;font-weight:bold;box-shadow:0 7px 18px #1b3d7b66}
.side-bottom{margin-top:auto;padding:20px;border-top:1px solid #263147}
.user-mini{display:flex;align-items:center;gap:12px;color:white;font-size:14px}
.avatar{width:45px;height:45px;display:grid;place-items:center;border-radius:50%;background:#168cdb;color:white;font-weight:bold}
.user-mini small{display:block;color:#8ea5c7;margin-top:5px}
main{margin-left:260px;width:calc(100% - 260px);min-width:0}
.topbar{background:white;padding:24px 32px;border-bottom:1px solid #e2e9f2}
h1{margin:0 0 9px;font-size:27px;color:#102443}
.breadcrumb{font-size:14px;color:#7890ae}
.content{padding:28px 32px}
.card{background:white;border:1px solid #e1e8f1;border-radius:18px;overflow:hidden;box-shadow:0 2px 4px #1d35570a}
.card-head{padding:22px 25px;border-bottom:1px solid #e5ebf3;font-size:18px;font-weight:bold}
.card-body{padding:26px}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
.group{display:flex;flex-direction:column;gap:9px}
label{font-size:14px;font-weight:bold;color:#344b69}
input,select{width:100%;min-height:47px;padding:12px 14px;border:1px solid #d3deeb;border-radius:11px;background:white;color:#203754;font:inherit}
input:focus,select:focus{outline:2px solid #c4d8ff;border-color:#2864ed}
.actions{display:flex;gap:11px;flex-wrap:wrap;margin-top:26px}
.btn{display:inline-flex;align-items:center;justify-content:center;padding:13px 19px;border:0;border-radius:11px;font-size:14px;font-weight:bold;cursor:pointer}
.primary{background:#2864ed;color:white}
.light{background:white;border:1px solid #d5dfec;color:#244060}
.error{padding:14px 18px;border-radius:11px;background:#fff0f0;color:#b52d36;border:1px solid #ffd4d4;margin-bottom:20px}
.error ul{margin:7px 0 0;padding-left:20px}
.hint{font-size:12px;color:#8294ad;margin-top:4px}
@media(max-width:700px){.sidebar{width:70px}.brand{padding:0;justify-content:center}.brand>span:last-child,.side-label,.nav .nav-text,.user-mini>div:last-child{display:none}.nav{padding:0 7px}.nav a{text-align:center;padding:16px 5px;font-size:20px}.side-bottom{padding:10px}.user-mini{justify-content:center}.avatar{width:40px;height:40px}main{margin-left:70px;width:calc(100% - 70px)}.content{padding:15px}.topbar{padding:20px}.grid{grid-template-columns:1fr}.card-body{padding:18px}}
</style>
</head>
<body>
<div class="app">
<aside class="sidebar">
    <div class="brand"><span class="brand-icon">👕</span><span>Konveksi<b>Ku</b></span></div>
    <div class="side-label">ANGGOTA 1</div>
    <nav class="nav">
        <a href="../user/index.php">♙ <span class="nav-text">Profil &amp; Akun</span></a>
        <a href="index.php" class="active">♟ <span class="nav-text">Karyawan</span></a>
        <a href="../absensi/index.php">▣ <span class="nav-text">Absensi</span></a>
        <a href="../audit_log/index.php">◴ <span class="nav-text">Audit Log</span></a>
    </nav>
    <div class="side-bottom"><div class="user-mini"><div class="avatar">AD</div><div><strong><?= e($_SESSION["nama"] ?? "Pengguna") ?></strong><small><?= e($_SESSION["role"] ?? "Pengguna") ?></small></div></div></div>
</aside>

<main>
<header class="topbar">
    <h1>Tambah Karyawan</h1>
    <div class="breadcrumb">Home / Karyawan / Tambah Karyawan</div>
</header>

<section class="content">
    <?php if ($errors): ?>
        <div class="error">
            <strong>Data belum bisa disimpan:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-head">Form Data Karyawan</div>
        <div class="card-body">
            <form method="POST" action="tambah.php">
                <div class="grid">
                    <div class="group">
                        <label for="kode">Kode Karyawan</label>
                        <input id="kode" name="kode" type="text" placeholder="Contoh: EMP-006" value="<?= e($kode) ?>" required>
                    </div>

                    <div class="group">
                        <label for="nama">Nama Lengkap</label>
                        <input id="nama" name="nama" type="text" placeholder="Masukkan nama karyawan" value="<?= e($nama) ?>" required>
                    </div>

                    <div class="group">
                        <label for="jabatan">Jabatan</label>
                        <input id="jabatan" name="jabatan" type="text" placeholder="Contoh: Operator Jahit" value="<?= e($jabatan) ?>" required>
                    </div>

                    <div class="group">
                        <label for="divisi">Divisi</label>
                        <select id="divisi" name="divisi" required>
                            <option value="">Pilih divisi</option>
                            <?php foreach (["Produksi", "Gudang", "Keuangan", "Administrasi", "Quality Control"] as $pilihan): ?>
                                <option value="<?= e($pilihan) ?>" <?= $divisi === $pilihan ? "selected" : "" ?>><?= e($pilihan) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="group">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input id="tanggal_masuk" name="tanggal_masuk" type="date" value="<?= e($tanggalMasuk) ?>" required>
                    </div>

                    <div class="group">
                        <label for="status">Status Karyawan</label>
                        <select id="status" name="status" required>
                            <?php foreach (["Aktif", "Cuti", "Nonaktif"] as $pilihan): ?>
                                <option value="<?= e($pilihan) ?>" <?= $status === $pilihan ? "selected" : "" ?>><?= e($pilihan) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn primary">Simpan Karyawan</button>
                    <a href="index.php" class="btn light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>
</main>
</div>
</body>
</html>
