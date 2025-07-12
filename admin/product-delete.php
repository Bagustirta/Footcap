<?php include '../conn.php'; ?>
<?php
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id = $id");
header("Location: products.php");
