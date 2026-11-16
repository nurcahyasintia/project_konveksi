<?php
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: ../master-stok.php");
$id = (int)($_POST['id'] ?? 0); $kode = trim($_POST['kode'] ?? ''); $nama = trim($_POST['nama'] ?? '');
$kategori = trim($_POST['kategori'] ?? ''); $satuan = trim($_POST['satuan'] ?? ''); $stok = (int)($_POST['stok'] ?? 0); $stok_minimum = (int)($_POST['stok_minimum'] ?? 0);
if ($id < 1) header("Location: ../master-stok.php");
try {
    $pdo->prepare("UPDATE inventory_items SET kode=?, nama=?, kategori=?, satuan=?, stok=?, stok_minimum=? WHERE id=?")->execute([$kode, $nama, $kategori, $satuan, $stok, $stok_minimum, $id]);
    header("Location: ../master-stok.php?msg=Data barang berhasil diperbarui!");
} catch (PDOException $e) { header("Location: ../edit-barang.php?id=$id&err=Gagal memperbarui data"); }