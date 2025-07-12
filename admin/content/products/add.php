<!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <!-- Product list section -->
    <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
      <div class="h-full relative z-20 flex flex-col break-words rounded-2xl border bg-white dark:bg-slate-850 shadow-md">
        <!-- Header -->
        <div class="px-6 pt-4 pb-2 border-b border-gray-200 dark:border-slate-700">
          <h6 class="text-lg font-semibold text-slate-700 dark:text-white">Tambah Produk</h6>
        </div>

<form action="index.php?page=products&action=add" method="POST" enctype="multipart/form-data" class="space-y-4 p-6">
    <div>
      <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
      <input type="text" name="name" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Brand</label>
      <input type="text" name="brand" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Ukuran</label>
      <input type="text" name="size" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Warna</label>
      <input type="text" name="color" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Harga</label>
      <input type="number" name="price" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Stok</label>
      <input type="number" name="stock" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
      <textarea name="description" required rows="3" class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"></textarea>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Gambar Produk</label>
      <input type="file" name="image" accept="image/*" required class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
    </div>

    <div class="flex justify-end space-x-3">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">Simpan</button>
      <a href="index.php?page=products" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 text-sm">Kembali</a>
    </div>
  </form>
      </div>
    </div>
  </div>
</div>



<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $brand = $_POST['brand'];
  $size = $_POST['size'];
  $color = $_POST['color'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $description = $_POST['description'];

  // Upload gambar
  $imagePath = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $filename = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
      $imagePath = "uploads/$filename";
    }
  }

  // Simpan ke database
  $stmt = $conn->prepare("INSERT INTO products (name, brand, size, color, price, stock, description, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param("ssssdiis", $name, $brand, $size, $color, $price, $stock, $description, $imagePath);
  $stmt->execute();

  echo "<script>location.href='index.php?page=products';</script>";
}
?>
