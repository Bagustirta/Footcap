<?php
$order_total_items = 0;
$cart_total_items = 0;
$cart_total_price = 0;

if (isset($_SESSION['user_id'])) {
  require_once './conn.php';
  $user_id = $_SESSION['user_id'];

  // Hitung cart
  $stmt = $conn->prepare("SELECT p.price, ci.quantity FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $cart_total_price += $row['price'] * $row['quantity'];
    $cart_total_items += $row['quantity'];
  }

  // Hitung order (jika tidak selesai)
  $stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status != 'selesai'");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $order_total_items = $row['total'] ?? 0;
}

?>


<header class="header" data-header>
      <div class="container">

        <div class="overlay" data-overlay></div>

        <a href="#" class="logo">
          <img src="./assets/images/logo.svg" width="160" height="50" alt="Footcap logo">
        </a>

        <button class="nav-open-btn" data-nav-open-btn aria-label="Open Menu">
          <ion-icon name="menu-outline"></ion-icon>
        </button>

        <nav class="navbar" data-navbar>

          <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
            <ion-icon name="close-outline"></ion-icon>
          </button>

          <a href="#" class="logo">
            <img src="./assets/images/logo.svg" width="190" height="50" alt="Footcap logo">
          </a>

          <ul class="navbar-list" style="display: flex; gap: 2rem; align-items: center;">

            <li class="navbar-item">
              <a href="./index.php" class="navbar-link" style="font-size: 1.4rem; font-weight: 500; text-decoration: none;">Home</a>
            </li>

            <li class="navbar-item">
              <a href="#" class="navbar-link" style="font-size: 1.4rem; font-weight: 500; text-decoration: none;">Products</a>
            </li>

            <li class="navbar-item">
              <a href="./about.php" class="navbar-link" style="font-size: 1.4rem; font-weight: 500; text-decoration: none;">About</a>
            </li>

            <li class="navbar-item">
              <a href="#" class="navbar-link" style="font-size: 1.4rem; font-weight: 500; text-decoration: none;">Contact</a>
            </li>

          </ul>

          <ul class="nav-action-list">

            <li>
              <button class="nav-action-btn">
                <ion-icon name="search-outline" aria-hidden="true"></ion-icon>

                <span class="nav-action-text">Search</span>
              </button>
            </li>

            <li>
              <button class="nav-action-btn">
                <ion-icon name="heart-outline" aria-hidden="true"></ion-icon>
                <span class="nav-action-text">Wishlist</span>
              </button>
            </li>

            <li>
              <a href="cart.php" class="nav-action-btn position-relative d-flex align-items-center gap-2">
                <ion-icon name="bag-outline" aria-hidden="true"></ion-icon>     
                  <?php if ($cart_total_items > 0): ?>
                    <data class="nav-action-badge" value="<?= $cart_total_items ?>" aria-hidden="true">
                      <?= $cart_total_items ?>
                    </data>
                  <?php endif; ?>
                <data class="nav-action-text">
                </data>
              </a>
            </li>
             <li>
                <a href="orders.php" class="nav-action-btn position-relative d-flex align-items-center gap-2">
                  <ion-icon name="document-text-outline"></ion-icon>
                  <?php if ($order_total_items > 0): ?>
                    <data class="nav-action-badge" value="<?= $order_total_items ?>" aria-hidden="true">
                      <?= $order_total_items ?>
                    </data>
                  <?php endif; ?>
                  <data class="nav-action-text"></data>
                </a>
              </li>


            


            <li class="nav-item dropdown">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-action-btn d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
                  <span class="nav-action-text"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                  <li><a class="dropdown-item" href="profile.php">Detail User</a></li>
                  <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
              <?php else: ?>
                <a href="login.php" class="nav-action-btn d-flex align-items-center gap-2">
                  <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
                  <span class="nav-action-text">Login / Register</span>
                </a>
              <?php endif; ?>
            </li>



          </ul>
        </nav>
      </div>
    </header>