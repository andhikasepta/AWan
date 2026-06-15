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
    <div class="flex gap-2 mb-4">
      <a href="<?= base_url('export/pdf') . $exportQuery ?>" target="_blank"
        class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
        <i class="fa-solid fa-file-pdf"></i>
        Export PDF
      </a>

      <a href="<?= base_url('export/excel') . $exportQuery ?>"
        class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
        <i class="fa-solid fa-file-excel"></i>
        Export Excel
      </a>
    </div>
  </div>

  <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center sticky top-[70px]">
    <?php $isBulk = strpos($_GET['keyword'] ?? '', ';') !== false; ?>
    <div class="relative flex items-center transition-all duration-500 ease-in-out" id="searchContainer" style="width: <?= $isBulk ? '350px' : '200px' ?>;">
      <input type="text" id="searchInput" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" placeholder="<?= $isBulk ? 'Bulk search (pisahkan dengan ;)' : 'Search...' ?>"
        class="border text-xs rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-[#1C4D8D] pr-8 transition-all duration-500 ease-in-out">
      <button type="button" onclick="toggleBulkSearch()" class="absolute right-2 text-[#1C4D8D] hover:text-[#3E679E] transition" title="Toggle Bulk Search">
        <i class="fa-solid fa-layer-group"></i>
      </button>
    </div>

    <div>
      <select name="status" onchange="this.form.submit()"
        class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
        <option value="">Semua Status</option>
        <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
        <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang</option>
        <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
        <option value="Pengiriman" <?= (($_GET['status'] ?? '') == 'Pengiriman') ? 'selected' : '' ?>>Pengiriman</option>
        <option value="Terkirim" <?= (($_GET['status'] ?? '') == 'Terkirim') ? 'selected' : '' ?>>Terkirim</option>
      </select>
    </div>

    <div>
      <select name="filter_mutasi" onchange="this.form.submit()"
        class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
        <option value="">Semua Mutasi</option>
        <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi</option>
        <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>Crosscheck
          INTAN</option>
        <option value="check" <?= (($_GET['filter_mutasi'] ?? '') == 'check') ? 'selected' : '' ?>>Checked</option>
      </select>
    </div>

    <div>
      <select id="isiUser" name="user" onchange="this.form.submit()"
        class="border px-4 py-2 text-xs rounded-lg w-48 focus:outline-none focus:ring-[#1C4D8D]">
        <option value="">Semua User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <a href="/dashboard" class="bg-[#1C4D8D] px-4 py-2 text-xs rounded-lg hover:bg-[#7AAACE] transition text-white">
      Reset Filter
    </a>
  </form>

  <div class="flex-1 bg-white rounded-md shadow flex flex-col overflow-hidden">
    <div class="flex-1 overflow-auto max-h-[calc(100vh-280px)]">
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
                <?php if (session('admin')['username'] === 'admin'): ?>
                <button type="button" onclick="confirmDelete(<?= $p['id'] ?>)"
                  class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
                <?php endif; ?>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $no++ ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300"><?= esc($p['noreg']) ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal max-w-[250px]">
                <?= esc($p['nama']) ?>
              </td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300 text-wrap"><?= $p['nama_user'] ?? '-' ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal max-w-[225px]">
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
        <?php if (session('admin')['username'] === 'admin'): ?>
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

  <div class="py-1 sticky bottom-0 mt-2">
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
<script>
  // SERVER-SIDE TABLE SORTING
  function sortTable(column) {
    const params = new URLSearchParams(window.location.search);
    const currentSort = params.get('sort_by');
    const currentDir = params.get('sort_dir');

    // Toggle direction: if same column, flip; otherwise default to asc
    let newDir = 'asc';
    if (currentSort === column) {
      newDir = currentDir === 'asc' ? 'desc' : 'asc';
    }

    params.set('sort_by', column);
    params.set('sort_dir', newDir);
    params.set('page', '1'); // Reset to page 1 on sort

    window.location.search = params.toString();
  }
