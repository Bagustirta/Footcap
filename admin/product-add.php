<?php include '../conn.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $price = $_POST['price'];
  $image = $_FILES['image']['name'];

  move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image);

  $conn->query("INSERT INTO products (name, category, price, image) 
                VALUES ('$name', '$category', '$price', '$image')");

  header("Location: products.php");
}
?>

<h2>Tambah Produk</h2>
<form action="" method="POST" enctype="multipart/form-data">
  <label>Nama Produk:</label><br>
  <input type="text" name="name" required><br>
  <label>Kategori:</label><br>
  <input type="text" name="category" required><br>
  <label>Harga:</label><br>
  <input type="number" name="price" required><br>
  <label>Gambar:</label><br>
  <input type="file" name="image" required><br><br>
  <button type="submit">Simpan</button>
</form>
