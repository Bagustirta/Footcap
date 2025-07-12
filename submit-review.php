<?php
require_once 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_id = (int)$_POST['product_id'];
  $order_id = (int)$_POST['order_id'];
  $rating = (int)$_POST['rating'];
  $comment = trim($_POST['comment']);
  $variant = ''; // jika ada variasi
  $image_path = '';

  if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    die("Anda belum login.");
  }

  $user_id = $_SESSION['user_id'];
  $username = $_SESSION['user_name'];

  // Upload gambar jika ada
  if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $dir = 'uploads/reviews/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_path = $dir . uniqid('rev_', true) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  // Simpan review ke database
  $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, variant, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param("iiisss", $product_id, $user_id, $rating, $comment, $variant, $image_path);

  if (!$stmt->execute()) {
    die("Gagal menyimpan review: " . $conn->error);
  }

  // Cek apakah semua produk di order ini sudah direview
  $checkTotal = $conn->prepare("SELECT COUNT(*) as total_items FROM order_items WHERE order_id = ?");
  $checkTotal->bind_param("i", $order_id);
  $checkTotal->execute();
  $totalItems = $checkTotal->get_result()->fetch_assoc()['total_items'] ?? 0;

  $checkReviewed = $conn->prepare("SELECT COUNT(DISTINCT product_id) as reviewed_items FROM reviews WHERE user_id = ? AND product_id IN (SELECT product_id FROM order_items WHERE order_id = ?)");
  $checkReviewed->bind_param("ii", $user_id, $order_id);
  $checkReviewed->execute();
  $reviewedItems = $checkReviewed->get_result()->fetch_assoc()['reviewed_items'] ?? 0;

  // Jika semua produk sudah direview, ubah status ke 'selesai'
  if ($totalItems == $reviewedItems) {
    $update = $conn->prepare("UPDATE orders SET status = 'selesai' WHERE id = ?");
    $update->bind_param("i", $order_id);
    $update->execute();
  }

  header("Location: orders.php?review_success=1");
  exit;
} else {
  die("Permintaan tidak valid.");
}
?>
