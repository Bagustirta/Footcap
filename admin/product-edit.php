<?php include '../conn.php'; ?>
<?php
$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $price = $_POST['price'];

  if ($_FILES['image']['name']) {
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image);
    $conn->query("UPDATE products SET name='$name', category='$category', price='$price', image='$image' WHERE id=$id");
  } else {
    $conn->query("UPDATE products SET name='$name', category='$category', price='$price' WHERE id=$id");
  }

  header("Location: products.php");
}
?>

<h2>Edit Produk</h2>
<form action="" method="POST" enctype="multipart/form-data">
  <label>Nama Produk:</label><br>
  <input type="text" name="name" value="<?= $product['name'] ?>" required><br>
  <label>Kategori:</label><br>
  <input type="text" name="category" value="<?= $product['category'] ?>" required><br>
  <label>Harga:</label><br>
  <input type="number" name="price" value="<?= $product['price'] ?>" required><br>
  <label>Gambar Baru (opsional):</label><br>
  <input type="file" name="image"><br><br>
  <button type="submit">Update</button>
</form>
