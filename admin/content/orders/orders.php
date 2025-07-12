<?php
require_once '../conn.php'; // atau sesuaikan jalur file koneksi
session_start();

// Ambil semua pesanan dari semua pengguna
$sql = "
  SELECT 
    o.id AS order_id,
    u.name AS user_name,
    o.order_date,
    o.total_price,
    o.status,
    GROUP_CONCAT(CONCAT(p.name, ' x ', oi.quantity) SEPARATOR ', ') AS products
  FROM orders o
  JOIN users u ON o.user_id = u.id
  LEFT JOIN order_items oi ON o.id = oi.order_id
  LEFT JOIN products p ON oi.product_id = p.id
  GROUP BY o.id
  ORDER BY o.order_date DESC
";

$result = $conn->query($sql);
?>

<!-- HTML -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
      <div class="h-full shadow-xl relative flex flex-col break-words rounded-2xl bg-white border-0">

        <!-- Header -->
        <div class="mb-0 rounded-t-2xl p-6 pt-4 pb-2 border-b">
          <h6 class="capitalize text-slate-700 text-lg font-semibold">Daftar Pesanan</h6>
        </div>

        <!-- Content -->
        <div class="flex-auto p-4">
          <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded text-sm mb-4">
              Status pesanan berhasil diperbarui.
            </div>
          <?php endif; ?>

          <?php if ($result && $result->num_rows > 0): ?>
            <div class="overflow-x-auto rounded-lg shadow">
              <table class="w-full mb-0 text-sm text-slate-500 border-collapse">
                <thead class="text-xs font-semibold tracking-wide text-left text-gray-700 uppercase bg-gray-100">
                  <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">Pengguna</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3 text-center">Tanggal</th>
                    <th class="px-4 py-3 text-end">Total</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-4 py-3 text-center font-semibold text-slate-700">
                        <?= $no++ ?>
                      </td>
                      <td class="px-4 py-3 text-slate-700">
                        <?= htmlspecialchars($row['user_name']) ?>
                      </td>
                      <td class="px-4 py-3 text-slate-600">
                        <?= htmlspecialchars($row['products']) ?>
                      </td>
                      <td class="px-4 py-3 text-center text-slate-600">
                        <?= date('d M Y, H:i', strtotime($row['order_date'])) ?>
                      </td>
                      <td class="px-4 py-3 text-end font-medium text-slate-700">
                        Rp<?= number_format($row['total_price'], 0, ',', '.') ?>
                      </td>
                      <td class="px-4 py-3 text-center">
                        <span class="inline-block text-xs font-semibold px-4 py-1 rounded-full text-white 
                          <?= match (strtolower($row['status'])) {
                            'menunggu verifikasi' => 'bg-slate-500',
                            'sedang diproses' => 'bg-yellow-500',
                            'sedang dikirim' => 'bg-blue-500',
                            'sudah diterima' => 'bg-indigo-500',
                            'selesai' => 'bg-green-500',
                            default => 'bg-gray-700'
                          } ?>">
                          <?= ucwords($row['status']) ?>
                        </span>
                      </td>
                      <td class="px-4 py-3 text-center">
                        <a href="index.php?page=orders&action=edit-order&id=<?= $row['order_id'] ?>" class="text-xs font-semibold text-blue-500 hover:underline">
                          Edit
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded text-sm">
              Belum ada pesanan.
            </div>
          <?php endif; ?>
        </div>
        <!-- End Content -->
      </div>
    </div>
  </div>
</div>
