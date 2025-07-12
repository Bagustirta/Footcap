<?php
require_once '../../../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
  $new_status = trim($_POST['status'] ?? '');

  // Ambil status lama
  $stmtOld = $conn->prepare("SELECT status FROM orders WHERE id = ?");
  $stmtOld->bind_param("i", $order_id);
  $stmtOld->execute();
  $resultOld = $stmtOld->get_result()->fetch_assoc();

  if (!$resultOld) {
    die("Pesanan tidak ditemukan.");
  }

  $old_status = $resultOld['status'];

  // Daftar status yang diizinkan
  $statuses = ['Menunggu Verifikasi', 'Sedang Diproses', 'Sedang Dikirim', 'Sudah Diterima', 'Selesai'];
  $old_index = array_search($old_status, $statuses);
  $new_index = array_search($new_status, $statuses);

  // Validasi apakah status valid
  if ($old_index === false || $new_index === false) {
    die("Status tidak valid.");
  }

  // Tidak boleh mundur ke status sebelumnya
  if ($new_index < $old_index) {
    die("Tidak dapat mengubah status ke tahap sebelumnya.");
  }

// Validasi pembayaran (untuk status tertentu)
if (in_array($new_status, ['Sedang Diproses', 'Sedang Dikirim', 'Sudah Diterima', 'Selesai'])) {
  $stmtCheckPay = $conn->prepare("
    SELECT COUNT(*) AS payment_count
    FROM payments
    WHERE order_id = ? AND validated_by_admin = 1
  ");
  $stmtCheckPay->bind_param("i", $order_id);
  $stmtCheckPay->execute();
  $payResult = $stmtCheckPay->get_result()->fetch_assoc();

  if (($payResult['payment_count'] ?? 0) < 1) {
    // Jika status baru adalah "Sedang Diproses", validasi otomatis
    if ($new_status === 'Sedang Diproses') {
      $stmtUpdateValidation = $conn->prepare("
        UPDATE payments SET validated_by_admin = 1 
        WHERE order_id = ? AND validated_by_admin = 0
      ");
      $stmtUpdateValidation->bind_param("i", $order_id);
      $stmtUpdateValidation->execute();
    } else {
      die("Pembayaran belum divalidasi oleh admin.");
    }
  }
}


  // Update status pesanan
  $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $new_status, $order_id);

  if ($stmt->execute()) {
    // Jika status menjadi "Sedang Dikirim", buat entri pengiriman jika belum ada
    if ($new_status === 'Sedang Dikirim') {
      $cek = $conn->prepare("SELECT id FROM shipments WHERE order_id = ?");
      $cek->bind_param("i", $order_id);
      $cek->execute();
      $cekResult = $cek->get_result();

      if ($cekResult->num_rows === 0) {
        $resi = 'TRX' . strtoupper(uniqid());
        $tgl_kirim = date('Y-m-d');

        $insert = $conn->prepare("
          INSERT INTO shipments (order_id, tracking_number, shipping_date)
          VALUES (?, ?, ?)
        ");
        $insert->bind_param("iss", $order_id, $resi, $tgl_kirim);
        $insert->execute();
      }
    }

    header("Location: ../../index.php?page=orders&success=1");
    exit;
  } else {
    die("Gagal mengubah status pesanan: " . $conn->error);
  }
} else {
  header("Location: ../../index.php?page=orders");
  exit;
}
