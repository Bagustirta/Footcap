<?php
session_start();
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if (!$product_id) {
  echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
  exit;
}

// Simpan wishlist di session
if (!isset($_SESSION['wishlist'])) {
  $_SESSION['wishlist'] = [];
}

// Tambah jika belum ada
if (!in_array($product_id, $_SESSION['wishlist'])) {
  $_SESSION['wishlist'][] = $product_id;
}

echo json_encode([
  'status' => 'success',
  'count' => count($_SESSION['wishlist'])
]);
