<?php
session_start();
require_once 'conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])){
  echo json_encode(['status'=>'unauthenticated']);
  exit;
}

$user_id   = $_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));

/* --- Validasi produk & stok --- */
$product = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$product->bind_param("i", $product_id);
$product->execute();
$res = $product->get_result();
if (!$row = $res->fetch_assoc()){
  echo json_encode(['status'=>'error','message'=>'Produk tidak ditemukan']);
  exit;
}
$max_stock = (int)$row['stock'];

if ($quantity > $max_stock){
  echo json_encode(['status'=>'error','message'=>'Stok tidak mencukupi']);
  exit;
}

/* --- Update keranjang --- */
$stmt = $conn->prepare("
  UPDATE cart_items 
  SET quantity = ? 
  WHERE user_id = ? AND product_id = ?
");
$stmt->bind_param("iii", $quantity, $user_id, $product_id);

if ($stmt->execute()){
  echo json_encode(['status'=>'success']);
}else{
  echo json_encode(['status'=>'error','message'=>'DB gagal']);
}
