<?php
session_start();
require_once 'conn.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "Produk tidak ditemukan.";
  exit;
}

$product_result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $product_result->fetch_assoc();

if (!$product) {
  echo "Produk tidak ditemukan.";
  exit;
}

// Ambil review dari database
$reviews_result = $conn->query("SELECT r.*, u.name AS username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $id ORDER BY r.created_at DESC");
$reviews = [];
$total_rating = 0;
while ($row = $reviews_result->fetch_assoc()) {
  $reviews[] = $row;
  $total_rating += $row['rating'];
}
$rating_count = count($reviews);
$average_rating = $rating_count > 0 ? round($total_rating / $rating_count, 1) : 0;

include 'partials/layout.php';
include 'partials/navbar.php';
?>
<style>
    .product-sidebar {
      position: sticky;
      top: 20px;
      background-color: #fff;
      padding: 20px;
      border: 1px solid #eee;
      border-radius: 8px;
    }
    .star {
      color: gold;
    }
    .review-photo {
      width: 80px;
      height: auto;
      margin-right: 10px;
    }
  </style>

<main class="py-5">
  <div class="container">
    <div class="row">
      <!-- Kiri: Gambar & Info Produk -->
<div class="col-lg-8">
  <div class="row mb-4">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
    <div class="col-md-6">
        <h5 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
        <p class="text-muted mb-1">
        Terjual <?= htmlspecialchars($product['sold']) ?>+ • 
        <span class="text-warning">⭐ <?= $average_rating ?> </span> 
        (<?= $rating_count ?> rating)
        </p>


<!-- Pilih Warna -->
<p class="mb-1"><strong>Pilih warna:</strong></p>
<div class="mb-3">
  <button class="btn btn-outline-secondary btn-sm me-2 active" disabled>
    <?= htmlspecialchars($product['color']) ?>
  </button>
</div>


      <!-- Tabs: Detail dan Info Penting -->
      <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Detail</button>
        </li>
      </ul>

      <div class="tab-content" id="productTabContent">
        <!-- Detail -->
        <div class="tab-pane fade show active" id="detail" role="tabpanel">
        <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?></p>
        <p><strong>Ukuran:</strong> <?= htmlspecialchars($product['size']) ?></p>
        <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
      </div>
    </div>
  </div>


        Review Pembeli
        <div>
          <h4>Ulasan Pembeli</h4>
          <p><span class="star">⭐ <?= $average_rating ?> / 5.0</span> dari <?= $rating_count ?> ulasan</p>

          <?php foreach ($reviews as $review): ?>
            <div class="mb-4 border-bottom pb-3">
              <div class="d-flex justify-content-between">
                <div>
                  <strong><?= htmlspecialchars($review['username']) ?></strong>
                </div>
                <div class="text-warning">
                  <?= str_repeat('⭐', $review['rating']) ?>
                  <?= str_repeat('☆', 5 - $review['rating']) ?>
                </div>
              </div>
              <small class="text-muted"><?= date('d M Y', strtotime($review['created_at'])) ?></small>
              <p class="mb-1"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
              <?php if (!empty($review['image_path'])): ?>
                <img src="<?= htmlspecialchars($review['image_path']) ?>" class="review-photo rounded" alt="Foto pembeli">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <?php if ($rating_count === 0): ?>
            <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
          <?php endif; ?>
        </div>
      </div>

<!-- Kanan: Form Pembelian -->
<div class="col-lg-4">
  <div class="product-sidebar shadow-sm">
    <h5>Atur jumlah dan catatan</h5>
    <form action="add-cart.php" method="GET">
      <input type="hidden" name="id" value="<?= $product['id'] ?>">
      <input type="hidden" id="price" value="<?= $product['price'] ?>">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

      <!-- Warna -->
      <div class="mb-2">
        <p class="mb-0 fw-bold"><?= htmlspecialchars($product['color']) ?></p>
      </div>

<!-- Jumlah dan Stok -->
<div class="mb-3">
  <div class="d-flex align-items-center gap-2">
    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control form-control-sm" style="width: 80px;">
    <span class="text-muted small">Stok: <?= number_format($product['stock']) ?></span>
  </div>
</div>

      <!-- Subtotal -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Subtotal</span>
        <h5 class="fw-bold mb-0 text-success" id="subtotal">Rp<?= number_format($product['price'], 0, ',', '.') ?></h5>
      </div>

      <!-- Tombol Aksi -->
      <button type="submit" class="btn btn-success w-100 mb-2">+ Keranjang</button>
       <button type="submit" class="btn btn-outline-primary w-100" formaction="buy-cart.php" formmethod="POST">Beli Sekarang</button>

    <!-- Wishlist -->
    <div class="text-center mt-2">
<button type="button" id="wishlistBtn" class="btn btn-link text-decoration-none text-dark" style="font-size: 14px;">
        <ion-icon name="heart-outline" class="me-1" style="vertical-align: middle;"></ion-icon>
        Wishlist
    </button>
    </div>

    </form>
  </div>
</div>



    </div>
  </div>
</main>

<script>
  document.getElementById("qtyInput").addEventListener("input", function () {
    const qty = parseInt(this.value) || 1;
    const price = parseInt(document.getElementById("price").value);
    const subtotal = price * qty;

    document.getElementById("subtotal").textContent = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(subtotal);
  });

  document.getElementById('wishlistBtn').addEventListener('click', function () {
  const productId = <?= $product['id'] ?>;

  fetch('wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + productId
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      // Ubah badge wishlist di navbar
      const badge = document.querySelector('.nav-action-badge');
      badge.textContent = data.count;
      badge.setAttribute('value', data.count);
    } else {
      alert('Gagal menambahkan wishlist');
    }
  });
});
</script>

