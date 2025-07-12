<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  $buyer_name = $_POST['buyer_name'];
  $address = $_POST['address'];

  $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity, buyer_name, address, created_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->bind_param("iiss", $product_id, $quantity, $buyer_name, $address);
  $stmt->execute();

  echo "<script>alert('Pesanan berhasil!'); location.href='index.php';</script>";
} else {
  echo "Metode tidak diizinkan.";
}
