<?php
session_start();
require_once "../config/database.php";

// ==========================================
// VARIABEL SIDEBAR UNIVERSAL
// ==========================================
$base_path   = '../';
$active_page = 'pesanan';
// ==========================================

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

$old_input = [
    'nomor_order'       => $data['nomor_order'],
    'customer_id'       => $data['customer_id'],
    'tanggal'           => $data['tanggal'],
    'deadline'          => $data['deadline'],
    'total'             => $data['total'],
    'dp'                => $data['dp'],
    'status_pembayaran' => $data['status_pembayaran'],
    'status_produksi'   => $data['status_produksi']
];

if (isset($_POST['simpan'])) {
    $old_input['nomor_order']       = trim($_POST['nomor_order'] ?? "");
    $old_input['customer_id']       = (int) ($_POST['customer_id'] ?? 0);
    $old_input['tanggal']           = $_POST['tanggal'] ?? "";
    $old_input['deadline']          = $_POST['deadline'] ?? "";
    $old_input['total']             = $_POST['total'] ?? "";
    $old_input['dp']                = $_POST['dp'] ?? "";
    $old_input['status_pembayaran'] = $_POST['status_pembayaran'] ?? "";
    $old_input['status_produksi']   = $_POST['status_produksi'] ?? "";

    $status_pembayaran_valid = ["belum_lunas", "dp", "lunas"];
    $status_produksi_valid = ["menunggu", "proses", "selesai", "dikirim"];

    if ($old_input['nomor_order'] === "" || $old_input['customer_id'] < 1 || $old_input['tanggal'] === "" || $old_input['deadline'] === "" || $old_input['total'] === "" || $old_input['dp'] === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!is_numeric($old_input['total']) || !is_numeric($old_input['dp']) || (float)$old_input['total'] < 0 || (float)$old_input['dp'] < 0) {
        $error = "Total harga dan DP harus berupa angka minimal 0.";
    } elseif ((float)$old_input['dp'] > (float)$old_input['total']) {
        $error = "DP tidak boleh melebihi total harga.";
    } elseif (!in_array($old_input['status_pembayaran'], $status_pembayaran_valid, true) || !in_array($old_input['status_produksi'], $status_produksi_valid, true)) {
        $error = "Status pesanan tidak valid.";
    } else {
        $cek = mysqli_prepare($conn, "SELECT id FROM customers WHERE id = ?");
        mysqli_stmt_bind_param($cek, "i", $old_input['customer_id']);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);
        $pelanggan_ada = mysqli_num_rows($hasil_cek) > 0;
        mysqli_stmt_close($cek);

        if (!$pelanggan_ada) {
            $error = "Pelanggan yang dipilih tidak ditemukan.";
        } else {
            $total_db = (float)$old_input['total'];
            $dp_db = (float)$old_input['dp'];

            $update = mysqli_prepare($conn, "UPDATE orders SET nomor_order = ?, customer_id = ?, tanggal = ?, deadline = ?, total = ?, dp = ?, status_pembayaran = ?, status_produksi = ? WHERE id = ?");

            if ($update) {
                mysqli_stmt_bind_param($update, "sissddssi", $old_input['nomor_order'], $old_input['customer_id'], $old_input['tanggal'], $old_input['deadline'], $total_db, $dp_db, $old_input['status_pembayaran'], $old_input['status_produksi'], $id);

                if (mysqli_stmt_execute($update)) {
                    mysqli_stmt_close($update);
                    header("Location: index.php?pesan=edit");
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

$customers = mysqli_query($conn, "SELECT id, kode, nama FROM customers ORDER BY nama ASC");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 1. CSS GLOBAL & SIDEBAR (UNIFIED) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-bg: #1E293B; --sidebar-active: #3B82F6;
            --primary-blue: #2563EB; --main-bg: #F8FAFC;
            --text-dark: #1E293B; --text-light: #94A3B8;
            --white: #FFFFFF; --border-color: #E2E8F0;
        }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--main-bg); color: var(--text-dark); }
        a { text-decoration: none; }
        .app { display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; min-width: 250px; background: var(--sidebar-bg); color: var(--white); display: flex; flex-direction: column; padding: 25px 16px 18px; }
        .logo { display: flex; align-items: center; gap: 10px; padding: 0 12px; margin-bottom: 38px; font-size: 23px; font-weight: 800; color: var(--white); }
        .logo i { color: var(--primary-blue); font-size: 25px; }
        .logo span { color: #60A5FA; }
        .nav-title { color: var(--text-light); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; padding: 0 13px; margin-bottom: 12px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 13px 14px; margin-bottom: 7px; color: #CBD5E1; font-size: 14px; font-weight: 500; border-radius: 9px; transition: .2s; }
        .nav-link i { width: 20px; text-align: center; font-size: 16px; }
        .nav-link:hover { background: #334155; color: var(--white); }
        .nav-link.active { background: var(--sidebar-active); color: var(--white); box-shadow: 0 4px 12px rgba(59, 130, 246, .2); }
        .sidebar-footer { display: flex; align-items: center; gap: 11px; border-top: 1px solid #334155; padding: 20px 8px 0; margin-top: auto; }
        .avatar { width: 42px; height: 42px; min-width: 42px; border-radius: 50%; background: var(--primary-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; flex-shrink: 0; }
        .user-info { min-width: 0; }
        .user-name { font-size: 12px; font-weight: 700; color: var(--white); line-height: 1.5; overflow-wrap: anywhere; }
        .user-role { font-size: 11px; color: var(--text-light); margin-top: 3px; }
        
        .main { flex: 1; min-width: 0; }
        .topbar { min-height: 90px; background: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .topbar h1 { font-size: 23px; color: var(--text-dark); margin-bottom: 7px; }
        .breadcrumb { color: var(--text-light); font-size: 12px; }
        .breadcrumb span { color: var(--primary-blue); }
        .topbar-label { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #EFF6FF; border-radius: 9px; color: var(--primary-blue); font-size: 12px; font-weight: 700; }
        .content { padding: 30px 32px; }

        /* 2. CSS KHUSUS FORM PESANAN */
        .form-panel { max-width: 850px; background: var(--white); border: 1px solid var(--border-color); border-radius: 14px; padding: 28px; box-shadow: 0 4px 16px rgba(15, 23, 42, .04); }
        .form-heading { margin-bottom: 25px; padding-bottom: 18px; border-bottom: 1px solid var(--border-color); }
        .form-heading h3 { font-size: 18px; color: var(--text-dark); margin-bottom: 7px; }
        .form-heading h3 i { color: var(--primary-blue); margin-right: 5px; }
        .form-heading p { font-size: 13px; color: var(--text-light); line-height: 1.6; }
        
        .alert { padding: 13px 15px; border-radius: 9px; margin-bottom: 20px; font-size: 13px; line-height: 1.6; background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { min-width: 0; margin-bottom: 21px; }
        .form-group label { display: block; color: var(--text-dark); font-size: 13px; font-weight: 700; margin-bottom: 9px; }
        .required { color: #DC2626; }
        .form-group input, .form-group select {
            display: block; width: 100%; padding: 13px 14px; border: 1px solid var(--border-color); border-radius: 9px;
            background: var(--white); color: var(--text-dark); font-family: inherit; font-size: 13px; outline: none; transition: .2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-color); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 18px; border: none; border-radius: 9px; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: .2s; text-decoration: none; }
        .btn-save { background: var(--primary-blue); color: var(--white); }
        .btn-save:hover { background: #1D4ED8; }
        .btn-back { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); }
        .btn-back:hover { background: #F8FAFC; }
        
        .footer-note { text-align: center; color: var(--text-light); font-size: 11px; padding: 24px 10px 5px; }
        @media (max-width: 650px) { .app { flex-direction: column; } .sidebar { width: 100%; min-width: 0; } .form-grid { grid-template-columns: 1fr; } .form-actions { flex-direction: column-reverse; } .btn { width: 100%; } }
    </style>
</head>
<body>
<div class="app">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main">
        <header class="topbar">
            <div>
                <h1>Edit Pesanan</h1>
                <div class="breadcrumb">Beranda / Pesanan / <span>Edit</span></div>
            </div>
            <div class="topbar-label"><i class="fa-solid fa-circle-check"></i> Sistem Konveksi</div>
        </header>

        <section class="content">
            <div class="form-panel">
                <div class="form-heading">
                    <h3><i class="fa-solid fa-pen-to-square"></i> Form Edit Pesanan</h3>
                    <p>Pastikan data sudah benar sebelum menyimpan perubahan.</p>
                </div>

                <?php if ($error !== ""): ?>
                    <div class="alert"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nomor_order">Nomor Pesanan <span class="required">*</span></label>
                            <input type="text" id="nomor_order" name="nomor_order" value="<?= e($old_input['nomor_order']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="customer_id">Pelanggan <span class="required">*</span></label>
                            <select id="customer_id" name="customer_id" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                                    <option value="<?= (int)$customer['id']; ?>" <?= (int)$customer['id'] === (int)$old_input['customer_id'] ? 'selected' : ''; ?>>
                                        <?= e($customer['kode']); ?> - <?= e($customer['nama']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal">Tanggal Pesanan <span class="required">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" value="<?= e($old_input['tanggal']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="deadline">Batas Waktu Pengerjaan <span class="required">*</span></label>
                            <input type="date" id="deadline" name="deadline" value="<?= e($old_input['deadline']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="total">Total Harga (Rp) <span class="required">*</span></label>
                            <input type="number" id="total" name="total" min="0" step="1" value="<?= e($old_input['total']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="dp">Uang Muka / DP (Rp) <span class="required">*</span></label>
                            <input type="number" id="dp" name="dp" min="0" step="1" value="<?= e($old_input['dp']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select id="status_pembayaran" name="status_pembayaran" required>
                                <option value="belum_lunas" <?= $old_input['status_pembayaran'] === 'belum_lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                                <option value="dp" <?= $old_input['status_pembayaran'] === 'dp' ? 'selected' : ''; ?>>DP</option>
                                <option value="lunas" <?= $old_input['status_pembayaran'] === 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status_produksi">Status Produksi</label>
                            <select id="status_produksi" name="status_produksi" required>
                                <option value="menunggu" <?= $old_input['status_produksi'] === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="proses" <?= $old_input['status_produksi'] === 'proses' ? 'selected' : ''; ?>>Proses</option>
                                <option value="selesai" <?= $old_input['status_produksi'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="dikirim" <?= $old_input['status_produksi'] === 'dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                        <button type="submit" name="simpan" class="btn btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
            <div class="footer-note">&copy; <?= date('Y'); ?> KonveksiKu — Sistem Manajemen Konveksi</div>
        </section>
    </main>
</div>
</body>
</html>
