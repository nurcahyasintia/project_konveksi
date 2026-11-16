<?php
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: ../barang-keluar.php");
$tanggal = $_POST['tanggal'] ?? date('Y-m-d'); $itemId = (int)($_POST['item_id'] ?? 0);
$qty = (float)($_POST['qty'] ?? 0); $referensi = trim($_POST['referensi'] ?? ''); $userId = $_SESSION['user_id'] ?? 1;
if ($itemId < 1 || $qty <= 0) header("Location: ../barang-keluar.php?err=Data tidak valid");
try {
    $stmt = $pdo->prepare("SELECT stok, nama FROM inventory_items WHERE id = ?"); $stmt->execute([$itemId]); $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) header("Location: ../barang-keluar.php?err=Barang tidak ditemukan");
    if ($item['stok'] < $qty) header("Location: ../barang-keluar.php?err=Stok " . htmlspecialchars($item['nama']) . " tidak cukup! Tersedia: " . $item['stok']);
    $pdo->prepare("UPDATE inventory_items SET stok = stok - ? WHERE id = ?")->execute([$qty, $itemId]);
    $pdo->prepare("INSERT INTO inventory_transactions (item_id, tipe, qty, referensi, tanggal, user_id) VALUES (?, 'Keluar', ?, ?, ?, ?)")->execute([$itemId, $qty, $referensi, $tanggal, $userId]);
    header("Location: ../barang-keluar.php?msg=Barang keluar berhasil disimpan!");
} catch (PDOException $e) { header("Location: ../barang-keluar.php?err=Gagal menyimpan"); }