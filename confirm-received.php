<?php
require_once 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
  $order_id = (int) $_POST['order_id'];
  $user_id = $_SESSION['user_id'] ?? 0;

  // Pastikan pesanan milik user yang sedang login
  $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $order_id, $user_id);
  $stmt->execute();
  $order = $stmt->get_result()->fetch_assoc();

  if (!$order) {
    die('Pesanan tidak ditemukan atau bukan milik Anda.');
  }

  // Update status di tabel orders
  $stmt = $conn->prepare("UPDATE orders SET status = 'Sudah Diterima' WHERE id = ?");
  $stmt->bind_param("i", $order_id);
  $stmt->execute();

  // Update received_by_user = 1 di tabel shipments
  $stmt2 = $conn->prepare("UPDATE shipments SET received_by_user = 1 WHERE order_id = ?");
  $stmt2->bind_param("i", $order_id);
  $stmt2->execute();

  header("Location: orders.php");
  exit;
}
?>
