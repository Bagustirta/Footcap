<?php
session_start();
require_once 'conn.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

// Validasi input produk
$product_stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
  echo "Produk tidak ditemukan.";
  exit;
}

// Pastikan jumlah tidak melebihi stok
$stock = (int)$product['stock'];
if ($quantity > $stock) {
  $quantity = $stock;
}

// Cek apakah produk sudah ada di keranjang user
$check_stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
  // Jika sudah ada, langsung arahkan ke halaman keranjang tanpa menambah
  header("Location: cart.php");
  exit;
} else {
  // Jika belum ada, masukkan ke cart lalu arahkan ke keranjang
  $insert_stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
  $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
  $insert_stmt->execute();

  header("Location: cart.php");
  exit;
}
