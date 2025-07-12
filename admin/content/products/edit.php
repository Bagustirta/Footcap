<?php
// Validasi ID
if (!isset($_GET['id'])) {
  echo "<p>Produk tidak ditemukan.</p>";
  return;
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if (!$product) {
  echo "<p>Produk tidak ditemukan.</p>";
  return;
}
?>

<!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <div class="w-full max-w-full px-3 lg:w-12">
      <div class="relative z-20 flex flex-col break-words rounded-2xl border bg-white dark:bg-slate-850 shadow-md">
        <!-- Header -->
        <div class="px-6 pt-4 pb-2 border-b border-gray-200 dark:border-slate-700">
          <h6 class="text-xl font-bold text-slate-700 dark:text-white">Edit Produk</h6>
        </div>

        <!-- Form -->
        <form action="index.php?page=products&action=edit&id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Brand</label>
            <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Ukuran</label>
            <input type="text" name="size" value="<?= htmlspecialchars($product['size']) ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Warna</label>
            <input type="text" name="color" value="<?= htmlspecialchars($product['color']) ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Harga</label>
            <input type="number" name="price" value="<?= $product['price'] ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Stok</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" required rows="3" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200"><?= htmlspecialchars($product['description']) ?></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Gambar Produk (biarkan kosong jika tidak diganti)</label>
            <input type="file" name="image" accept="image/*" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200" />
            <?php if ($product['image_url']): ?>
              <p class="mt-2 text-sm text-gray-600">Gambar saat ini:</p>
              <img src="../<?= $product['image_url'] ?>" width="100" class="mt-1 border rounded" />
            <?php endif; ?>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">Simpan Perubahan</button>
            <a href="index.php?page=products" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 text-sm">Kembali</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
// Handle update
if (isset($_POST['update'])) {
  $name = $_POST['name'];
  $brand = $_POST['brand'];
  $size = $_POST['size'];
  $color = $_POST['color'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $description = $_POST['description'];

  $imageSql = "";
  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $targetDir = "../../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $filename = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
      $imagePath = "uploads/$filename";
      $imageSql = ", image_url='$imagePath'";
    }
  }

  $conn->query("UPDATE products SET name='$name', brand='$brand', size='$size', color='$color', price='$price', stock='$stock', description='$description' $imageSql WHERE id=$id");

  echo "<script>location.href='index.php?page=products';</script>";
}
?>
