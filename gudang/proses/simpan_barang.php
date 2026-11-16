<?php
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: ../barang-masuk.php");
$tanggal = $_POST['tanggal'] ?? date('Y-m-d'); $itemId = (int)($_POST['item_id'] ?? 0);
$qty = (float)($_POST['qty'] ?? 0); $referensi = trim($_POST['referensi'] ?? ''); $userId = $_SESSION['user_id'] ?? 1;
if ($itemId < 1 || $qty <= 0) header("Location: ../barang-masuk.php?err=Data tidak valid");
try {
    $pdo->prepare("UPDATE inventory_items SET stok = stok + ? WHERE id = ?")->execute([$qty, $itemId]);
    $pdo->prepare("INSERT INTO inventory_transactions (item_id, tipe, qty, referensi, tanggal, user_id) VALUES (?, 'Masuk', ?, ?, ?, ?)")->execute([$itemId, $qty, $referensi, $tanggal, $userId]);
    header("Location: ../barang-masuk.php?msg=Barang masuk berhasil disimpan!");
} catch (PDOException $e) { header("Location: ../barang-masuk.php?err=Gagal menyimpan"); }