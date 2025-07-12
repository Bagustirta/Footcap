<?php
require_once '../conn.php';

$query = "
  SELECT s.*, o.id AS order_id, o.status AS order_status, u.name AS user_name
  FROM shipments s
  JOIN orders o ON s.order_id = o.id
  JOIN users u ON o.user_id = u.id
  ORDER BY s.shipping_date DESC
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
          <h6 class="capitalize text-lg font-semibold text-slate-700 dark:text-white">Daftar Pengiriman</h6>
        </div>

        <!-- Content -->
        <div class="flex-auto p-4">
          <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
              <table class="w-full mb-0 align-top border-collapse text-slate-500 text-sm">
                <thead class="text-xs font-semibold tracking-wide text-left text-gray-700 uppercase bg-gray-100 dark:bg-slate-800 dark:text-white">
                  <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">Nama Pengguna</th>
                    <th class="px-4 py-3">No. Resi</th>
                    <th class="px-4 py-3">Status Pengiriman</th>
                    <th class="px-4 py-3 text-center">Tanggal Kirim</th>
                    <th class="px-4 py-3">Diterima Oleh</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                  <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                      <td class="px-4 py-3 text-center font-semibold text-sm text-slate-700 dark:text-white"><?= $no++ ?></td>
                      <td class="px-4 py-3 text-slate-700 dark:text-white"><?= htmlspecialchars($row['user_name']) ?></td>
                      <td class="px-4 py-3 text-slate-700 dark:text-white"><?= htmlspecialchars($row['tracking_number']) ?></td>
                      <td class="px-4 py-3 text-slate-700 dark:text-white">
                        <?php
                          $status = $row['order_status'];
                          $status_pengiriman = match ($status) {
                            'Sedang Dikirim' => 'Sedang Dikirim',
                            'Sudah Diterima', 'Selesai' => 'Sudah Diterima',
                            default => 'Belum Dikirim'
                          };
                          echo $status_pengiriman;
                        ?>
                      </td>
                      <td class="px-4 py-3 text-center text-slate-700 dark:text-white">
                        <?= date('d M Y', strtotime($row['shipping_date'])) ?>
                      </td>
                      <td class="px-4 py-3 text-slate-700 dark:text-white">
                        <?= $row['received_by_user'] == 1 ? htmlspecialchars($row['user_name']) : '-' ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded text-sm">
              Belum ada data pengiriman.
            </div>
          <?php endif; ?>
        </div>
        <!-- End Content -->
      </div>
    </div>
  </div>
</div>