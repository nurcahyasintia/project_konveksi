<?php
require_once '../../config/database.php';
header('Content-Type: application/json');
$itemId = (int)($_GET['item_id'] ?? 0);
if ($itemId < 1) { echo json_encode(['success' => false]); exit; }
try {
    $stmt = $pdo->prepare("SELECT stok FROM inventory_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'stok' => $item ? $item['stok'] : 0]);
} catch (PDOException $e) { echo json_encode(['success' => false]); }