</script>
<script>
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      loadUsers();
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      const input = document.getElementById("newUserInput");
      if (input) {
        e.preventDefault();
        saveNewUser();
      }
    }
  });

  document.addEventListener("input", function (e) {
    if (e.target.id === "searchUser") {
      const keyword = e.target.value.toLowerCase();

      const filtered = allUsers.filter(u =>
        u.nama.toLowerCase().includes(keyword)
      );

      renderUsers(filtered);
    }
  });

  function showToast(message, type = "error") {
    const toast = document.getElementById("toast");
    const box = document.getElementById("toastBox");
    const msg = document.getElementById("toastMsg");
    const icon = document.getElementById("toastIcon");

    msg.innerText = message;

    box.className = "flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-white text-sm";

    if (type === "error") {
      box.classList.add("bg-red-500");
      icon.className = "fa-solid fa-circle-xmark";
    } else if (type === "success") {
      box.classList.add("bg-green-500");
      icon.className = "fa-solid fa-circle-check";
    } else {
      box.classList.add("bg-yellow-500");
      icon.className = "fa-solid fa-triangle-exclamation";
    }

    toast.classList.remove("hidden", "translate-x-full");
    toast.classList.add("translate-x-0");

    setTimeout(() => {
      toast.classList.remove("translate-x-0");
      toast.classList.add("translate-x-full");

      setTimeout(() => {
        toast.classList.add("hidden");
      }, 300);
    }, 3000);
  }

  function toggleBulkSearch() {
    const container = document.getElementById('searchContainer');
    const input = document.getElementById('searchInput');
    const isBulk = container.style.width === '350px';
    
    if (isBulk) {
      container.style.width = '200px';
      input.placeholder = 'Search...';
    } else {
      container.style.width = '350px';
      input.placeholder = 'Multi-search...';
      input.focus();
    }
  }

  function openModal(id) {
    document.getElementById(id).classList.remove("hidden");
    document.getElementById(id).classList.add("flex");

    const namaWrapper = document.getElementById("namaWrapper");
    if (namaWrapper) namaWrapper.classList.remove("hidden");
  }

  function closeModal(id) {
    const modal = document.getElementById(id);

    modal.classList.add("hidden");
    modal.classList.remove("flex");

    const form = modal.querySelector("form");

    if (form && id !== "editModal") {
      form.reset();
    }

    if (id === "tambahModal" && tsSpec) {
      tsSpec.clear();
    }
  }

  // USER MANAGE
  window.openUserManage = function () {
    openModal('userManageModal');
    loadUsers();
  }

  let allUsers = [];
  function loadUsers() {
    fetch("<?= base_url('dashboard/userList') ?>")
      .then(res => res.json())
      .then(res => {
        allUsers = res;
        renderUsers(res);
      });
  }

  function renderUsers(users) {
    const tbody = document.getElementById("userManageBody");
    const keyword = (document.getElementById("searchUser")?.value || "").toLowerCase();

    tbody.innerHTML = "";

    if (!users.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="3" class="text-center py-4">
            User tidak ditemukan
          </td>
        </tr>`;
      return;
    }

    let no = 1;
    users.forEach(user => {
      tbody.innerHTML += `
        <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
          <td class="px-4 py-3 text-center text-xs text-blue-700 border border-gray-300">
            <button type="button" onclick="editUser(this)" data-id="${user.id}" data-nama="${user.nama}" class="text-[#1C4D8D] hover:text-blue-400 mr-1 transition">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button type="button" onclick="deleteUser(${user.id})" class="text-[#1C4D8D] hover:text-blue-400 mr-1 transition">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </td>
          <td class="px-4 py-3 text-center border border-gray-300">${no++}</td>
          <td class="px-4 py-3 text-left border border-gray-300">${highlightText(user.nama, keyword)}</td>
        </tr>`;
    });
  }

  function highlightText(text, keyword) {
    if (!keyword) return text;

    const regex = new RegExp(`(${keyword})`, "gi");
    return text.replace(regex, '<span class="bg-blue-200 rounded">$1</span>');
  }

  function editUser(el) {
    const userId = el.dataset.id;
    const namaLama = el.dataset.nama;

    const row = el.closest("tr");
    row.classList.add("bg-[#F9FBFF]", "ring-1", "ring-[#1C4D8D]/10");

    const tdNama = row.children[2];

    tdNama.innerHTML = `
      <div class="relative group">
        <input type="text" id="edit_nama_${userId}" value="${namaLama}" class="w-full bg-white border border-gray-300 px-3 py-2 rounded-md text-xs shadow-sm focus:ring-1 focus:ring-[#1C4D8D] outline-none transition-all duration-200">
      </div>`;

    tdNama.style.opacity = "0";
    tdNama.style.transform = "translateY(4px)";

    setTimeout(() => {
      tdNama.style.opacity = "1";
      tdNama.style.transform = "translateY(0)";
    }, 120);

    const tdAction = row.children[0];
    tdAction.innerHTML = `
    <div class="flex justify-center items-center gap-2">

      <button onclick="saveUser(${userId})"
        class="w-6 h-6 flex items-center justify-center rounded-full 
              bg-green-100 text-green-600 hover:bg-green-200 active:scale-95 transition"
        title="Simpan">
        <i class="fa-solid fa-check text-xs"></i>
      </button>

      <button onclick="cancelEdit(${userId})"
        class="w-6 h-6 flex items-center justify-center rounded-full 
              bg-gray-100 text-gray-600 hover:bg-gray-200 active:scale-95 transition"
        title="Batal">
        <i class="fa-solid fa-xmark text-xs"></i>
      </button>

    </div>`;

    setTimeout(() => {
      const input = document.getElementById(`edit_nama_${userId}`);
      input.focus();

      input.select();
    }, 100);
  }

  function saveUser(id) {
    const nama = document.getElementById(`edit_nama_${id}`).value;

    fetch("<?= base_url('dashboard/updateUser') ?>/" + id, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({ nama })
    })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          showToast("User berhasil diubah", "success");
          loadUsers();
        } else {
          showToast("Gagal mengubah user", "error");
        }
      });
  }

  function cancelEdit(el, id, namaLama) {
    loadUsers();
    const row = el.closest("tr");
    row.classList.remove("bg-[#F9FBFF]", "ring-1", "ring-[#1C4D8D]/10");
  }

  function addUser() {
    const tbody = document.getElementById("userManageBody");

    if (document.getElementById("newUserRow")) return;

    const row = document.createElement("tr");
    row.id = "newUserRow";
    row.className = "bg-[#F9FBFF] animate-fadein";

    row.innerHTML = `
    <td class="px-4 py-3 text-center border">
      <div class="flex justify-center gap-2">

        <button onclick="saveNewUser()"
          class="w-6 h-6 flex items-center justify-center rounded-full 
                bg-green-100 text-green-600 hover:bg-green-200 active:scale-95 transition"
          title="Simpan">
          <i class="fa-solid fa-check text-xs"></i>
        </button>

        <button onclick="cancelNewUser()"
          class="w-6 h-6 flex items-center justify-center rounded-full 
                bg-gray-100 text-gray-600 hover:bg-gray-200 active:scale-95 transition"
          title="Batal">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      </div>
    </td>

    <td class="px-4 py-3 text-center border text-xs">-</td>
    <td class="px-4 py-3 border">
      <input type="text" id="newUserInput" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none" placeholder="Masukkan Nama User">
    </td>
    `;

    tbody.prepend(row);

    setTimeout(() => {
      document.getElementById("newUserInput").focus();
    }, 100);
  }

  //  let nama = prompt("Masukkan nama user : "); 

  //  if(!nama || !nama.trim()) return;
  //  fetch("<?= base_url('dashboard/addUser') ?>", {
  //   method : "POST",
  //   headers: {
  //     "X-Requested-With" : "XMLHttpRequest"
  //   },
  //   body: new URLSearchParams({nama})
  //  })
  // .then(res => res.json())
  // .then(res => {
  //   console.log(res);
  //   if(res.success){
  //     showToast("Berhasil Menambahkan User", "success");
  //     loadUsers();
  //     refreshUserDropdown();
  //     addUserToTomSelect(res.data);
  //   }else{
  //     showToast("Gagal Menambahkan User", "error");
  //   }
  //  });

  function saveNewUser() {
    const input = document.getElementById("newUserInput");
    const nama = input.value;

    if (!nama.trim()) {
      showToast("Nama User Kosong", "warning");
      return;
    }

    fetch("<?= base_url('dashboard/addUser') ?>", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({ nama })
    })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          showToast("Berhasil Menambahkan User", "success");
          loadUsers();
          refreshUserDropdown();
        } else {
          showToast("Gagal Menambahkan User", "error");
        }
      });
  }

  function cancelNewUser() {
    const row = document.getElementById("newUserRow");
    if (row) row.remove();
  }

  function deleteUser(id) {
    Swal.fire({
      title: "Hapus user ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Hapus"
    }).then(result => {
      if (result.isConfirmed) {
        fetch("<?= base_url('dashboard/deleteUser') ?>/" + id, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        })
          .then(res => res.json())
          .then(res => {
            if (res.success) {
              showToast("User berhasil dihapus", "success");
              loadUsers();
            } else {
              showToast("Gagal menghapus user", "error");
            }
          });
      }
    });
  }

  function refreshUserDropdown() {
    fetch("<?= base_url('dashboard/userList') ?>")
      .then(res => res.json())
      .then(users => {
        const select = document.getElementById("isiUser");

        const selected = select.value;
        select.innerHTML = `<option value="">Semua User</option>`;

        users.forEach(u => {
          select.innerHTML += `<option value="${u.id}">${u.nama}</option>`;
        });

        select.value = selected;
      });
  }

  function addUserToTomSelect(user) {
    const select = document.getElementById("edit_user");

    if (select && select.tomselect) {
      select.tomselect.addOption({
        value: user.id,
        text: user.nama
      });

      select.tomselect.refreshOptions(false);
    }
  }
  // EDIT MODAL
  window.openEdit = function (id) {
    fetch("<?= base_url('dashboard/edit') ?>/" + id)
      .then(res => res.json())
      .then(data => {
        fetch("<?= base_url('dashboard/userList') ?>")
          .then(res => res.json())
          .then(users => {
            const select = document.getElementById("edit_user");

            if (select.tomselect) {
              select.tomselect.destroy();
            }

            select.innerHTML = `<option value="">Pilih User</option>`;
            users.forEach(u => {
              select.innerHTML += `<option value="${u.id}">${u.nama}</option>`;
            });

            const ts = new TomSelect("#edit_user", {
              create: false,
              sortField: {
                field: "text",
                direction: "asc"
              }
            });

            ts.setValue(data.id_users ?? "");

            // new TomSelect("#edit_user", {
            //   create: false,
            //   sortField: {
            //     field: "text",
            //     direction: "asc"
            //   }
            // });
          });
        console.log(data);
        console.log("STATUS MUTASI:", data.status_mutasi);

        document.getElementById("edit_id").value = data.id;
        document.getElementById("edit_noreg").value = data.noreg;
        document.getElementById("edit_np").value = data.nama;

        // document.getElementById("edit_user").value = data.id_users ?? "";
        document.getElementById("edit_status").value = data.status_mutasi ?? "";
        document.getElementById("edit_ket").value = data.keterangan ?? "";

        openModal("editModal");

        // if (!document.getElementById("edit_user").TomSelect) {
        //   new TomSelect("#edit_user", {
        //     create: false,
        //     sortField: {
        //       field: "text",
        //       direction: "asc"
        //     }
        //   });
        // }
      });
  }

  const editForm = document.querySelector("#editMutasi");
  const submitEdit = document.getElementById("btn_submit_edit");

  if (editForm) {
    editForm.addEventListener("submit", function (e) {
      e.preventDefault();

      submitEdit.disabled = true;

      let formData = new FormData(this);

      fetch("<?= base_url('dashboard/update') ?>", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(() => location.reload())
        .catch(() => {
          submitEdit.disabled = false;
          submitEdit.innerText = "Simpan";
        });
    });
  }

  // HISTORY MODAL
  window.openHistory = function (id) {
    openModal("historyModal");

    const input = document.getElementById("searchHistory");
    input.dataset.id = id;

    loadHistory(id, 1);
  }

  function loadHistory(id, page = 1, search = '') {
    fetch(`<?= base_url('dashboard/history') ?>/${id}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `page=${page}&searchHistory=${encodeURIComponent(search)}`
    })
      .then(res => res.json())
      .then(res => {
        console.log(res);

        let tbody = document.getElementById("historyBody");
        tbody.innerHTML = "";

        if (!res.data || res.data.length === 0) {
          tbody.innerHTML =
            `<tr>
            <td colspan="5" class="text-center py-4">History tidak ditemukan</td>
          </tr>`;

          document.getElementById("paginationHistory").innerHTML = "";
          return;
        }

        let no = (res.currentPage - 1) * 50 + 1;

        const keyword = search.toLowerCase();

        function highlightText(text, keyword) {
          if (!keyword) return text;

          const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
          const regex = new RegExp(`(${escaped})`, "gi");

          return text.replace(regex, '<span class="bg-blue-200 rounded">$1</span>');
        }

        res.data.forEach(row => {
          tbody.innerHTML += `
          <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
            <td class="px-4 py-3 text-center border border-gray-300">${no++}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[125px] border border-gray-300">${highlightText(row.updated_at ?? '-', keyword)}</td>
            <td class="px-4 py-3 text-center border border-gray-300">${highlightText(row.nm_user ?? '-', keyword)}</td>
            <td class="px-4 py-3 text-center border border-gray-300">${highlightText(row.status ?? '-', keyword)}</td>
            <td class="px-4 py-3 text-center border border-gray-300">${highlightText(row.updated_by ?? '-', keyword)}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[200px] border border-gray-300">${highlightText(row.keterangan ?? '-', keyword)}</td>
          </tr>`;
        });

        renderPagination(id, res.currentPage, res.totalPage, search);
      });
  }

  function renderPagination(id, currentPage, totalPage, search) {
    let container = document.getElementById("paginationHistory");

    container.innerHTML = "";

    for (let i = 1; i <= totalPage; i++) {
      container.innerHTML += `
      <button onclick="loadHistory(${id}, ${i}, '${search}')" class="px-3 py-1 text-xs rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200'}">${i}</button>`;
    }
  }

  window.closeHistory = function () {
    closeModal("historyModal");
  }

  document.getElementById("historyModal").addEventListener("click", function (e) {
    if (e.target.id === "historyModal") {
      closeHistory();
    }
  });

  const searchInput = document.getElementById("searchHistory");
  if (searchInput) {
    searchInput.addEventListener("keyup", function () {
      let id = this.dataset.id;
      loadHistory(id, 1, this.value);
    });
  }

  // BUTTON CROSSCHECK INTAN
  document.addEventListener("click", function (e) {
    let btn = e.target.closest(".btn-check");
    if (!btn) return;

    let id = btn.getAttribute("data-id");

    Swal.fire({
      title: "Check perangkat ini?",
      text: "Ubah status menjadi checked setelah selesai crosscheck di INTAN ",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#1C4D8D",
      cancelButtonColor: "#d33",
      confirmButtonText: "Ubah Status",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("<?= base_url('dashboard/check') ?>/" + id, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              btn.outerHTML = '<span class="px-2 py-1 rounded text-xs bg-lime-400 text-lime-800">Checked</span>';
            }
          });
      }
    });
  });

  // TAMBAH PERANGKAT MODAL
  let tsSpec;

  document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("kode_spec");
    if (el) {
      tsSpec = new TomSelect(el, {
        valueField: "id",
        labelField: "text",
        searchField: ["kode_spec", "nama_perangkat"],
        create: true,
        load: function (query, callback) {
          if (!query.length) return callback();
          fetch(`/perangkat/getSpec?search=${query}`)
            .then(res => res.json())
            .then(data => {
              callback(data.map(item => ({
                id: item.id,
                text: item.kode_spec + " - " + item.nama_perangkat,
                kode_spec: item.kode_spec,
                nama: item.nama_perangkat
              })));
            }).catch(() => callback());
        },
        onChange: function (value) {
          const namaInput = document.getElementById("nama");
          const namaWrapper = document.getElementById("namaWrapper");
          if (/^\d+$/.test(value)) {
            fetch(`/perangkat/getSpecById?id=${value}`)
              .then(res => res.json())
              .then(data => {
                namaInput.value = data.nama_perangkat;
                namaWrapper.classList.add("hidden");
              });
          } else {
            namaInput.value = value;
            namaWrapper.classList.remove("hidden");
          }
        }
      });
    }

    const tambahForm = document.getElementById("tambahperangkat");
    if (tambahForm) {
      tambahForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById("btn_submit_tambah");
        const namaInput = document.getElementById("nama");

        if (!namaInput.value) {
          showToast("Nama perangkat belum diisi!", "warning");
          return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerText = "Menyimpan...";
        }

        let formData = new FormData(this);

        if (tsSpec) {
          formData.set('id_spec', tsSpec.getValue());
        }

        fetch("<?= base_url('dashboard/simpan') ?>", {
          method: "POST",
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(res => res.json())
          .then(res => {
            if (res.success) {
              showToast(res.message || "Data berhasil disimpan!", "success");
              setTimeout(() => {
                location.reload();
              }, 1500);
            } else {
              showToast(res.message || "Gagal menyimpan data!", "error");
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = "Simpan";
              }
            }
          })
          .catch(err => {
            console.error(err);
            showToast("Terjadi kesalahan pada server!", "error");
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.innerText = "Simpan";
            }
          });
      });
    }
  });

  const kodeInput = document.getElementById('kode_id');
  const specSelect = document.getElementById('kode_spec');

  const warning = document.getElementById('noregWarning');
  const ok = document.getElementById('noregOK');

  let debounceTimer;

  function cekNoregRealTime() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
      let kode_id = kodeInput.value;
      if (!specSelect.tomselect || !specSelect.value) return;

      let selectedOption = specSelect.tomselect.getItem(specSelect.value);
      if (!selectedOption) return;

      if (!kode_id || !selectedOption) return;

      let kode_spec = selectedOption.innerText.split(' - ')[0];

      let noreg = kode_spec + kode_id;

      fetch(`/perangkat/cek-noreg?noreg=${noreg}`)
        .then(res => res.json())
        .then(data => {
          if (data.exists) {
            warning.classList.remove('hidden');
            ok.classList.add('hidden');
          } else {
            warning.classList.add('hidden');
            ok.classList.remove('hidden');
          }
        });
    }, 500);
  }

  kodeInput.addEventListener('input', cekNoregRealTime);
  specSelect.addEventListener('change', cekNoregRealTime);

  // HAPUS DATA PERANGKAT
  window.confirmDelete = function (id) {
    Swal.fire({
      title: "Apakah Anda yakin menghapus perangkat ini?",
      text: "Data perangkat akan terhapus",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#1C4D8D",
      cancelButtonColor: "#d33",
      confirmButtonText: "Hapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("<?= base_url('perangkat/delete') ?>/" + id)
          .then(res => res.json())
          .then(res => {
            if (res.success) {

              const row = document.getElementById("row-" + id);

              row.style.transition = "all 0.4s ease";
              row.style.opacity = "0";
              row.style.transform = "translateX(50px)";

              setTimeout(() => {
                row.remove();
              }, 400);

              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Perangkat berhasil dihapus",
                showConfirmButton: false
              });

            } else {
              Swal.fire("Gagal", "Data tidak ditemukan", "error");
            }
          })
      };
    });
  }
  // ========== MULTI-SELECT & BULK EDIT ==========

  function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
  }

  function updateBulkToolbar() {
    const ids = getSelectedIds();
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('selectedCount');

    countEl.textContent = ids.length;

    if (ids.length > 0) {
      toolbar.classList.remove('hidden');
    } else {
      toolbar.classList.add('hidden');
    }

    // Update select-all checkbox state
    const allCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('selectAll');
    if (allCheckboxes.length > 0 && ids.length === allCheckboxes.length) {
      selectAll.checked = true;
      selectAll.indeterminate = false;
    } else if (ids.length > 0) {
      selectAll.checked = false;
      selectAll.indeterminate = true;
    } else {
      selectAll.checked = false;
      selectAll.indeterminate = false;
    }
  }

  function toggleRowHighlight(checkbox) {
    const row = checkbox.closest('tr');
    if (checkbox.checked) {
      row.classList.add('selected-row');
    } else {
      row.classList.remove('selected-row');
    }
  }

  // Select All checkbox
  document.getElementById('selectAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
      cb.checked = this.checked;
      toggleRowHighlight(cb);
    });
    updateBulkToolbar();
  });

  // Individual row checkboxes
  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-checkbox')) {
      toggleRowHighlight(e.target);
      updateBulkToolbar();
    }
  });

  function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
      cb.checked = false;
      toggleRowHighlight(cb);
    });
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    updateBulkToolbar();
  }

  function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
      showToast('Pilih minimal 1 data', 'warning');
      return;
    }

    Swal.fire({
      title: `Hapus ${ids.length} perangkat?`,
      text: 'Data perangkat yang dipilih akan dihapus permanen',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("<?= base_url('perangkat/bulkDelete') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
          },
          body: JSON.stringify({ ids: ids })
        })
          .then(res => res.json())
          .then(res => {
            if (res.success) {
              ids.forEach(id => {
                const row = document.getElementById('row-' + id);
                if (row) {
                  row.style.transition = 'all 0.4s ease';
                  row.style.opacity = '0';
                  row.style.transform = 'translateX(50px)';
                  setTimeout(() => row.remove(), 400);
                }
              });
              clearSelection();
              showToast(`Berhasil menghapus ${res.deleted} perangkat`, 'success');
            } else {
              showToast(res.message || 'Gagal menghapus data', 'error');
            }
          })
          .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan pada server', 'error');
          });
      }
    });
  }

  let tsBulkUser = null;
  let tsBulkStatus = null;

  function destroyBulkTomSelects() {
    if (tsBulkUser) { tsBulkUser.destroy(); tsBulkUser = null; }
    if (tsBulkStatus) { tsBulkStatus.destroy(); tsBulkStatus = null; }
  }

  function openBulkEdit() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
      showToast('Pilih minimal 1 data', 'warning');
      return;
    }

    // Set hidden IDs field
    document.getElementById('bulk_ids').value = JSON.stringify(ids);
    document.getElementById('bulkEditCount').textContent = ids.length;

    // Build selected items preview list
    const listEl = document.getElementById('bulkSelectedList');
    listEl.innerHTML = '';
    ids.forEach(id => {
      const row = document.getElementById('row-' + id);
      if (row) {
        const cells = row.querySelectorAll('td');
        const noreg = cells[2]?.textContent?.trim() || '-';
        const nama = cells[3]?.textContent?.trim() || '-';
        listEl.innerHTML += `<div class="py-1 border-b border-gray-200 last:border-0"><strong>${noreg}</strong> — ${nama}</div>`;
      }
    });

    // Reset form fields
    document.getElementById('bulk_ket').value = '';

    // Destroy previous TomSelect instances
    destroyBulkTomSelects();

    // Init TomSelect on existing selects
    tsBulkUser = new TomSelect('#bulk_user', {
      create: false,
      sortField: { field: 'text', direction: 'asc' },
      allowEmptyOption: true
    });
    tsBulkUser.setValue('');

    tsBulkStatus = new TomSelect('#bulk_status', {
      create: false,
      allowEmptyOption: true
    });
    tsBulkStatus.setValue('');

    openModal('bulkEditModal');
  }

  // Override closeModal for bulkEditModal to clean up TomSelect
  const _originalCloseModal = closeModal;
  closeModal = function (id) {
    if (id === 'bulkEditModal') {
      destroyBulkTomSelects();
    }
    if (id === 'tambahModal') {
      resetCsvImport();
      switchTambahTab('manual');
    }
    _originalCloseModal(id);
  };

  // Bulk Edit Form Submit
  const bulkForm = document.getElementById('bulkEditForm');
  if (bulkForm) {
    bulkForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const submitBtn = document.getElementById('btn_submit_bulk');
      const ids = JSON.parse(document.getElementById('bulk_ids').value);
      const id_users = tsBulkUser ? tsBulkUser.getValue() : document.getElementById('bulk_user').value;
      const status_mutasi = tsBulkStatus ? tsBulkStatus.getValue() : document.getElementById('bulk_status').value;
      const keterangan = document.getElementById('bulk_ket').value;

      // Check at least one field is filled
      if (!id_users && !status_mutasi && !keterangan) {
        showToast('Isi minimal 1 field untuk diupdate', 'warning');
        return;
      }

      Swal.fire({
        title: `Update ${ids.length} perangkat?`,
        text: 'Data perangkat yang dipilih akan terupdate',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1C4D8D',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

          fetch("<?= base_url('dashboard/bulkUpdate') ?>", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
              ids: ids,
              id_users: id_users,
              status_mutasi: status_mutasi,
              keterangan: keterangan
            })
          })
            .then(res => res.json())
            .then(res => {
              if (res.success) {
                showToast(`Berhasil mengupdate ${res.updated} perangkat`, 'success');
                setTimeout(() => location.reload(), 1500);
              } else {
                showToast(res.message || 'Gagal mengupdate data', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Semua';
              }
            })
            .catch(err => {
              console.error(err);
              showToast('Terjadi kesalahan pada server', 'error');
              submitBtn.disabled = false;
              submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Semua';
            });
        }
      });
    });
  }

  // ========== CSV IMPORT ==========
  let csvParsedData = [];
  let csvValidationResults = [];

  function switchTambahTab(tab) {
    const tabManual = document.getElementById('tabManual');
    const tabCsv = document.getElementById('tabCsv');
    const contentManual = document.getElementById('tabContentManual');
    const contentCsv = document.getElementById('tabContentCsv');
    const modalContent = document.getElementById('tambahModalContent');

    const activeClass = 'flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]';
    const inactiveClass = 'flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]';

    if (tab === 'manual') {
      tabManual.className = activeClass;
      tabCsv.className = inactiveClass;
      contentManual.classList.remove('hidden');
      contentCsv.classList.add('hidden');
      modalContent.style.maxWidth = '500px';
    } else {
      tabCsv.className = activeClass;
      tabManual.className = inactiveClass;
      contentCsv.classList.remove('hidden');
      contentManual.classList.add('hidden');
      modalContent.style.maxWidth = '750px';
    }
  }

  function downloadCsvTemplate() {
    const csvContent = "noreg,nama\nABC001,Laptop Dell Latitude 5520\nXYZ002,Monitor LG 24 inch";
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_perangkat.csv';
    a.click();
    URL.revokeObjectURL(url);
  }

  function parseCsvLine(line, delimiter = ',') {
    const result = [];
    let current = '';
    let inQuotes = false;
    for (let i = 0; i < line.length; i++) {
      const ch = line[i];
      if (inQuotes) {
        if (ch === '"' && line[i + 1] === '"') {
          current += '"';
          i++;
        } else if (ch === '"') {
          inQuotes = false;
        } else {
          current += ch;
        }
      } else {
        if (ch === '"') {
          inQuotes = true;
        } else if (ch === delimiter) {
          result.push(current.trim());
          current = '';
        } else {
          current += ch;
        }
      }
    }
    result.push(current.trim());
    return result;
  }

  function handleCsvFile(file) {
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const validExts = ['csv', 'xlsx', 'xls'];

    if (!validExts.includes(ext)) {
      showToast('Format file harus .csv, .xlsx, atau .xls', 'warning');
      return;
    }

    if (ext === 'csv') {
      const reader = new FileReader();
      reader.onload = function (e) {
        const text = e.target.result;
        const lines = text.split(/\r?\n/).filter(l => l.trim());

        if (lines.length < 2) {
          showToast('File kosong atau hanya berisi header', 'warning');
          return;
        }

        // Auto-detect delimiter: comma (,) or semicolon (;)
        let delimiter = ',';
        const firstLine = lines[0];
        const commaCount = (firstLine.match(/,/g) || []).length;
        const semiCount = (firstLine.match(/;/g) || []).length;
        if (semiCount > commaCount) {
          delimiter = ';';
        }

        const header = parseCsvLine(lines[0], delimiter).map(h => h.toLowerCase().replace(/[^a-z0-9_]/g, ''));
        const noregIdx = header.indexOf('noreg');
        const namaIdx = header.indexOf('nama');

        if (noregIdx === -1 || namaIdx === -1) {
          showToast('Header harus mengandung kolom "noreg" dan "nama"', 'error');
          return;
        }

        csvParsedData = [];
        for (let i = 1; i < lines.length; i++) {
          const cols = parseCsvLine(lines[i], delimiter);
          
          let noreg = '';
          let nama = '';
          
          if (noregIdx < namaIdx) {
            // Layout: noreg first, then nama (e.g. Column A = noreg, Column B = nama)
            noreg = (cols[0] || '').trim();
            nama = cols.slice(1).join(delimiter).trim();
          } else {
            // Layout: nama first, then noreg (e.g. Column A = nama, Column B = noreg)
            noreg = (cols[cols.length - 1] || '').trim();
            nama = cols.slice(0, cols.length - 1).join(delimiter).trim();
          }

          if (noreg || nama) {
            csvParsedData.push({ noreg, nama, status: 'checking', message: 'Memeriksa...' });
          }
        }

        finishFileParse();
      };
      reader.readAsText(file);
    } else {
      const reader = new FileReader();
      reader.onload = function (e) {
        try {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
          const sheetName = workbook.SheetNames[0];
          const sheet = workbook.Sheets[sheetName];
          const jsonData = XLSX.utils.sheet_to_json(sheet, { defval: '' });

          if (jsonData.length === 0) {
            showToast('File Excel kosong atau tidak ada data', 'warning');
            return;
          }

          const firstRow = jsonData[0];
          const keys = Object.keys(firstRow);
          const noregKey = keys.find(k => k.toLowerCase().replace(/[^a-z0-9_]/g, '') === 'noreg');
          const namaKey = keys.find(k => k.toLowerCase().replace(/[^a-z0-9_]/g, '') === 'nama');

          if (!noregKey || !namaKey) {
            showToast('Header harus mengandung kolom "noreg" dan "nama"', 'error');
            return;
          }

          csvParsedData = [];
          jsonData.forEach(row => {
            const noreg = String(row[noregKey] || '').trim();
            const nama = String(row[namaKey] || '').trim();
            if (noreg || nama) {
              csvParsedData.push({ noreg, nama, status: 'checking', message: 'Memeriksa...' });
            }
          });

          finishFileParse();
        } catch (err) {
          console.error(err);
          showToast('Gagal membaca file Excel. Pastikan format file benar.', 'error');
        }
      };
      reader.readAsArrayBuffer(file);
    }
  }

  function finishFileParse() {
    if (csvParsedData.length === 0) {
      showToast('Tidak ada data valid dalam file', 'warning');
      return;
    }

    document.getElementById('csvUploadZone').classList.add('hidden');
    document.getElementById('csvPreviewArea').classList.remove('hidden');
    renderCsvPreview();
    validateCsvData();
  }

  function renderCsvPreview() {
    const tbody = document.getElementById('csvPreviewBody');
    tbody.innerHTML = '';

    let valid = 0, dup = 0, inv = 0;

    csvParsedData.forEach((row, idx) => {
      let statusBadge = '';
      if (row.status === 'checking') {
        statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500"><i class="fa-solid fa-spinner fa-spin"></i> Memeriksa</span>';
      } else if (row.status === 'tersedia') {
        statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-green-100 text-green-700"><i class="fa-solid fa-circle-check"></i> Tersedia</span>';
        valid++;
      } else if (row.status === 'db_duplicate') {
        statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-red-100 text-red-600"><i class="fa-solid fa-circle-xmark tex-nowrap"></i> Data Duplikat</span>';
        dup++;
      } else if (row.status === 'csv_duplicate') {
        statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-orange-100 text-orange-600"><i class="fa-solid fa-copy text-nowrap"></i> Duplikat CSV</span>';
        dup++;
      } else if (row.status === 'invalid') {
        statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700"><i class="fa-solid fa-triangle-exclamation"></i> Invalid</span>';
        inv++;
      }

      const rowClass = row.status === 'tersedia' ? 'bg-white' : (row.status === 'checking' ? 'bg-white' : 'bg-red-50/50');

      tbody.innerHTML += `
        <tr class="${rowClass} hover:bg-gray-50 transition">
          <td class="px-3 py-2 text-center border border-gray-300">${idx + 1}</td>
          <td class="px-3 py-2 text-left border border-gray-300 font-mono">${row.noreg || '<em class="text-gray-400">-</em>'}</td>
          <td class="px-3 py-2 text-left border border-gray-300">${row.nama || '<em class="text-gray-400">-</em>'}</td>
          <td class="px-3 py-2 text-center border border-gray-300">${statusBadge}</td>
        </tr>`;
    });

    document.getElementById('csvTotalCount').textContent = csvParsedData.length;
    document.getElementById('csvValidCount').textContent = valid;
    document.getElementById('csvDupCount').textContent = dup;
    document.getElementById('csvInvCount').textContent = inv;

    const importBtn = document.getElementById('btn_import_csv');
    if (valid > 0) {
      importBtn.disabled = false;
      importBtn.querySelector('span').textContent = `Import ${valid} Data`;
    } else {
      importBtn.disabled = true;
      importBtn.querySelector('span').textContent = 'Import Data';
    }
  }

  function validateCsvData() {
    const noregList = csvParsedData.map(r => r.noreg);

    fetch("<?= base_url('perangkat/validateCsvNoreg') ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify({ noreg_list: noregList })
    })
      .then(res => res.json())
      .then(res => {
        if (res.success && res.results) {
          res.results.forEach(r => {
            if (csvParsedData[r.index]) {
              csvParsedData[r.index].status = r.status;
              csvParsedData[r.index].message = r.message;
            }
          });

          csvParsedData.forEach((row, idx) => {
            if (row.status === 'tersedia' && !row.nama) {
              row.status = 'invalid';
              row.message = 'Nama kosong';
            }
          });

          renderCsvPreview();
        }
      })
      .catch(err => {
        console.error(err);
        showToast('Gagal memvalidasi data CSV', 'error');
      });
  }

  function resetCsvImport() {
    csvParsedData = [];
    csvValidationResults = [];
    const fileInput = document.getElementById('csvFileInput');
    if (fileInput) fileInput.value = '';
    document.getElementById('csvUploadZone').classList.remove('hidden');
    document.getElementById('csvPreviewArea').classList.add('hidden');
    document.getElementById('csvPreviewBody').innerHTML = '';
    const importBtn = document.getElementById('btn_import_csv');
    if (importBtn) {
      importBtn.disabled = true;
      importBtn.querySelector('span').textContent = 'Import Data';
    }
  }

  function submitCsvImport() {
    const validRows = csvParsedData.filter(r => r.status === 'tersedia');

    if (validRows.length === 0) {
      showToast('Tidak ada data valid untuk diimport', 'warning');
      return;
    }

    Swal.fire({
      title: `Import ${validRows.length} perangkat?`,
      text: `${validRows.length} data dengan status Tersedia akan ditambahkan ke database`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#1C4D8D',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, Import!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        const importBtn = document.getElementById('btn_import_csv');
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mengimport...</span>';

        fetch("<?= base_url('perangkat/importCsv') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
          },
          body: JSON.stringify({ rows: validRows.map(r => ({ noreg: r.noreg, nama: r.nama })) })
        })
          .then(res => res.json())
          .then(res => {
            if (res.success) {
              let msg = `Berhasil mengimport ${res.inserted} perangkat`;
              if (res.skipped > 0) msg += ` (${res.skipped} dilewati)`;
              showToast(msg, 'success');
              setTimeout(() => location.reload(), 1500);
            } else {
              showToast(res.message || 'Gagal mengimport data', 'error');
              importBtn.disabled = false;
              importBtn.innerHTML = '<i class="fa-solid fa-file-import"></i> <span>Import Data</span>';
            }
          })
          .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan pada server', 'error');
            importBtn.disabled = false;
            importBtn.innerHTML = '<i class="fa-solid fa-file-import"></i> <span>Import Data</span>';
          });
      }
    });
  }

  // CSV drag-drop and file input event listeners
  document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('csvDropZone');
    const fileInput = document.getElementById('csvFileInput');

    if (dropZone && fileInput) {
      dropZone.addEventListener('click', () => fileInput.click());

      dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-[#1C4D8D]', 'bg-blue-50');
      });

      dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
      });

      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
        const file = e.dataTransfer.files[0];
        handleCsvFile(file);
      });

      fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) handleCsvFile(file);
      });
    }
  });

</script>

<?= $this->endSection() ?>