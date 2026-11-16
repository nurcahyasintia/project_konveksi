<?php
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: ../riwayat.php");
$jobId = (int)($_POST['job_id'] ?? 0); $itemId = (int)($_POST['item_id'] ?? 0);
$qty = (float)($_POST['qty'] ?? 0); $userId = $_SESSION['user_id'] ?? 1;
if ($itemId < 1 || $qty <= 0) header("Location: ../riwayat.php?err=Data tidak valid");
try {
    $stmt = $pdo->prepare("SELECT stok, nama FROM inventory_items WHERE id = ?"); $stmt->execute([$itemId]); $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) header("Location: ../riwayat.php?err=Barang tidak ditemukan");
    if ($item['stok'] < $qty) header("Location: ../riwayat.php?err=Stok " . htmlspecialchars($item['nama']) . " tidak cukup!");
    $pdo->prepare("UPDATE inventory_items SET stok = stok - ? WHERE id = ?")->execute([$qty, $itemId]);
    $pdo->prepare("INSERT INTO inventory_transactions (item_id, tipe, qty, referensi, tanggal, user_id) VALUES (?, 'Pemakaian', ?, ?, CURDATE(), ?)")->execute([$itemId, $qty, 'JOB-' . $jobId, $userId]);
    header("Location: ../riwayat.php?msg=Pemakaian bahan berhasil dicatat!");
} catch (PDOException $e) { header("Location: ../riwayat.php?err=Gagal menyimpan"); }