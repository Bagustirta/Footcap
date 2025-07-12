<?php
session_start();
include '../conn.php';
include 'layout.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$page = $_GET['page'] ?? 'products';
$action = $_GET['action'] ?? ''; // cek apakah ada action

?>

<div class="d-flex">
  <?php include 'sidebar.php'; ?>

  <main class="flex-grow-1 p-4" style="margin-left: 250px;">
    <?php
      $allowedPages = ['dashboard', 'products', 'users', 'orders', 'reviews', 'shipments'];

      if (in_array($page, $allowedPages)) {
        $file = $action ? "content/{$page}/{$action}.php" : "content/{$page}/{$page}.php";

        if (file_exists($file)) {
          include $file;
        } else {
          echo "<h2>Halaman tidak ditemukan</h2>";
        }
      } else {
        echo "<h2>Halaman tidak ditemukan</h2>";
      }
    ?>
  </main>
</div>


