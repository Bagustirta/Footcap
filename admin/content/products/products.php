<!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
      <div class="h-full border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
        <!-- Header -->
        <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-2">
          <h6 class="capitalize dark:text-white text-slate-700 text-lg font-semibold">Daftar Produk</h6>
        </div>

        <!-- Content -->
        <div class="flex-auto p-4">
          <!-- Add product button -->
          <a href="index.php?page=products&action=add" class="inline-block mb-4 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded">
            Tambah Produk
          </a>

          <!-- Product table -->
          <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full mb-0 align-top border-collapse text-slate-500 text-sm">
              <thead class="align-bottom">
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-700 uppercase bg-gray-100 dark:bg-slate-800 dark:text-white">
                  <th class="px-6 py-3">No</th>
                  <th class="px-6 py-3">Nama</th>
                  <th class="px-6 py-3">Harga</th>
                  <th class="px-6 py-3">Kategori</th>
                  <th class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                <?php
                  $result = $conn->query("SELECT * FROM products");
                  $no = 1; while ($row = $result->fetch_assoc()): 
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                  <td class="px-6 py-4 font-medium text-slate-700 dark:text-white"><?= $no++ ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['name']) ?></td>
                  <td class="px-6 py-4">Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['brand']) ?></td>
                  <td class="px-6 py-4 space-x-2">
                    <a href="index.php?page=products&action=edit&id=<?= $row['id'] ?>" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                      Edit
                    </a>
                    <a href="index.php?page=products&action=delete&id=<?= $row['id'] ?>" class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs" onclick="return confirm('Yakin hapus?')">
                      Hapus
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- End Content -->
      </div>
    </div>
  </div>
</div>

