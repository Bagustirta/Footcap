<?php
require_once '../conn.php';

$query = "
  SELECT r.*, p.name AS product_name
  FROM reviews r
  JOIN products p ON r.product_id = p.id
  ORDER BY r.created_at DESC
";

$result = $conn->query($query);
?>


<!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
      <div class="h-full border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
        <!-- Header -->
        <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-2">
          <h6 class="capitalize text-lg font-semibold text-slate-700 dark:text-white">Daftar Ulasan Produk</h6>
        </div>

        <!-- Content -->
        <div class="flex-auto p-4">
          <?php if ($result && $result->num_rows > 0): ?>
          <div class="table-responsive">
            <table class="w-full mb-0 align-top border-collapse text-slate-500 text-sm">
              <thead class="text-xs font-semibold tracking-wide text-left text-gray-700 uppercase bg-gray-100 dark:bg-slate-800 dark:text-white">
                <tr>
                  <th class="px-4 py-3">No</th>
                  <th class="px-4 py-3">Produk</th>
                  <th class="px-4 py-3">Username</th>
                  <th class="px-4 py-3 text-center">Rating</th>
                  <th class="px-4 py-3">Komentar</th>
                  <th class="px-4 py-3 text-center">Gambar</th>
                  <th class="px-4 py-3 text-center">Tanggal</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                  <td class="px-4 py-3"><?= $no++ ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['product_name']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['username']) ?></td>
                  <td class="px-4 py-3 text-center"><?= str_repeat('⭐', (int)$row['rating']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['comment']) ?></td>
                  <td class="px-4 py-3 text-center">
                    <?php if (!empty($row['image_path'])): ?>
                      <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Review Image" class="w-14 h-14 object-cover rounded">
                    <?php else: ?>
                      <span class="text-gray-400 italic">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-center"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
            <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded text-sm">
              Belum ada ulasan.
            </div>
          <?php endif; ?>
        </div>
        <!-- End Content -->
      </div>
    </div>
  </div>
</div>