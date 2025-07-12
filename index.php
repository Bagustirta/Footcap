<?php
session_start();
require_once 'conn.php';

$products = $conn->query("SELECT * FROM products");
?>

<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<main>
  <section class="section hero" style="background-image: url('./assets/images/hero-banner.png')">
    <div class="container py-5">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h2 class="hero-title" style="font-family: 'Josefin Sans', sans-serif; font-size: 4.5rem; font-weight: 300; margin-bottom: 0;">
            <span style="display: block;">New Summer</span>
            <span style="display: block; font-weight: 500; margin-top: 1.5rem;">Shoes Collection</span>
          </h2>
          <p class="hero-text">
            Competently expedite alternative benefits whereas leading-edge catalysts for change.
            Globally leverage existing an expanded array of leadership.
          </p>
          <button class="btn" style="background-color: #ff6f61; color: white; font-size: 1.4rem; font-weight: 500; padding: 10px 20px; border: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 8px;">
            <span>Shop Now</span>
            <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
          </button>
        </div>
      </div>
    </div>
  </section>


<section class="section collection">
  <div class="container">
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col">
        <div class="collection-card text-center p-4" style="background-image: url('./assets/images/collection-1.jpg')">
          <h2 class="card-title " style="font-size: 2.5rem;">Women Collections</h2>
          <a href="#" class="btn border border-dark mt-3 d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 1.5rem; padding: 10px 20px;">
            <span>Explore All</span>
            <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
          </a>
        </div>
      </div>
      <div class="col">
        <div class="collection-card text-center p-4" style="background-image: url('./assets/images/collection-2.jpg')">
          <h2 class="card-title " style="font-size: 2.5rem;">Women Collections</h2>
          <a href="#" class="btn border border-dark mt-3 d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 1.5rem; padding: 10px 20px;">
            <span>Explore All</span>
            <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
          </a>
        </div>
      </div>
      <div class="col">
        <div class="collection-card text-center p-4" style="background-image: url('./assets/images/collection-3.jpg')">
          <h2 class="card-title " style="font-size: 2.5rem;">Women Collections</h2>
          <a href="#" class="btn border border-dark mt-3 d-inline-flex align-items-center gap-2 fw-bold" style="font-size: 1.5rem; padding: 10px 20px;">
            <span>Explore All</span>
            <ion-icon name="arrow-forward-outline" aria-hidden="true"></ion-icon>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>



<section class="section product">
  <div class="container">
    <h2 class="h2 section-title text-center mb-5">Bestsellers Products</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
      <?php if ($products && $products->num_rows > 0): ?>
        <?php while ($row = $products->fetch_assoc()): ?>
          <div class="col">
            <div class="product-card h-100" tabindex="0">
              <figure class="card-banner">
                <img src="<?= htmlspecialchars($row['image_url']) ?>" width="312" height="350"
                     alt="<?= htmlspecialchars($row['name']) ?>" class="image-contain">
                <ul class="card-action-list">
                  <li class="card-action-item">
                    <a href="<?= isset($_SESSION['user_id']) ? 'add-cart.php?id=' . $row['id'] : 'login.php'; ?>" class="card-action-btn">
                      <ion-icon name="cart-outline"></ion-icon>
                    </a>
                    <div class="card-action-tooltip">Tambah ke Keranjang</div>
                  </li>
                  <li class="card-action-item">
                    <a href="product-detail.php?id=<?= $row['id'] ?>" class="card-action-btn">
                      <ion-icon name="eye-outline"></ion-icon>
                    </a>
                    <div class="card-action-tooltip">Lihat</div>
                  </li>
                </ul>
              </figure>
              <div class="card-content">
                <div class="card-cat">
                  <a href="#" class="card-cat-link"><?= htmlspecialchars($row['brand']) ?></a>
                </div>
                <h3 class="h3 card-title">
                  <a href="product-detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
                </h3>
                <data class="card-price">Rp<?= number_format($row['price'], 0, ',', '.') ?></data>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center">Tidak ada produk tersedia.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

  <section class="section service">
    <div class="container">
      <ul class="service-list">
        <li class="service-item">
          <div class="service-card">
            <div class="card-icon">
              <img src="./assets/images/service-1.png" width="53" height="28" loading="lazy" alt="Service icon">
            </div>
            <div>
              <h3 class="h4 card-title">Free Shiping</h3>
              <p class="card-text">All orders over <span>$150</span></p>
            </div>
          </div>
        </li>
        <li class="service-item">
          <div class="service-card">
            <div class="card-icon">
              <img src="./assets/images/service-2.png" width="43" height="35" loading="lazy" alt="Service icon">
            </div>
            <div>
              <h3 class="h4 card-title">Quick Payment</h3>
              <p class="card-text">100% secure payment</p>
            </div>
          </div>
        </li>
        <li class="service-item">
          <div class="service-card">
            <div class="card-icon">
              <img src="./assets/images/service-3.png" width="40" height="40" loading="lazy" alt="Service icon">
            </div>
            <div>
              <h3 class="h4 card-title">Free Returns</h3>
              <p class="card-text">Money back in 30 days</p>
            </div>
          </div>
        </li>
        <li class="service-item">
          <div class="service-card">
            <div class="card-icon">
              <img src="./assets/images/service-4.png" width="40" height="40" loading="lazy" alt="Service icon">
            </div>
            <div>
              <h3 class="h4 card-title">24/7 Support</h3>
              <p class="card-text">Get Quick Support</p>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </section>
</main>

<?php include 'partials/footer.php'; ?>
