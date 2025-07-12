<?php
$cart_total_price = 0;
$cart_total_items = 0;

if (isset($_SESSION['user_id'])) {
  require_once '../conn.php';

  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("
    SELECT p.price, ci.quantity 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
  ");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $cart_total_price += $row['price'] * $row['quantity'];
    $cart_total_items += $row['quantity'];
  }
}
?>

<div class="absolute w-full bg-blue-500 dark:hidden min-h-75">
<header class="header" data-header>
      <!-- Navbar -->
      <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main navbar-scroll="false">
        <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
          <nav>
            <!-- breadcrumb -->
            <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
              <li class="text-sm leading-normal">
                <a class="text-black opacity-50" href="javascript:;">Pages</a>
              </li>
              <li class="text-sm pl-2 capitalize leading-normal text-white before:float-left before:pr-2 before:text-white before:content-['/']" aria-current="page">Dashboard</li>
            </ol>
            <h6 class="mb-0 font-bold text-white capitalize">Dashboard</h6>
          </nav>

              <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                <li class="relative">
                  <button class="flex items-center gap-2 text-white text-base focus:outline-none">
                    <i class="fas fa-user text-white text-2xl"></i>
                    <span class="text-lg"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                  </button>
                  <div id="dropdownUserMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                    <a href="profile.php" class="block px-5 py-3 text-sm text-gray-700 hover:bg-gray-100">Detail User</a>
                    <a href="logout.php" class="block px-5 py-3 text-sm text-red-600 hover:bg-gray-100">Logout</a>
                  </div>
                </li>
              </ul>
          </div>
        </div>
      </nav>

      <!-- end Navbar -->
    </header>
    </div>