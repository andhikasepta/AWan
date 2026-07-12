<?= $this->extend('layouts/buatdashboard') ?>

<?= $this->section('content') ?>

<div id="toast" class="fixed top-20 right-5 z-50 hidden transform transition-all duration-300 translate-x-full">
  <div id="toastBox" class="flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-white text-sm">
    <i id="toastIcon" class="fa-solid"></i>
    <span id="toastMsg"></span>
  </div>
</div>
<div class="max-w-[1450px] mx-auto w-full flex-1 flex flex-col">
  <div class="flex justify-between items-center w-full">
    <h2 class="text-base font-semibold mb-3">
      Selamat Datang, <?= session('admin')['nama'] ?? '' ?>!
    </h2>
  </div>



  <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center">
    <?php $isBulk = strpos($_GET['keyword'] ?? '', ';') !== false; ?>
    <div class="relative flex items-center transition-all duration-500 ease-in-out" id="searchContainer" style="width: <?= $isBulk ? '350px' : '200px' ?>;">
      <input type="text" id="searchInput" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" placeholder="<?= $isBulk ? 'Bulk search (pisahkan dengan ;)' : 'Search...' ?>"
        class="border text-xs rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-[#1C4D8D] pr-8 transition-all duration-500 ease-in-out">
      <button type="button" onclick="toggleBulkSearch()" class="absolute right-2 text-[#1C4D8D] hover:text-[#3E679E] transition" title="Toggle Bulk Search">
        <i class="fa-solid fa-layer-group"></i>
      </button>
    </div>

    <div class="w-48">
      <select id="filter_status" name="status" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
        <option value="">Semua Status</option>
        <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
        <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang</option>
        <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
        <option value="Pengiriman" <?= (($_GET['status'] ?? '') == 'Pengiriman') ? 'selected' : '' ?>>Pengiriman</option>
        <option value="Terkirim" <?= (($_GET['status'] ?? '') == 'Terkirim') ? 'selected' : '' ?>>Terkirim</option>
      </select>
    </div>

    <div class="w-48">
      <select id="filter_mutasi" name="filter_mutasi" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
        <option value="">Semua Mutasi</option>
        <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi</option>
        <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>Crosscheck
          INTAN</option>
        <option value="check" <?= (($_GET['filter_mutasi'] ?? '') == 'check') ? 'selected' : '' ?>>Checked</option>
      </select>
    </div>

    <div class="w-48">
      <select id="isiUser" name="user" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
        <option value="">Semua User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <a href="<?= base_url('dashboard') ?>" class="bg-[#1C4D8D] px-4 py-2 text-xs rounded-lg hover:bg-[#7AAACE] transition text-white">
      Reset Filter
    </a>

    <?php
    // Build export query string from active filters
    $exportParams = [];
    if (!empty($_GET['keyword']))
      $exportParams['keyword'] = $_GET['keyword'];
    if (!empty($_GET['status']))
      $exportParams['status'] = $_GET['status'];
    if (!empty($_GET['filter_mutasi']))
      $exportParams['filter_mutasi'] = $_GET['filter_mutasi'];
    if (!empty($_GET['user']))
      $exportParams['user'] = $_GET['user'];
    $exportQuery = !empty($exportParams) ? '?' . http_build_query($exportParams) : '';
    ?>
    <a href="<?= base_url('export/pdf') . $exportQuery ?>" target="_blank"
      class="bg-[#1C4D8D] text-white px-4 py-2 text-xs rounded-lg flex items-center gap-2 hover:bg-[#7AAACE] transition">
      <i class="fa-solid fa-file-pdf"></i>
      Export PDF
    </a>

    <a href="<?= base_url('export/excel') . $exportQuery ?>"
      class="bg-[#1C4D8D] text-white px-4 py-2 text-xs rounded-lg flex items-center gap-2 hover:bg-[#7AAACE] transition">
      <i class="fa-solid fa-file-excel"></i>
      Export Excel
    </a>

  </form>

  <div class="flex border-b border-gray-300 gap-4 mb-3">
    <a href="<?= base_url('dashboard') ?>" class="py-2 px-4 text-sm font-semibold transition-colors duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">Registrasi</a>
    <a href="<?= base_url('dashboard/nonreg') ?>" class="py-2 px-4 text-sm font-semibold transition-colors duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Non-Registrasi</a>
  </div>

  <div class="flex flex-col overflow-hidden min-h-0 mb-4">
    <div class="overflow-y-auto max-h-[420px] custom-scrollbar">
      <?php
      $currentSort = $_GET['sort_by'] ?? '';
      $currentDir = $_GET['sort_dir'] ?? '';
      function sortIcon($col, $currentSort, $currentDir)
      {
        if ($currentSort === $col) {
          return $currentDir === 'desc'
            ? '<i class="fa-solid fa-sort-down ml-1 text-[10px] opacity-100"></i>'
            : '<i class="fa-solid fa-sort-up ml-1 text-[10px] opacity-100"></i>';
        }
        return '<i class="fa-solid fa-sort ml-1 text-[10px] opacity-50"></i>';
      }
      ?>
      <table class="min-w-full text-xs text-left border border-gray-300 text-nowrap">
        <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
          <tr>
            <th class="px-2 py-3 text-xs text-center border border-gray-300 w-8">
              <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer accent-[#1C4D8D]" title="Select All">
            </th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Action</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">No</th>
            <th
              class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('noreg')">No Registrasi <?= sortIcon('noreg', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('nama')">Nama Perangkat <?= sortIcon('nama', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('user')">User <?= sortIcon('user', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('keterangan')">Keterangan <?= sortIcon('keterangan', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('status')">Status <?= sortIcon('status', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('created')">Created <?= sortIcon('created', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('updated')">Updated <?= sortIcon('updated', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('updated_by')">Updated By <?= sortIcon('updated_by', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('mutasi')">Mutasi <?= sortIcon('mutasi', $currentSort, $currentDir) ?></th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <?php $no = ($currentPage - 1) * $limit + 1;
          foreach ($perangkat as $p): ?>

            <tr id="row-<?= $p['id'] ?>"
              class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black transition">
              <td class="px-2 py-3 text-center text-xs border border-gray-300">
                <input type="checkbox" class="row-checkbox w-4 h-4 cursor-pointer accent-[#1C4D8D]"
                  value="<?= $p['id'] ?>">
              </td>
              <td class="px-4 py-3 text-center text-xs text-blue-700 border border-gray-300">
                <button type="button" onclick="openEdit(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" onclick="openHistory(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <?php if ((isset(session('admin')['is_super']) && session('admin')['is_super'] == 1) || session('admin')['username'] === 'admin'): ?>
                <button type="button" onclick="confirmDelete(<?= $p['id'] ?>)"
                  class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
                <?php endif; ?>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $no++ ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300"><?= esc($p['noreg']) ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal min-w-[250px]">
                <?= esc($p['nama']) ?>
              </td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300 text-wrap"><?= $p['nama_user'] ?? '-' ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal min-w-[250px]">
                <?= esc($p['keterangan_mutasi']) ?: '-' ?>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300">
                <span class="px-2 py-1 rounded text-xs
                  <?= $p['status_mutasi'] == 'Dibawa' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Terpasang' ? 'bg-blue-200 text-blue-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Kembali' ? 'bg-green-200 text-green-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Pengiriman' ? 'bg-orange-200 text-orange-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Terkirim' ? 'bg-purple-200 text-purple-800' : '' ?>
                  ">
                  <?= $p['status_mutasi'] ?? '-' ?>
                </span>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['created_at'] ?></td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['mutasi_updated'] ?></td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= esc($p['mutasi_updated_by'] ?? '-') ?></td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300">
                <?php if (in_array($p['status_mutasi'], ['Terpasang', 'Terkirim'])): ?>
                  <?php if ($p['mutasi_check'] == 1): ?>
                    <span class="px-2 py-1 rounded text-xs bg-lime-400 text-lime-800">Checked</span>
                  <?php else: ?>
                    <button class="btn-check bg-blue-500 text-white p-2 rounded text-xs hover:bg-blue-400 transition"
                      data-id="<?= $p['mutasi_id'] ?>">
                      Crosscheck INTAN
                    </button>
                  <?php endif; ?>
                <?php else: ?>
                  <?php if ($p['mutasi_check'] == 0): ?>
                    <span
                      class="inline-block text-center whitespace-nowrap px-2 py-1 rounded text-xs bg-amber-400 text-amber-800">Belum
                      Mutasi</span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>

          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <button onclick="openModal('tambahModal')"
      class="fixed bottom-10 right-10 w-14 h-14 bg-[#1C4D8D] text-white rounded-full flex items-center justify-center text-xl shadow-lg hover:bg-[#7AAACE] hover:scale-110 transition z-[49]">
      <i class="fa-solid fa-plus"></i>
    </button>

    <?= view('components/editmutasi') ?>
    <?= view('components/bulkedit', ['users' => $users, 'statuses' => $statuses]) ?>
    <?= view('components/historyperangkat') ?>
    <?= view('components/tambahperangkat') ?>
    <?= view('components/usermanage') ?>

    <!-- Floating Bulk Action Toolbar -->
    <div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[55] hidden">
      <div
        class="flex items-center gap-3 bg-[#0F2854] text-white px-5 py-3 rounded-xl shadow-2xl border border-[#1C4D8D]/30"
        style="animation: slideUp 0.3s ease-out">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-check-double text-sm"></i>
          <span id="selectedCount" class="text-sm font-semibold">0</span>
          <span class="text-sm">data dipilih</span>
        </div>
        <div class="w-px h-6 bg-white/30"></div>
        <button onclick="openBulkEdit()"
          class="flex items-center gap-2 bg-[#1C4D8D] hover:bg-[#7AAACE] px-4 py-2 rounded-lg text-xs font-semibold transition">
          <i class="fa-solid fa-pen-to-square"></i>
          Edit Selected
        </button>
        <?php if ((isset(session('admin')['is_super']) && session('admin')['is_super'] == 1) || session('admin')['username'] === 'admin'): ?>
        <button onclick="bulkDelete()"
          class="flex items-center gap-2 bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg text-xs font-semibold transition">
          <i class="fa-solid fa-trash-can"></i>
          Delete Selected
        </button>
        <?php endif; ?>
        <button onclick="clearSelection()"
          class="flex items-center gap-2 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-xs transition">
          <i class="fa-solid fa-xmark"></i>
          Batal
        </button>
      </div>
    </div>

    <style>
      @keyframes slideUp {
        from {
          transform: translateY(20px);
          opacity: 0;
        }

        to {
          transform: translateY(0);
          opacity: 1;
        }
      }

      tr.selected-row {
        background-color: #DBEAFE !important;
      }
    </style>

  </div>

  <div class="py-2 sticky bottom-0 mt-auto bg-[#F1F1F1] z-20 shadow-sm border-t border-gray-200/50">
    <div class="flex justify-center items-center gap-1 w-full">

      <?php $query = $_GET; ?>

      <!-- Prev -->
      <?php if ($currentPage > 1): ?>
        <?php $query['page'] = $currentPage - 1; ?>
        <a href="?<?= http_build_query($query) ?>" class="px-3 py-1 text-xs bg-gray-200 rounded">
          &laquo;
        </a>
      <?php endif; ?>

      <?php
      $start = max(1, $currentPage - 2);
      $end = min($totalPage, $currentPage + 2);
      ?>

      <!-- First page + dots -->
      <?php if ($start > 1): ?>
        <?php $query['page'] = 1; ?>
        <a href="?<?= http_build_query($query) ?>" class="px-3 py-1 text-xs bg-gray-200 rounded">1</a>
        <?php if ($start > 2): ?>
          <span class="px-2">...</span>
        <?php endif; ?>
      <?php endif; ?>

      <!-- Middle pages -->
      <?php for ($i = $start; $i <= $end; $i++): ?>
        <?php $query['page'] = $i; ?>
        <a href="?<?= http_build_query($query) ?>" class="px-3 py-1 text-xs rounded 
          <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <!-- Last page + dots -->
      <?php if ($end < $totalPage): ?>
        <?php if ($end < $totalPage - 1): ?>
          <span class="px-2">...</span>
        <?php endif; ?>
        <?php $query['page'] = $totalPage; ?>
        <a href="?<?= http_build_query($query) ?>" class="px-3 py-1 text-xs bg-gray-200 rounded">
          <?= $totalPage ?>
        </a>
      <?php endif; ?>

      <!-- Next -->
      <?php if ($currentPage < $totalPage): ?>
        <?php $query['page'] = $currentPage + 1; ?>
        <a href="?<?= http_build_query($query) ?>" class="px-3 py-1 text-xs bg-gray-200 rounded">
          &raquo;
        </a>
      <?php endif; ?>

    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>



<?= $this->endSection() ?>


