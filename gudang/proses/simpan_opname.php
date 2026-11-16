<?php
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: ../opname.php");
$tanggal = $_POST['tanggal'] ?? date('Y-m-d'); $itemId = (int)($_POST['item_id'] ?? 0);
$stokSistem = (float)($_POST['stok_sistem_value'] ?? 0); $stokFisik = (float)($_POST['stok_fisik'] ?? 0);
$catatan = trim($_POST['catatan'] ?? ''); $userId = $_SESSION['user_id'] ?? 1;
if ($itemId < 1) header("Location: ../opname.php?err=Pilih bahan terlebih dahulu");
try {
    $selisih = $stokFisik - $stokSistem;
    $pdo->prepare("INSERT INTO stock_opname (item_id, tanggal, stok_sistem, stok_fisik, selisih, catatan, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute([$itemId, $tanggal, $stokSistem, $stokFisik, $selisih, $catatan, $userId]);
    $pdo->prepare("UPDATE inventory_items SET stok = ? WHERE id = ?")->execute([$stokFisik, $itemId]);
    if ($selisih != 0) {
        $tipe = ($selisih > 0) ? 'Masuk' : 'Keluar';
        $pdo->prepare("INSERT INTO inventory_transactions (item_id, tipe, qty, referensi, tanggal, user_id) VALUES (?, ?, ?, ?, ?, ?)")->execute([$itemId, $tipe, abs($selisih), 'OPNAME-' . date('Ymd'), $tanggal, $userId]);
    }
    header("Location: ../opname.php?msg=Stock opname berhasil disimpan!");
} catch (PDOException $e) { header("Location: ../opname.php?err=Gagal menyimpan: " . $e->getMessage()); }