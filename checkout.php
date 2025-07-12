<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil ID produk yang dipilih
$selected_ids = $_POST['selected'] ?? [];
$quantities   = $_POST['quantity'] ?? [];

if (empty($selected_ids)) {
  echo "Tidak ada produk yang dipilih.";
  exit;
}

// Ambil alamat user dari DB
$stmt = $conn->prepare("SELECT alamat FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$alamat = $user['alamat'] ?? '';

// Siapkan list produk terpilih
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
$types = str_repeat('i', count($selected_ids));
$stmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$selected_ids);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
  $pid = $row['id'];
  $qty = isset($quantities[$pid]) ? (int)$quantities[$pid] : 1;
  $qty = max(1, min($qty, $row['stock']));
  $subtotal = $row['price'] * $qty;
  $total += $subtotal;

  $items[] = [
    'product_id' => $pid,
    'name' => $row['name'],
    'price' => $row['price'],
    'quantity' => $qty,
    'subtotal' => $subtotal
  ];
}

// Tangani submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alamat']) && isset($_FILES['payment_proof'])) {
  $alamat = trim($_POST['alamat']);
  $payment_proof = $_FILES['payment_proof'];

  if (empty($alamat) || $payment_proof['error'] !== UPLOAD_ERR_OK) {
    $error = "Alamat atau bukti pembayaran wajib diisi.";
  } else {
    // Simpan alamat jika belum ada
    if (empty($user['alamat'])) {
      $stmt = $conn->prepare("UPDATE users SET alamat = ? WHERE id = ?");
      $stmt->bind_param("si", $alamat, $user_id);
      $stmt->execute();
    }

    // Upload bukti pembayaran
    $upload_dir = "uploads/payments/";
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    $file_ext = pathinfo($payment_proof['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('pay_', true) . '.' . $file_ext;
    move_uploaded_file($payment_proof['tmp_name'], $upload_dir . $file_name);

    // Simpan order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, order_date) VALUES (?, ?, 'menunggu verifikasi', NOW())");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Simpan order_items
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
      $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
      $item_stmt->execute();
    }

    // Kurangi stok produk
    $update_stock_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    foreach ($items as $item) {
      $qty = $item['quantity'];
      $pid = $item['product_id'];
      $update_stock_stmt->bind_param("ii", $qty, $pid);
      $update_stock_stmt->execute();
    }


    // Simpan pembayaran
    $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_date, payment_proof, amount)VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("isd", $order_id, $file_name, $total);  // 'd' untuk double (amount)
    $stmt->execute();


    // Hapus item dari cart
    $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
    $types = 'i' . str_repeat('i', count($selected_ids));
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id IN ($placeholders)");
    $stmt->bind_param($types, $user_id, ...$selected_ids);
    $stmt->execute();

    header("Location: checkout-success.php");
    exit;
  }
}
?>

<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<main class="container py-5">
  <h2 class="mb-4">Checkout</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <?php foreach ($selected_ids as $id): ?>
      <input type="hidden" name="selected[]" value="<?= $id ?>">
    <?php endforeach; ?>
    <?php foreach ($quantities as $pid => $qty): ?>
      <input type="hidden" name="quantity[<?= $pid ?>]" value="<?= $qty ?>">
    <?php endforeach; ?>

    <div class="row">
      <div class="col-md-8">
        <!-- Alamat -->
        <div class="mb-3">
          <label class="form-label fw-bold">Alamat Pengiriman</label>
          <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($alamat) ?></textarea>
        </div>

        <!-- Produk -->
        <div class="mb-4">
          <h5 class="mb-3">Produk Dipesan</h5>
          <?php foreach ($items as $item): ?>
            <div class="d-flex justify-content-between mb-2">
              <div><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></div>
              <div>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Bukti Bayar -->
        <div class="mb-3">
          <label class="form-label fw-bold">Upload Bukti Pembayaran</label>
          <input type="file" name="payment_proof" class="form-control" accept="image/*" required>
        </div>
      </div>

      <!-- Ringkasan -->
      <div class="col-md-4">
        <div class="bg-white shadow-sm p-4 rounded">
          <h5 class="fw-bold mb-3">Ringkasan Transaksi</h5>
          <div class="d-flex justify-content-between">
            <span>Total</span>
            <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong>
          </div>
          <hr>
          <button type="submit" class="btn btn-success w-100 mt-2">Bayar Sekarang</button>
        </div>
      </div>
    </div>
  </form>
</main>

