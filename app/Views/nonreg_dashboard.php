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
    <div class="relative flex items-center transition-all duration-500 ease-in-out" id="searchContainer" style="width: 250px;">
      <input type="text" id="searchInput" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" placeholder="Search Kode / Nama..."
        class="border text-xs rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-[#1C4D8D] pr-8 transition-all duration-500 ease-in-out">
    </div>

    <a href="<?= base_url('dashboard/nonreg') ?>" class="bg-[#1C4D8D] px-4 py-2 text-xs rounded-lg hover:bg-[#7AAACE] transition text-white">
      Reset Filter
    </a>
  </form>

  <div class="flex border-b border-gray-300 gap-4 mb-3">
    <a href="<?= base_url('dashboard') ?>" class="py-2 px-4 text-sm font-semibold transition-colors duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Registrasi</a>
    <a href="<?= base_url('dashboard/nonreg') ?>" class="py-2 px-4 text-sm font-semibold transition-colors duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">Non-Registrasi</a>
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
              onclick="sortTable('kode_spec')">Kode Spec <?= sortIcon('kode_spec', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('nama_material')">Nama Material <?= sortIcon('nama_material', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('quantity')">Stock Qty <?= sortIcon('quantity', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('created_at')">Created <?= sortIcon('created_at', $currentSort, $currentDir) ?></th>
            <th
              class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
              onclick="sortTable('updated_at')">Updated <?= sortIcon('updated_at', $currentSort, $currentDir) ?></th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition">Mutasi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php $no = ($currentPage - 1) * $limit + 1;
          if (!empty($perangkat)) {
            foreach ($perangkat as $m): ?>
              <tr id="row-<?= $m['id'] ?>" class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black transition">
                <td class="px-2 py-3 text-center text-xs border border-gray-300">
                  <input type="checkbox" class="row-checkbox w-4 h-4 cursor-pointer accent-[#1C4D8D]" value="<?= $m['id'] ?>">
                </td>
                <td class="px-4 py-3 text-center text-xs text-blue-700 border border-gray-300">
                  <button type="button" onclick="openNonRegEdit(<?= $m['id'] ?>, '<?= addslashes(htmlspecialchars($m['kode_spec'], ENT_QUOTES)) ?>', '<?= addslashes(htmlspecialchars($m['nama_material'], ENT_QUOTES)) ?>', <?= (int)$m['quantity'] ?>)" class="hover:text-blue-400 mr-1 transition">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button type="button" onclick="openNonRegHistory(<?= $m['id'] ?>, '<?= htmlspecialchars($m['nama_material'], ENT_QUOTES) ?>')" class="hover:text-blue-400 mr-1 transition">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                  </button>
                  <?php if ((isset(session('admin')['is_super']) && session('admin')['is_super'] == 1) || session('admin')['username'] === 'admin'): ?>
                  <button type="button" onclick="confirmNonRegDelete(<?= $m['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $no++ ?></td>
                <td class="px-4 py-3 text-left text-xs border border-gray-300"><?= esc($m['kode_spec']) ?></td>
                <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal min-w-[250px]">
                  <?= esc($m['nama_material']) ?>
                </td>
                <td class="px-4 py-3 text-center text-xs border border-gray-300 font-semibold <?= $m['quantity'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                  <?= esc($m['quantity']) ?>
                </td>
                <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $m['created_at'] ?></td>
                <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $m['updated_at'] ?></td>
                <td class="px-4 py-3 text-center text-xs border border-gray-300">
                  <span class="inline-block text-center whitespace-nowrap px-2 py-1 rounded text-xs bg-amber-400 text-amber-800">
                    Tersedia
                  </span>
                </td>
              </tr>
            <?php endforeach;
          } else { ?>
            <tr><td colspan="9" class="px-4 py-3 text-center text-gray-500">Data tidak ditemukan</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- History Modal -->
    <div id="nonRegHistoryModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl w-[95%] md:w-[850px] overflow-hidden flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
          <h3 class="font-bold text-sm">History: <span id="nonRegHistoryItemName"></span></h3>
          <button onclick="closeNonRegHistory()" class="text-white hover:text-gray-300 transition">
            <i class="fa-solid fa-xmark fa-lg"></i>
          </button>
        </div>
        <div class="p-4 flex-1 overflow-y-auto bg-[#F9FBFF]">
          <!-- Search History -->
          <div class="mb-3">
            <input type="text" id="nonRegSearchHistoryInput" placeholder="Cari keterangan, user, atau status..."
              class="border text-xs rounded-md p-2 w-full max-w-sm focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
          </div>
          <div id="nonRegHistoryLoading" class="text-center py-8 hidden">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-[#1C4D8D]"></i>
            <p class="text-sm mt-2 text-gray-500">Memuat history...</p>
          </div>
          <table id="nonRegHistoryTable" class="w-full text-left text-xs border-collapse bg-white shadow-sm rounded-md overflow-hidden border border-gray-200">
            <thead class="bg-gray-100 border-b border-gray-200">
              <tr>
                <th class="p-2 font-semibold text-gray-700 w-12 text-center">No</th>
                <th class="p-2 font-semibold text-gray-700">Tanggal</th>
                <th class="p-2 font-semibold text-gray-700">User</th>
                <th class="p-2 font-semibold text-gray-700">Keterangan</th>
                <th class="p-2 font-semibold text-gray-700 text-center">Mutasi</th>
                <th class="p-2 font-semibold text-gray-700 text-center">Status</th>
              </tr>
            </thead>
            <tbody id="nonRegHistoryBody" class="divide-y divide-gray-100">
              <!-- Content injected by JS -->
            </tbody>
          </table>
          <!-- History Pagination -->
          <div id="nonRegHistoryPagination" class="mt-4 flex justify-center gap-1 hidden">
          </div>
        </div>
      </div>
    </div>
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

<!-- Floating Add Material Button -->
<button onclick="openNonRegManage()"
  class="fixed bottom-10 right-10 w-14 h-14 bg-[#1C4D8D] text-white rounded-full flex items-center justify-center text-xl shadow-lg hover:bg-[#7AAACE] hover:scale-110 transition z-[49]">
  <i class="fa-solid fa-plus"></i>
</button>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>

  let currentNonRegHistoryId = null;

  function openNonRegHistory(id, name) {
    currentNonRegHistoryId = id;
    document.getElementById('nonRegHistoryModal').classList.remove('hidden');
    document.getElementById('nonRegHistoryModal').classList.add('flex');
    document.getElementById('nonRegHistoryItemName').innerText = name;
    document.getElementById('nonRegSearchHistoryInput').value = '';
    
    loadNonRegHistory(1);
  }

  function closeNonRegHistory() {
    document.getElementById('nonRegHistoryModal').classList.add('hidden');
    document.getElementById('nonRegHistoryModal').classList.remove('flex');
    currentNonRegHistoryId = null;
  }

  // Close on overlay click
  document.getElementById('nonRegHistoryModal').addEventListener('click', function(e) {
    if (e.target.id === 'nonRegHistoryModal') {
      closeNonRegHistory();
    }
  });

  const nonRegHistorySearchInput = document.getElementById('nonRegSearchHistoryInput');
  if (nonRegHistorySearchInput) {
    nonRegHistorySearchInput.addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        loadNonRegHistory(1);
      }
    });
  }

  function loadNonRegHistory(page) {
    if (!currentNonRegHistoryId) return;

    const tbody = document.getElementById('nonRegHistoryBody');
    const loading = document.getElementById('nonRegHistoryLoading');
    const table = document.getElementById('nonRegHistoryTable');
    const pagination = document.getElementById('nonRegHistoryPagination');
    const search = document.getElementById('nonRegSearchHistoryInput').value;

    tbody.innerHTML = '';
    table.classList.add('hidden');
    pagination.classList.add('hidden');
    loading.classList.remove('hidden');

    fetch(`<?= base_url('dashboard/nonreg/history') ?>/${currentNonRegHistoryId}?page=${page}&searchHistory=${encodeURIComponent(search)}`)
      .then(res => res.json())
      .then(res => {
        loading.classList.add('hidden');
        table.classList.remove('hidden');

        if (res.data && res.data.length > 0) {
          let html = '';
          let no = (page - 1) * 15 + 1;
          res.data.forEach(item => {
            
            let statusBadge = '';
            switch(item.status) {
              case 'Dibawa': statusBadge = 'bg-yellow-100 text-yellow-800'; break;
              case 'Terpasang': statusBadge = 'bg-blue-100 text-blue-800'; break;
              case 'Kembali': statusBadge = 'bg-green-100 text-green-800'; break;
              case 'Pengiriman': statusBadge = 'bg-orange-100 text-orange-800'; break;
              case 'Terkirim': statusBadge = 'bg-purple-100 text-purple-800'; break;
              default: statusBadge = 'bg-gray-100 text-gray-800';
            }

            let mutasiBadge = item.status === 'Kembali' 
                ? '<span class="text-green-600 font-bold">+' + item.qty + '</span>'
                : '<span class="text-red-600 font-bold">-' + item.qty + '</span>';

            html += `
              <tr class="hover:bg-gray-50 transition">
                <td class="p-2 text-center text-gray-600">${no++}</td>
                <td class="p-2 text-gray-800">${item.created_at}</td>
                <td class="p-2 text-gray-800">${item.nm_user || '-'}</td>
                <td class="p-2 text-gray-600 max-w-[200px] break-words whitespace-normal">${item.keterangan || '-'}</td>
                <td class="p-2 text-center">${mutasiBadge}</td>
                <td class="p-2 text-center">
                  <span class="px-2 py-1 rounded text-[10px] font-semibold ${statusBadge}">${item.status || '-'}</span>
                </td>
              </tr>
            `;
          });
          tbody.innerHTML = html;
          renderNonRegHistoryPagination(res.currentPage, res.totalPage);
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-gray-500">Tidak ada history mutasi</td></tr>';
        }
      })
      .catch(err => {
        console.error(err);
        loading.classList.add('hidden');
        table.classList.remove('hidden');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-red-500">Gagal memuat history</td></tr>';
      });
  }

  function renderNonRegHistoryPagination(current, total) {
    const container = document.getElementById('nonRegHistoryPagination');
    if (total <= 1) return;
    
    container.classList.remove('hidden');
    let html = '';
    
    if (current > 1) {
      html += `<button onclick="loadNonRegHistory(${current - 1})" class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition">&laquo;</button>`;
    }
    
    let start = Math.max(1, current - 2);
    let end = Math.min(total, current + 2);
    
    for (let i = start; i <= end; i++) {
      html += `<button onclick="loadNonRegHistory(${i})" class="px-3 py-1 text-xs rounded transition ${i === current ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
    }
    
    if (current < total) {
      html += `<button onclick="loadNonRegHistory(${current + 1})" class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition">&raquo;</button>`;
    }
    
    container.innerHTML = html;
  }


</script>


<?= $this->endSection() ?>