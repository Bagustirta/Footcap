<?php
require_once '..//conn.php';

// Total pesanan
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];

// Total User
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM Users WHERE role = 'user'")->fetch_assoc()['total'];

// Total pengiriman
$totalShipments = $conn->query("SELECT COUNT(*) AS total FROM shipments")->fetch_assoc()['total'];

// Total pendapatan
$totalRevenue = $conn->query("SELECT SUM(total_price) AS revenue FROM orders")->fetch_assoc()['revenue'];

// Produk terlaris
$topProducts = $conn->query("
  SELECT p.name, SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  GROUP BY p.id
  ORDER BY total_sold DESC
  LIMIT 5
");
?>
  <!-- <h2 class="mb-4">Laporan Penjualan</h2>

  <ul class="list-group mb-4">
    <li class="list-group-item">Total Pesanan: <strong><?= $totalOrders ?></strong></li>
    <li class="list-group-item">Total Pendapatan: <strong>Rp<?= number_format($totalRevenue, 0, ',', '.') ?></strong></li>
    <li class="list-group-item">Produk Terlaris: <strong><?= $topProduct['name'] ?> (<?= $topProduct['total_sold'] ?> terjual)</strong></li>
  </ul> -->


 <!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <!-- row -->
  <div class="flex flex-wrap -mx-3">
    
    <!-- card1 -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
      <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
        <div class="flex-auto p-4">
          <div class="flex flex-row -mx-3">
            <div class="flex-none w-2/3 max-w-full px-3">
              <div>
                <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">Total Pesanan</p>
                <h5 class="mb-2 font-bold dark:text-white"><?= $totalOrders ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- card2 -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
      <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
        <div class="flex-auto p-4">
          <div class="flex flex-row -mx-3">
            <div class="flex-none w-2/3 max-w-full px-3">
              <div>
                <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">Total Pendapatan</p>
                <h5 class="mb-2 font-bold dark:text-white">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- card3 -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
      <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
        <div class="flex-auto p-4">
          <div class="flex flex-row -mx-3">
            <div class="flex-none w-2/3 max-w-full px-3">
              <div>
                <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">Total Pengiriman</p>
                <h5 class="mb-2 font-bold dark:text-white"><?= $totalShipments ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- card4 -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 xl:w-1/4">
      <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
        <div class="flex-auto p-4">
          <div class="flex flex-row -mx-3">
            <div class="flex-none w-2/3 max-w-full px-3">
              <div>
                <p class="mb-0 font-sans text-sm font-semibold leading-normal uppercase dark:text-white dark:opacity-60">Total Users</p>
                <h5 class="mb-2 font-bold dark:text-white"><?= $totalUsers ?></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
<!-- cards row 2 -->
<div class="flex flex-wrap mt-6 -mx-3 h-full">
  <!-- Sales overview (8/12) -->
  <div class="w-full max-w-full px-3 mt-0 lg:w-8/12 lg:flex-none">
    <div class="h-full border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
      <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-0">
        <h6 class="capitalize dark:text-white">Sales overview</h6>
        <p class="mb-0 text-sm leading-normal dark:text-white dark:opacity-60">
          <i class="fa fa-arrow-up text-emerald-500"></i>
          <span class="font-semibold">4% more</span> in 2021
        </p>
      </div>
      <div class="flex-auto p-4">
        <div>
          <canvas id="chart-line" height="300"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Categories (4/12) -->
  <div class="w-full max-w-full px-3 mt-0 lg:w-4/12 lg:flex-none">
  <div class="h-full border-black/12.5 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl relative flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
    <div class="p-4 pb-0 rounded-t-4">
      <h6 class="mb-0 dark:text-white">Top 5 Produk Terlaris</h6>
    </div>
    <div class="flex-auto p-4">
      <ul class="flex flex-col pl-0 mb-0 rounded-lg">
        <?php while ($product = $topProducts->fetch_assoc()): ?>
          <li class="relative flex justify-between py-2 pr-4 mb-2 border-0 rounded-xl text-inherit">
            <div class="flex items-center">
              <div class="flex flex-col">
                <h6 class="mb-1 text-sm leading-normal text-slate-700 dark:text-white">
                  <?= htmlspecialchars($product['name']) ?>
                </h6>
                <span class="text-xs leading-tight dark:text-white/80">
                  <span class="font-semibold">(<?= (int) $product['total_sold'] ?> Terjual)</span>
                </span>
              </div>
            </div>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</div>
</div>



