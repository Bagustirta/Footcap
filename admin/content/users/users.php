<!-- cards -->
<div class="w-full px-6 py-6 mx-auto">
  <div class="flex flex-wrap mt-6 -mx-3 h-full">
    <div class="w-full max-w-full px-3 mt-0 lg:w-12 lg:flex-none">
      <div class="h-full border-black/12.5 dark:bg-slate-850 dark:shadow-dark-xl shadow-xl relative z-20 flex flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
        <!-- Header -->
        <div class="border-black/12.5 mb-0 rounded-t-2xl border-b-0 border-solid p-6 pt-4 pb-2">
          <h6 class="capitalize text-lg font-semibold text-slate-700 dark:text-white">Daftar User</h6>
        </div>

        <!-- Table Content -->
        <div class="flex-auto p-4">
          <div class="table-responsive">
            <table class="w-full mb-0 align-top border-collapse text-slate-500 text-sm">
              <thead class="text-xs font-semibold tracking-wide text-left text-gray-700 uppercase bg-gray-100 dark:bg-slate-800 dark:text-white">
                <tr>
                  <th class="px-4 py-3 text-center">No</th>
                  <th class="px-4 py-3">Nama</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Role</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                <?php
                  $result = $conn->query("SELECT * FROM users");
                  $no = 1; while ($row = $result->fetch_assoc()):
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                  <td class="px-4 py-3 text-center font-medium text-slate-700 dark:text-white"><?= $no++ ?></td>
                  <td class="px-4 py-3 text-slate-700 dark:text-white"><?= htmlspecialchars($row['name']) ?></td>
                  <td class="px-4 py-3 text-slate-700 dark:text-white"><?= htmlspecialchars($row['email']) ?></td>
                  <td class="px-4 py-3">
                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white rounded-full 
                      <?= $row['role'] === 'admin' ? 'bg-red-500' : 'bg-blue-500' ?>">
                      <?= ucfirst($row['role']) ?>
                    </span>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- End Table Content -->
      </div>
    </div>
  </div>
</div>
