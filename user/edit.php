<?php
session_start();
require_once "../config/database.php";
require_once "../auth/role_check.php";

// Hanya Admin dan Owner yang boleh edit
requireRole(['Admin', 'Owner']);

$id = (int) ($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data user saat ini
$stmt = $conn->prepare("SELECT id, nama, email, role_id, status FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}

// Ambil daftar roles untuk dropdown
$roles = $conn->query("SELECT id, nama_role FROM roles ORDER BY id ASC");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role_id = (int) ($_POST["role_id"] ?? 0);
    $status = trim($_POST["status"] ?? "aktif");
    $password_baru = $_POST["password_baru"] ?? "";

    if ($nama === "" || $email === "" || $role_id <= 0) {
        $error = "Nama, Email, dan Role wajib diisi.";
    } elseif (!in_array($status, ["aktif", "nonaktif"], true)) {
        $error = "Status tidak valid.";
    } else {
        // Cek duplikasi email (kecuali untuk user yang sedang diedit ini)
        $cek = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $cek->bind_param("si", $email, $id);
        $cek->execute();
        $email_ada = $cek->get_result()->fetch_assoc();
        $cek->close();

        if ($email_ada) {
            $error = "Email sudah digunakan oleh user lain.";
        } else {
            // Siapkan query update
            if ($password_baru !== "") {
                // Jika password diisi, hash dan update termasuk password
                if (strlen($password_baru) < 6) {
                    $error = "Password minimal 6 karakter.";
                } else {
                    $hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role_id=?, status=?, password_hash=? WHERE id=?");
                    $stmt->bind_param("ssissi", $nama, $email, $role_id, $status, $hash, $id);
                }
            } else {
                // Jika password kosong, JANGAN update password_hash
                $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role_id=?, status=? WHERE id=?");
                $stmt->bind_param("ssisi", $nama, $email, $role_id, $status, $id);
            }

            if (empty($error) && $stmt->execute()) {
                $stmt->close();

                // Audit Log
                $uid = (int) $_SESSION["user_id"];
                $aktivitas = "Mengubah data akun pengguna";
                $referensi = "User ID: " . $id . " (" . $email . ")";
                $log = $conn->prepare("INSERT INTO activity_logs (user_id, aktivitas, referensi, waktu) VALUES (?, ?, ?, NOW())");
                if ($log) {
                    $log->bind_param("iss", $uid, $aktivitas, $referensi);
                    $log->execute();
                    $log->close();
                }

                header("Location: index.php?pesan=berhasil_edit");
                exit;
            } else {
                $error = "Gagal menyimpan perubahan.";
                if (isset($stmt)) $stmt->close();
            }
        }
    }
    
    // Update variabel user dengan input terbaru agar form tidak reset
    $user["nama"] = $nama;
    $user["email"] = $email;
    $user["role_id"] = $role_id;
    $user["status"] = $status;
}

function e($val) { return htmlspecialchars((string)($val ?? ""), ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User - KonveksiKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* ============================================================
           KONVEKSIKU THEME — CSS INTERNAL (Edit User)
           ============================================================ */
        :root {
            --primary: #2563eb; --primary-dark: #1d4ed8; --primary-light: #dbeafe;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
            --info: #06b6d4; --purple: #8b5cf6; --dark: #0f172a;
            --gray-50: #f8fafc; --gray-100: #f1f5f9; --gray-200: #e2e8f0;
            --gray-300: #cbd5e1; --gray-400: #94a3b8; --gray-500: #64748b;
            --gray-600: #475569; --gray-700: #334155; --gray-800: #1e293b; --gray-900: #0f172a;
            --radius: 14px; --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--gray-100); color: var(--gray-800);
            line-height: 1.6; -webkit-font-smoothing: antialiased;
            padding: 24px;
        }
        .container { max-width: 700px; margin: 0 auto; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
        }
        .page-header h1 {
            font-size: 22px; font-weight: 800; color: var(--gray-900);
            letter-spacing: -.3px;
        }
        .page-header .breadcrumb { font-size: 13px; color: var(--gray-500); margin-top: 2px; }

        /* ===== NAVIGASI ===== */
        .nav-links {
            display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 22px;
        }
        .nav-links a {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: var(--radius-sm);
            background: #fff; color: var(--gray-600);
            text-decoration: none; font-size: 13px; font-weight: 600;
            border: 1px solid var(--gray-300); transition: all .2s;
        }
        .nav-links a:hover {
            background: var(--primary-light); color: var(--primary-dark);
            border-color: var(--primary);
        }

        /* ===== CARD ===== */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); border: 1px solid var(--gray-200);
            overflow: hidden; margin-bottom: 22px;
        }
        .card-header {
            padding: 18px 22px; border-bottom: 1px solid var(--gray-200);
            font-size: 15px; font-weight: 700; color: var(--gray-900);
            letter-spacing: -.2px;
        }
        .card-body { padding: 22px; }

        /* ===== FORM ===== */
        .form-group { margin-bottom: 18px; display: flex; flex-direction: column; gap: 6px; }
        .form-group label {
            font-size: 12.5px; font-weight: 600; color: var(--gray-700);
        }
        .form-group input,
        .form-group select {
            padding: 10px 14px; border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 13.5px;
            font-family: inherit; color: var(--gray-800);
            transition: all .2s; background: #fff; width: 100%;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .form-hint {
            font-size: 11.5px; color: var(--gray-500); margin-top: 2px;
        }

        /* ===== ALERT ===== */
        .alert {
            padding: 14px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 500; margin-bottom: 18px;
            border-left: 3px solid var(--danger);
            background: #fee2e2; color: #991b1b;
        }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: var(--radius-sm);
            font-size: 13.5px; font-weight: 600; border: none;
            cursor: pointer; transition: all .2s; text-decoration: none;
            font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); box-shadow: 0 6px 16px rgba(37,99,235,.35); }
        .btn-outline { background: #fff; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-outline:hover { background: var(--gray-50); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body { padding: 16px; }
            .page-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <div>
            <h1>Edit Akun Pengguna</h1>
            <div class="breadcrumb">Home / Manajemen User / Edit</div>
        </div>
    </div>

    <!-- ===== NAVIGASI ===== -->
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Daftar User</a>
        <a href="../index.php"><i class="fas fa-th-large"></i> Dashboard</a>
    </div>

    <!-- ===== ALERT ERROR ===== -->
    <?php if ($error !== ""): ?>
        <div class="alert">
            <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- ===== FORM EDIT ===== -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-edit" style="color:var(--primary);margin-right:6px"></i>
            Form Edit Akun
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= e($user["nama"]) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($user["email"]) ?>" required>
                </div>

                <div class="form-group">
                    <label for="role_id">Role / Hak Akses</label>
                    <select id="role_id" name="role_id" required>
                        <?php 
                        $roles->data_seek(0);
                        while ($r = $roles->fetch_assoc()): 
                        ?>
                            <option value="<?= (int)$r["id"] ?>" <?= $user["role_id"] == $r["id"] ? "selected" : "" ?>>
                                <?= e($r["nama_role"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="aktif" <?= $user["status"] === "aktif" ? "selected" : "" ?>>Aktif</option>
                        <option value="nonaktif" <?= $user["status"] === "nonaktif" ? "selected" : "" ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Minimal 6 karakter. Biarkan kosong untuk mempertahankan password lama.
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:20px">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>