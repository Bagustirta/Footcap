<?php
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");
echo "<script>location.href='index.php?page=products';</script>";
?>
