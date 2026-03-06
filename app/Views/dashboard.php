<?= $this->extend('layouts/buatdashboard') ?>

<?= $this->section('content') ?>

<h2 class="text-xl font-semibold mb-3">
  Selamat Datang, <?= session('admin')['nama'] ?? 'Admin' ?>!
</h2>

<form method="get" class="bg-white p-3 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-center">
  <div class="filter-box border text-xs px-5 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <input type="text" name="keyword" value="<?= $_GET['keyword'] ?? '' ?>" placeholder="Cari apa aja">
  </div>

  <div>
    <select name="status" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">Semua Status</option>
      <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
      <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang</option>
      <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
    </select>
  </div>

  <div>
    <select name="filter_mutasi" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">Semua Mutasi</option>
      <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi</option>
      <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>Crosscheck INTAN</option>
      <option value="check" <?= (($_GET['filter_mutasi'] ?? '') == 'check') ? 'selected' : '' ?>>Checked</option>
    </select>
  </div>

  <div>
    <select name="user" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg w-48 focus:outline-none focus:ring-blue-500">
      <option value="">Semua User</option>
      <?php foreach ($users as $u): ?>
        <option value="<?= $u['id'] ?>"
          <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
          <?= esc($u['nama']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="bg-blue-600 text-white px-4 py-2 text-xs rounded-lg hover:bg-blue-700 transition">
    Filter
  </button>

  <a href="/dashboard" class="bg-gray-200 px-4 py-2 text-xs rounded-lg hover:bg-gray-300 transition">
    Reset
  </a>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
  <div class="overflow-x-auto">

    <table class="min-w-full text-sm text-left">
      <thead class="bg-[#000033] text-white">
        <tr>
          <th class="px-4 py-3">Action</th>
          <th class="px-4 py-3">No</th>
          <th class="px-4 py-3">No Registrasi</th>
          <th class="px-4 py-3">Nama Perangkat</th>
          <th class="px-4 py-3">Serial Number</th>
          <th class="px-4 py-3">User</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Keterangan</th>
          <th class="px-4 py-3">Created</th>
          <th class="px-4 py-3">Updated</th>
          <th class="px-4 py-3">Mutasi</th>
        </tr>
      </thead>

      <tbody class="divide-y">
        <?php $no = ($currentPage - 1)*$limit + 1;
        foreach ($perangkat as $p): ?>

          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">
              <button onclick="openEdit(<?= $p['id'] ?>)"
                class="text-blue-600 mr-2">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="openHistory(<?= $p['id'] ?>)" class="text-blue-600">
                <i class="fas fa-history"></i>
              </button>
            </td>

            <td class="px-4 py-3"><?= $no++ ?></td>
            <td class="px-4 py-3"><?= esc($p['noreg']) ?></td>
            <td class="px-4 py-3"><?= esc($p['nama']) ?></td>
            <td class="px-4 py-3"><?= esc($p['serial_number']) ?></td>
            <td class="px-4 py-3"><?= $p['nama_user'] ?? '-' ?></td>

            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded text-xs
              <?= $p['status_mutasi'] == 'Dibawa' ? 'bg-yellow-200 text-yellow-800' : '' ?>
              <?= $p['status_mutasi'] == 'Terpasang' ? 'bg-blue-200 text-blue-800' : '' ?>
              <?= $p['status_mutasi'] == 'Kembali' ? 'bg-green-200 text-green-800' : '' ?>
              ">
                <?= $p['status_mutasi'] ?? '-' ?>
              </span>
            </td>

            <td class="px-4 py-3"><?= esc($p['keterangan_mutasi']) ?: '-' ?></td>
            <td class="px-4 py-3"><?= $p['created_at'] ?></td>
            <td class="px-4 py-3"><?= $p['mutasi_updated'] ?></td>

            <td class="px-4 py-3">
              <?php if ($p['status_mutasi'] == 'Terpasang'): ?>
                <?php if ($p['mutasi_check'] == 1): ?>
                  <span class="px-2 py-1 rounded text-xs bg-green-400 text-white">Checked</span>
                <?php else: ?>
                  <button class="btn-check bg-blue-500 text-white px-3 py-1 rounded text-xs" data-id="<?= $p['mutasi_id'] ?>">
                    Crosscheck INTAN
                  </button>
                <?php endif; ?>
              <?php else: ?>
                <?php if ($p['mutasi_check'] == 0): ?>
                  <span class="inline-block text-center whitespace-nowrap px-2 py-1 rounded text-xs bg-yellow-400 text-yellow-900">Belum Mutasi</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>

        <?php endforeach ?>
      </tbody>
    </table>
    <div class="flex justify-center gap-2 py-4 border-t">
      <?php for($i=1; $i <= $totalPage; $i++): ?>
        <a href="?page=<?= $i ?>" class="px-3 py-1 text-sm rounded <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>

  <div id="editModal" class="fixed inset-0 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white border border-gray-200 rounded-md shadow-md w-full max-w-md p-6 relative">
      <button onclick="closeModal()" class="absolute right-3 top-3 text-black text-lg font-bold focus:outline-none">
        <i class="fas fa-times"></i>
      </button>

      <h2 class="text-base text-black font-semibold mb-4">Edit Perangkat</h2>
      <form id="editForm">
        <input type="hidden" name="id" id="edit_id">

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">No Registrasi</label>
          <input type="text" id="edit_noreg" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2 bg-gray-100" disabled>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
          <input type="text" id="edit_np" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2 bg-gray-100" disabled>
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Serial Number</label>
          <input type="text" id="edit_sn" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2 bg-gray-100" disabled>
        </div>

        <hr class="my-4">

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Nama User</label>
          <select id="edit_user" name="id_users" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2">
            <option value="">Pilih User</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= $u['id'] ?>">
                <?= $u['nama'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select id="edit_status" name="status_mutasi" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2">
            <option value="">Pilih Status</option>
            <?php foreach ($statuses as $s): ?>
              <option value="<?= $s ?>">
                <?= $s ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Keterangan</label>
          <textarea x.model="form.reason" id="edit_keterangan" name="keterangan" class="mt-1 block w-full border border-gray-300 shadow-sm rounded-md p-2"></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
          <button type="submit" class="px-4 py-2 bg-[#0066CC] text-white rounded">Simpan</button>
        </div>
      </form>

    </div>
  </div>

  <div id="historyModal" class="fixed inset-0 bg-black-/50 z-50 hidden justify-center items-center">
    <div class="bg-white border border-gray-200 rounded-xl w-full max-w-4xl shadow-xl flex flex-col max-h-[85vh]">
      <div class="flex justify-between items-center px-6 py-4 border-b sticky top-0 bg-white z-10">
        <h2 class="text-base text-black font-bold">History Perangkat</h2>
        <button onclick="closeHistory()" class="absolute right-3 top-2 text-black text-lg font-bold focus:outline-none">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="px-6 pt-4">
        <input type="text" id="searchHistory" value="<?= $_GET['searchHistory'] ?? '' ?>" class="border rounded px-3 py-2 w-full mb-4 sticky top-0" placeholder="Search History">
      </div>

      <div class="flex-1 overflow-y-auto px-6 pb-4 rounded-xl">
        <div class="rounded-xl overflow-hidden">
          <table class="w-full rounded-xl text-sm border text-left sticky top-0">
            <thead class="sticky top-0 z-10 bg-[#000033] text-white">
              <tr>
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Update</th>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Keterangan</th>
              </tr>
            </thead>
            <tbody id="historyBody" class="divide-y"></tbody>
          </table>
        </div>
      </div>
      <div id="paginationHistory" class="flex justify-center gap-2 py-3 border-t"></div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  document.querySelectorAll('input[name="keyword"], input[name="user"]').forEach(el => {
    el.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        this.form.submit();
      }
    });
  });

  document.addEventListener("click", function(e) {
    if (e.target.classList.contains("btn-check")) {
      let button = e.target;
      let id = button.getAttribute("data-id");
      fetch("<?= base_url('dashboard/check') ?>/" + id, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            button.outerHTML = '<span class="px-2 py-1 rounded text-xs bg-green-400 text-white">Checked</span>';
          }
        });
    }
  });

  window.openEdit = function(id) {
    fetch("<?= base_url('dashboard/edit') ?>/" + id)
      .then(res => res.json())
      .then(data => {
        console.log(data);

        document.getElementById("edit_id").value = data.id;
        document.getElementById("edit_noreg").value = data.noreg;
        document.getElementById("edit_np").value = data.nama;
        document.getElementById("edit_sn").value = data.serial_number;

        document.getElementById("edit_user").value = data.id_users ?? "";
        document.getElementById("edit_status").value = data.status ?? "";
        document.getElementById("edit_keterangan").value = data.keterangan ?? "";

        document.getElementById("editModal").classList.remove("hidden");
        document.getElementById("editModal").classList.add("flex");
      });
  }

  window.closeModal = function() {
    document.getElementById("editModal").classList.add("hidden");
    document.getElementById("editModal").classList.remove("flex");
  }

  document.getElementById("editForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("<?= base_url('dashboard/update') ?>", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(() => {
        location.reload();
      });
  });

  window.openHistory = function(id) {
    document.getElementById("historyModal").classList.remove("hidden");
    document.getElementById("historyModal").classList.add("flex");

    document.getElementById("searchHistory").dataset.id = id;

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

        if (res.data.length === 0) {
          tbody.innerHTML =
            `<tr>
        <td colspan="5" class="text-center py-4">Belum Ada History Mutasi</td>
      </tr>`;
          return;
        }

        let no = (res.currentPage - 1) * 30
         + 1;

        res.data.forEach(row => {
          tbody.innerHTML += `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3">${no++}</td>
          <td class="px-4 py-3">${row.updated_at ?? '-'}</td>
          <td class="px-4 py-3">${row.nm_user ?? '-'}</td>
          <td class="px-4 py-3">${row.status ?? '-'}</td>            
          <td class="px-4 py-3">${row.keterangan ?? '-'}</td>
        </tr>
      `;
        });
        renderPagination(id, res.currentPage, res.totalPage, search);
      });
  }

  function renderPagination(id, currentPage, totalPage, search) {
    let container = document.getElementById("paginationHistory");
    if (!container) return;

    container.innerHTML = "";

    for (let i = 1; i <= totalPage; i++) {
      container.innerHTML += `
      <button onclick="loadHistory(${id}, ${i}, '${search}')" class="px-3 py-1 text-sm rounded ${i===currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200'}">${i}</button>
    `;
    }
  }

  window.closeHistory = function() {
    document.getElementById("historyModal").classList.add("hidden");
    document.getElementById("historyModal").classList.remove("flex");
  }

  document.getElementById("historyModal").addEventListener("click", function(e) {
    if (e.target.id === "historyModal") {
      closeHistory();
    }
  });

  document.getElementById("searchHistory").addEventListener("keyup", function() {
    let value = this.value;
    let id = this.dataset.id;

    loadHistory(id, 1, value);
  });
</script>
<?= $this->endSection() ?>