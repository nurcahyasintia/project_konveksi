<?php
require_once '../../config/database.php';
$id = (int)($_GET['id'] ?? 0);
if ($id < 1) header("Location: ../master-stok.php");
try {
    $pdo->prepare("DELETE FROM inventory_items WHERE id = ?")->execute([$id]);
    header("Location: ../master-stok.php?msg=Barang berhasil dihapus!");
} catch (PDOException $e) { header("Location: ../master-stok.php?err=Gagal menghapus: Barang mungkin memiliki riwayat transaksi."); }