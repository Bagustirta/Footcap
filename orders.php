<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  SELECT 
    o.id AS order_id,
    o.total_price,
    o.status,
    o.order_date,
    s.shipping_date,
    s.tracking_number,
    s.received_by_user,
    GROUP_CONCAT(CONCAT(p.name, ' x ', oi.quantity) SEPARATOR '\n') AS product_list
  FROM orders o
  LEFT JOIN order_items oi ON o.id = oi.order_id
  LEFT JOIN products p ON oi.product_id = p.id
  LEFT JOIN shipments s ON o.id = s.order_id
  WHERE o.user_id = ?
  GROUP BY o.id
  ORDER BY o.id DESC
");

if (!$stmt) {
  die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<main class="container py-5">
  <h2 class="mb-4">Daftar Pesanan Saya</h2>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info">Belum ada pesanan.</div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($orders as $row): ?>
        <?php
          $status = strtolower(trim($row['status'] ?? ''));
          $badgeColor = match ($status) {
            'menunggu verifikasi' => 'secondary',
            'sedang diproses' => 'warning',
            'sedang dikirim' => 'info',
            'sudah diterima' => 'primary',
            'selesai' => 'success',
            default => 'dark'
          };

          $productLines = explode("\n", $row['product_list']);

          $allReviewed = true; // Asumsinya semua sudah direview
          $productIDs = [];

          foreach ($productLines as $line) {
            preg_match('/^(.*?) x (\d+)$/', trim($line), $matches);
            $productName = $matches[1] ?? $line;

            $stmtPid = $conn->prepare("SELECT id FROM products WHERE name = ?");
            $stmtPid->bind_param("s", $productName);
            $stmtPid->execute();
            $resPid = $stmtPid->get_result()->fetch_assoc();
            $productId = $resPid['id'] ?? 0;
            $productIDs[] = $productId;

            $stmtReview = $conn->prepare("SELECT COUNT(*) AS count FROM reviews WHERE product_id = ? AND user_id = ?");
            $stmtReview->bind_param("ii", $productId, $user_id);
            $stmtReview->execute();
            $resReview = $stmtReview->get_result()->fetch_assoc();

            if (($resReview['count'] ?? 0) == 0) {
              $allReviewed = false;
              break;
            }
          }

          if ($status === 'sudah diterima' && $allReviewed) {
            $stmtUpdate = $conn->prepare("UPDATE orders SET status = 'selesai' WHERE id = ?");
            $stmtUpdate->bind_param("i", $row['order_id']);
            $stmtUpdate->execute();
            $status = 'selesai'; // untuk update tampilan langsung
          }
        ?>

        <div class="col-md-12 mb-4">
          <div class="card shadow-sm border">
            <div class="card-body" style="font-size: 1rem;">
              <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="d-flex align-items-center text-muted">
                  <span>Belanja • <?= date('d M Y', strtotime($row['order_date'])) ?></span>
                  <span class="badge bg-<?= $badgeColor ?> ms-2"><?= ucwords($status ?: 'Tidak diketahui') ?></span>
                </div>
                <div class="text-success fw-bold small mt-2 mt-md-0">
                  INV/<?= date('Ymd', strtotime($row['order_date'])) ?>/MPL/<?= $row['order_id'] ?>
                </div>
              </div>

              <hr class="my-3">

              <?php foreach ($productLines as $line): 
                preg_match('/^(.*?) x (\d+)$/', trim($line), $matches);
                $productName = $matches[1] ?? $line;
                $productQty = $matches[2] ?? 1;

                $imgPath = "uploads/default-product.png";
                $unitPrice = 0;
                $stmtImg = $conn->prepare("SELECT image_url, price FROM products WHERE name = ?");
                $stmtImg->bind_param("s", $productName);
                $stmtImg->execute();
                $resImg = $stmtImg->get_result()->fetch_assoc();
                if ($resImg) {
                  $imgPath = $resImg['image_url'];
                  $unitPrice = $resImg['price'];
                }
              ?>
                <div class="d-flex justify-content-between mb-3">
                  <div class="d-flex align-items-center">
                    <img src="<?= htmlspecialchars($imgPath) ?>" class="me-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($productName) ?></div>
                      <div class="text-muted"><?= $productQty ?> barang x Rp<?= number_format($unitPrice, 0, ',', '.') ?></div>
                    </div>
                  </div>
                  <div class="text-end align-self-center">
                    <div class="fw-bold text-muted">Total Belanja</div>
                    <div class="fw-semibold text-success">Rp<?= number_format((float)$row['total_price'], 0, ',', '.') ?></div>
                  </div>
                </div>
              <?php endforeach; ?>

              <div class="d-flex flex-wrap gap-2 justify-content-end">

                <?php
                  // Ambil product_id
                  $stmtPid = $conn->prepare("SELECT id FROM products WHERE name = ?");
                  $stmtPid->bind_param("s", $productName);
                  $stmtPid->execute();
                  $resPid = $stmtPid->get_result()->fetch_assoc();
                  $product_id = $resPid['id'] ?? 0;

                  // Cek apakah user sudah memberi review
                  $stmtReview = $conn->prepare("SELECT COUNT(*) AS count FROM reviews WHERE product_id = ? AND user_id = ?");
                  $stmtReview->bind_param("ii", $productId, $user_id);
                  $stmtReview->execute();
                  $resReview = $stmtReview->get_result()->fetch_assoc();
                  $hasReviewed = $resReview['count'] > 0;
                ?>

                <?php if ($status === 'sudah diterima' && !$hasReviewed): ?>
                  <button type="button"
                          class="btn btn-sm btn-outline-secondary"
                          data-bs-toggle="modal"
                          data-bs-target="#reviewModal<?= $product_id ?>">
                    Beri Rating
                  </button>
                <?php endif; ?>

                <?php if ($status === 'sedang dikirim'): ?>
                  <form method="POST" action="confirm-received.php" onsubmit="return confirm('Konfirmasi pesanan sudah diterima?');" class="me-2">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" class="btn btn-success btn-sm">Pesanan Sudah Diterima</button>
                  </form>
                <?php endif; ?>
                
                <a href="#"
                   class="btn btn-outline-secondary btn-sm"
                   data-bs-toggle="modal"
                   data-bs-target="#modalOrder<?= $row['order_id'] ?>"
                   onclick="event.preventDefault();">
                  Lihat Detail Transaksi
                </a>
              </div>
            </div>
          </div>
        </div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalOrder<?= $row['order_id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['order_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel<?= $row['order_id'] ?>">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">

        <div class="d-flex justify-content-between mb-1">
          <strong>No. Pesanan:</strong>
          <span class="text-success">INV/<?= date('Ymd', strtotime($row['order_date'])) ?>/MPL/<?= $row['order_id'] ?></span>
        </div>
        <div class="mb-3">
          <strong>Tanggal Pembelian:</strong> <?= date('d F Y, H:i', strtotime($row['order_date'])) ?> WIB
        </div>

        <hr class="my-2">

        <?php foreach ($productLines as $line):
          preg_match('/^(.*?) x (\d+)$/', trim($line), $matches);
          $productName = $matches[1] ?? $line;
          $productQty = $matches[2] ?? 1;
          $stmtImg = $conn->prepare("SELECT image_url, price FROM products WHERE name = ?");
          $stmtImg->bind_param("s", $productName);
          $stmtImg->execute();
          $resImg = $stmtImg->get_result()->fetch_assoc();
          $imgPath = $resImg['image_url'] ?? "uploads/default-product.png";
          $unitPrice = $resImg['price'] ?? 0;
        ?>
        <div class="d-flex align-items-center mb-3">
          <img src="<?= htmlspecialchars($imgPath) ?>" class="me-3" style="width: 65px; height: 65px; object-fit: cover; border-radius: 6px;">
          <div>
            <div class="fw-semibold"><?= htmlspecialchars($productName) ?></div>
            <div class="text-muted"><?= $productQty ?> barang x Rp<?= number_format($unitPrice, 0, ',', '.') ?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <hr class="my-2">

        <div class="mb-3">
          <div><strong>Informasi Pengiriman:</strong></div>
          <div class="mt-1">
            <div><strong>Tanggal Kirim:</strong> <?= $row['shipping_date'] ? date('d M Y', strtotime($row['shipping_date'])) : '<em>Belum dikirim</em>' ?></div>
            <div><strong>No. Resi:</strong> <?= htmlspecialchars($row['tracking_number'] ?? '-') ?></div>
            <div><strong>Status:</strong> <?= ucwords($status) ?></div>
            <div><strong>Diterima Oleh:</strong> <?= htmlspecialchars($row['received_by_user'] ?? '-') ?></div>
          </div>
        </div>

        <hr class="my-2">

        <div class="d-flex justify-content-between mt-3">
          <strong>Total Belanja:</strong>
          <strong class="text-success">Rp<?= number_format((float)$row['total_price'], 0, ',', '.') ?></strong>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Form Review -->
<div class="modal fade" id="reviewModal<?= $product_id ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?= $product_id ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="submit-review.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel<?= $product_id ?>">Beri Ulasan Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="product_id" value="<?= $product_id ?>">
          <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">

          <div class="mb-3">
            <label for="rating<?= $product_id ?>" class="form-label">Rating (1–5)</label>
            <select name="rating" id="rating<?= $product_id ?>" class="form-select" required>
              <option value="">Pilih Rating</option>
              <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?> ★</option>
              <?php endfor; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="comment<?= $product_id ?>" class="form-label">Komentar</label>
            <textarea name="comment" id="comment<?= $product_id ?>" class="form-control" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="image<?= $product_id ?>" class="form-label">Gambar (opsional)</label>
            <input type="file" name="image" id="image<?= $product_id ?>" class="form-control" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Kirim Review</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>


      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
