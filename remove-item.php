<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'unauthenticated']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
  $user_id = $_SESSION['user_id'];
  $product_id = (int)$_POST['product_id'];

  $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
  $stmt->bind_param("ii", $user_id, $product_id);

  if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
  }
} else {
  echo json_encode(['status' => 'invalid']);
}
