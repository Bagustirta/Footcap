<?php
require_once '.././conn.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die('ID pesanan tidak valid.');
}

$order_id = (int)$_GET['id'];

// Ambil data pesanan
$stmt = $conn->prepare("
  SELECT o.*, u.name AS user_name, s.tracking_number, s.shipping_date, s.received_by_user
  FROM orders o
  JOIN users u ON o.user_id = u.id
  LEFT JOIN shipments s ON o.id = s.order_id
  WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  die('Pesanan tidak ditemukan.');
}

// Ambil detail item pesanan
$stmtItems = $conn->prepare("
  SELECT p.name, p.image_url, p.price, oi.quantity
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = ?
");
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);

// Ambil bukti pembayaran jika status menunggu verifikasi
$payment = null;
if ($order['status'] === 'Menunggu Verifikasi') {
  $stmtPay = $conn->prepare("SELECT payment_proof FROM payments WHERE order_id = ?");
  $stmtPay->bind_param("i", $order_id);
  $stmtPay->execute();
  $payment = $stmtPay->get_result()->fetch_assoc();
}
?>

 <!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
<div class="flex flex-wrap mt-6 -mx-3 h-full">
  <!-- Sales overview (8/12) -->
  <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
        <div class="h-full border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
        <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-0">
          <h6 class="capitalize dark:text-white">Detail Pesanan</h6>
        </div>
        <div class="flex-auto p-4">
  <div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap -mx-3">
    <div class="w-full max-w-full px-3">
      <div class="bg-white dark:bg-slate-850 shadow-xl rounded-2xl p-6">
        <h6 class="text-lg font-semibold mb-4 dark:text-white">Detail Pesanan</h6>

        <div class="text-sm text-slate-700 dark:text-white space-y-2">
          <p><strong>Nama Pengguna:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
          <p><strong>Tanggal Order:</strong> <?= date('d M Y, H:i', strtotime($order['order_date'])) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
          <p><strong>Total Harga:</strong> Rp<?= number_format($order['total_price'], 0, ',', '.') ?></p>
        </div>

        <h6 class="mt-6 mb-3 font-semibold text-slate-700 dark:text-white">Produk dalam Pesanan:</h6>
        <?php foreach ($items as $item): ?>
          <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg p-3 mb-3 bg-gray-50 dark:bg-slate-800">
            <img src="../<?= htmlspecialchars($item['image_url'] ?? 'uploads/default-product.png') ?>" class="w-16 h-16 object-cover rounded mr-4">
            <div>
              <p class="font-semibold text-slate-800 dark:text-white"><?= htmlspecialchars($item['name']) ?></p>
              <p class="text-slate-600 dark:text-slate-300"><?= $item['quantity'] ?> x Rp<?= number_format($item['price'], 0, ',', '.') ?></p>
            </div>
          </div>
        <?php endforeach; ?>

        <h6 class="mt-6 mb-2 font-semibold text-slate-700 dark:text-white">Pengiriman:</h6>
        <?php if ($order['tracking_number']): ?>
          <?php
          $status_pengiriman = match ($order['status']) {
            'Sedang Dikirim' => 'Sedang Dikirim',
            'Sudah Diterima', 'Selesai' => 'Sudah Diterima',
            default => 'Belum Dikirim'
          };
          ?>
          <div class="text-sm text-slate-700 dark:text-white space-y-2">
            <p><strong>No. Resi:</strong> <?= htmlspecialchars($order['tracking_number']) ?></p>
            <p><strong>Status Pengiriman:</strong> <?= $status_pengiriman ?></p>
            <p><strong>Tanggal Kirim:</strong> <?= $order['shipping_date'] ? date('d M Y', strtotime($order['shipping_date'])) : '-' ?></p>
            <p><strong>Diterima Oleh:</strong> <?= htmlspecialchars($order['received_by_user'] ?: '-') ?></p>
          </div>
        <?php else: ?>
          <p class="text-sm italic text-gray-500 dark:text-gray-400">Belum ada informasi pengiriman.</p>
        <?php endif; ?>

        <?php if ($order['status'] === 'Menunggu Verifikasi' && $payment): ?>
          <h6 class="mt-6 mb-2 font-semibold text-slate-700 dark:text-white">Bukti Pembayaran:</h6>
          <div>
            <img src="../uploads/payments/<?= htmlspecialchars($payment['payment_proof']) ?>"
                 alt="Bukti Pembayaran"
                 class="border rounded shadow max-w-xs">
          </div>
        <?php endif; ?>

        <form method="post" action="content/orders/update-order.php" class="mt-6">
          <input type="hidden" name="order_id" value="<?= $order_id ?>">
          <div class="mb-4">
            <label for="status" class="block mb-2 text-sm font-medium text-slate-700 dark:text-white">Ubah Status Pesanan:</label>
            <select name="status" id="status" class="form-select w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
              <?php
                $statuses = ['Menunggu Verifikasi', 'Sedang Diproses', 'Sedang Dikirim', 'Sudah Diterima', 'Selesai'];
                $currentIndex = array_search($order['status'], $statuses);
                for ($i = $currentIndex; $i <= $currentIndex + 1 && $i < count($statuses); $i++) {
                  $selected = $i === $currentIndex ? 'selected' : '';
                  echo "<option value=\"{$statuses[$i]}\" $selected>{$statuses[$i]}</option>";
                }
              ?>
            </select>
          </div>
          <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Simpan Perubahan</button>
            <a href="index.php?page=orders" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 text-sm">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
