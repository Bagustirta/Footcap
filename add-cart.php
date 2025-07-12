<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'] ?? null;
$quantity = $_GET['quantity'] ?? 1;

$product_result = $conn->query("SELECT id, stock FROM products WHERE id = $product_id");
$product = $product_result->fetch_assoc();
if (!$product) {
  die("Produk tidak ditemukan.");
}

$quantity = max(1, (int)$quantity);

// Cek apakah item sudah ada di cart
$check = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {
  // Update jumlah
  $new_qty = $row['quantity'] + $quantity;
  $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
  $update->bind_param("iii", $new_qty, $user_id, $product_id);
  $update->execute();
} else {
  // Tambahkan baru
  $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
  $insert->bind_param("iii", $user_id, $product_id, $quantity);
  $insert->execute();
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'cart.php'));
exit;
