<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>

<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<main class="container py-5">
  <div class="alert alert-success text-center">
    <h4>Terima kasih!</h4>
    <p>Pesananmu telah berhasil dibuat dan sedang menunggu verifikasi.</p>
    <a href="orders.php" class="btn btn-primary mt-3">Lihat Pesanan</a>
  </div>
</main>

