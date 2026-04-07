<?= $this->extend('layouts/buatdashboard') ?>

<?= $this->section('content') ?>

<div class="max-w-[1450px] mx-auto w-full flex-1 flex flex-col">
  <div class="flex justify-between items-center w-full">
    <h2 class="text-base font-semibold mb-3">
      Selamat Datang, <?= session('admin')['nama'] ?? 'Admin' ?>!
    </h2>

    <div class="flex gap-2 mb-4">
      <a href="<?= base_url('export/pdf') ?>" target="_blank" class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
     <i class="fa-solid fa-file-pdf"></i>
     Export PDF
      </a>

      <a href="<?= base_url('export/excel') ?>" class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
        <i class="fa-solid fa-file-excel"></i>
        Export Excel
      </a>
    </div>
  </div>

  <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center sticky top-[70px]">
    <input type="text" name="keyword" value="<?= $_GET['keyword'] ?? '' ?>" placeholder="Cari apa aja" class="border text-xs rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">

    <div>
      <select name="status" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
        <option value="">Semua Status</option>
        <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
        <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang</option>
        <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
      </select>
    </div>

    <div>
      <select name="filter_mutasi" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
        <option value="">Semua Mutasi</option>
        <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi</option>
        <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>Crosscheck INTAN</option>
        <option value="check" <?= (($_GET['filter_mutasi'] ?? '') == 'check') ? 'selected' : '' ?>>Checked</option>
      </select>
    </div>

    <div>
      <select name="user" onchange="this.form.submit()" class="border px-4 py-2 text-xs rounded-lg w-48 focus:outline-none focus:ring-[#1C4D8D]">
        <option value="">Semua User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>"
            <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <a href="/dashboard" class="bg-gray-200 px-4 py-2 text-xs rounded-lg hover:bg-gray-300 transition">
      Reset
    </a>
  </form>

  <div class="flex-1 bg-white rounded-md shadow flex flex-col overflow-hidden">
    <div class="flex-1 overflow-auto max-h-[calc(100vh-280px)]">

      <table class="min-w-full text-xs text-left border border-gray-300">
        <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
          <tr>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Action</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">No</th>
            <th class="px-4 py-3 text-xs text-left border border-gray-300">No Registrasi</th>
            <th class="px-4 py-3 text-xs text-left border border-gray-300">Nama Perangkat</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">User</th>
            <th class="px-4 py-3 text-xs text-left border border-gray-300">Keterangan</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Status</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Created</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Updated</th>
            <th class="px-4 py-3 text-xs text-center border border-gray-300">Mutasi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <?php $no = ($currentPage - 1) * $limit + 1;
          foreach ($perangkat as $p): ?>

            <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
              <td class="px-4 py-3 text-center text-xs text-blue-700 border border-gray-300">
                <button type="button" onclick="openEdit(<?= $p['id'] ?>)" class="btn-check hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" onclick="openHistory(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <button type="button" onclick="confirmDelete(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $no++ ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300"><?= esc($p['noreg']) ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal max-w-[250px]"><?= esc($p['nama']) ?></td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['nama_user'] ?? '-' ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal max-w-[225px]"><?= esc($p['keterangan_mutasi']) ?: '-' ?></td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300">
                <span class="px-2 py-1 rounded text-xs
                  <?= $p['status_mutasi'] == 'Dibawa' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Terpasang' ? 'bg-blue-200 text-blue-800' : '' ?>
                  <?= $p['status_mutasi'] == 'Kembali' ? 'bg-green-200 text-green-800' : '' ?>
                  ">
                  <?= $p['status_mutasi'] ?? '-' ?>
                </span>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['created_at'] ?></td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['mutasi_updated'] ?></td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300">
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
    </div>

    <button onclick="openModal('tambahModal')" class="fixed bottom-10 right-10 w-14 h-14 bg-[#1C4D8D] text-white rounded-full flex items-center justify-center text-xl shadow-lg hover:bg-[#7AAACE] hover:scale-110 transition z-[49]">
      <i class="fa-solid fa-plus"></i>
    </button>

    <?= view('components/editmutasi') ?>
    <?= view('components/historyperangkat') ?>
    <?= view('components/tambahperangkat') ?>

  </div>
  
  <div class="py-1 sticky bottom-0 mt-2">
    <div class="flex justify-center items-center gap-1 w-full">
      <?php for ($i = 1; $i <= $totalPage; $i++): ?>
        <a href="?page=<?= $i ?>" class="px-3 py-1 text-xs rounded 
            <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function openModal(id) {
    document.getElementById(id).classList.remove("hidden");
    document.getElementById(id).classList.add("flex");
  }

  function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
    document.getElementById(id).classList.remove("flex");
  }

  window.openEdit = function(id) {
    fetch("<?= base_url('dashboard/edit') ?>/" + id)
      .then(res => res.json())
      .then(data => {
        console.log(data);

        document.getElementById("edit_id").value = data.id;
        document.getElementById("edit_noreg").value = data.noreg;
        document.getElementById("edit_np").value = data.nama;

        document.getElementById("edit_user").value = data.id_users ?? "";
        document.getElementById("edit_status").value = data.status ?? "";
        document.getElementById("edit_ket").value = data.keterangan ?? "";

        openModal("editModal");

        if (!document.getElementById("edit_user").TomSelect) {
          new TomSelect("#edit_user", {
            create: false,
            sortField: {
              field: "text",
              direction: "asc"
            }
          });
        }
      });
  }

  const editMutasi = document.getElementById("editMutasi");
  if (editMutasi) {
    editMutasi.addEventListener("submit", function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      fetch("<?= base_url('dashboard/update') ?>", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(() => location.reload());
    });
  }

  window.openHistory = function(id) {
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

        if (res.data.length === 0) {
          tbody.innerHTML =
            `<tr>
            <td colspan="5" class="text-center py-4">Belum Ada History Mutasi</td>
          </tr>`;
          return;
        }

        let no = (res.currentPage - 1) * 30 + 1;

        res.data.forEach(row => {
          tbody.innerHTML += `
          <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
            <td class="px-4 py-3 text-center">${no++}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[125px]">${row.updated_at ?? '-'}</td>
            <td class="px-4 py-3 text-center">${row.nm_user ?? '-'}</td>
            <td class="px-4 py-3 text-center">${row.status ?? '-'}</td>            
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[200px]">${row.keterangan ?? '-'}</td>
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
      <button onclick="loadHistory(${id}, ${i}, '${search}')" class="px-3 py-1 text-xs rounded ${i===currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200'}">${i}</button>`;
    }
  }

  window.closeHistory = function() {
    closeModal("historyModal");
  }

  document.getElementById("historyModal").addEventListener("click", function(e) {
    if (e.target.id === "historyModal") {
      closeHistory();
    }
  });

  const searchInput = document.getElementById("searchHistory");
  if (searchInput) {
    searchInput.addEventListener("keyup", function() {
      let id = this.dataset.id;
      loadHistory(id, 1, this.value);
    });
  }

  document.addEventListener("click", function(e) {
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
              btn.outerHTML = '<span class="px-2 py-1 rounded text-xs bg-green-400 text-white">Checked</span>';
            }
          });
      }
    });
  });

  const tambahForm = document.getElementById("tambahperangkat");

  if (tambahForm) {
    tambahForm.addEventListener("submit", function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      fetch("<?= base_url('dashboard/simpan') ?>", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(res => {
          console.log(res);

          if (res.success) {
            closeModal("tambahModal");
            location.reload();
          }
        });
    });
  }

  window.confirmDelete = function(id) {
    Swal.fire({
      title: "Apakah Anda yakin?",
      text: "Anda tidak bisa membatalkan ini",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#1C4D8D",
      cancelButtonColor: "#d33",
      confirmButtonText: "Hapus",
      cancelButtonText: "Batal"
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("<?= base_url('perangkat/delete') ?>/" + id)
      };
    });
  }
</script>

<?= $this->endSection() ?>