<?= $this->extend('layouts/buatdashboard') ?>

<?= $this->section('content') ?>

<div class="max-w-[1450px] mx-auto w-full flex-1 flex flex-col">
  <div class="flex justify-between items-center w-full">
    <h2 class="text-base font-semibold mb-3">
      Selamat Datang, <?= session('admin')['nama'] ?? '' ?>!
    </h2>

    <div class="flex gap-2 mb-4">
      <a href="<?= base_url('export/pdf') ?>" target="_blank"
        class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
        <i class="fa-solid fa-file-pdf"></i>
        Export PDF
      </a>

      <a href="<?= base_url('export/excel') ?>"
        class="bg-[#1C4D8D] text-white px-2 py-2 rounded text-xs font-medium flex items-center gap-2 hover:bg-[#7AAACE] transition">
        <i class="fa-solid fa-file-excel"></i>
        Export Excel
      </a>
    </div>
  </div>

  <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center sticky top-[70px]">
    <input type="text" name="keyword" value="<?= $_GET['keyword'] ?? '' ?>" placeholder="Search..."
      class="border text-xs rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">

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
      <select name="user" onchange="this.form.submit()"
        class="border px-4 py-2 text-xs rounded-lg w-48 focus:outline-none focus:ring-[#1C4D8D]">
        <option value="">Semua User</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <a href="/dashboard" class="bg-[#1C4D8D] px-4 py-2 text-xs rounded-lg hover:bg-[#7FB3D5] transition text-white">
      Reset Filter
    </a>
  </form>

  <div class="flex-1 bg-white rounded-md shadow flex flex-col overflow-hidden">
    <div class="flex-1 overflow-auto max-h-[calc(100vh-280px)]">
      <table class="min-w-full text-xs text-left border border-gray-300 text-nowrap">
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

            <tr id="row-<?= $p['id'] ?>"
              class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black transition">
              <td class="px-4 py-3 text-center text-xs text-blue-700 border border-gray-300">
                <button type="button" onclick="openEdit(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" onclick="openHistory(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <button type="button" onclick="confirmDelete(<?= $p['id'] ?>)"
                  class="hover:text-blue-400 mr-1 transition">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </td>

              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $no++ ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300"><?= esc($p['noreg']) ?></td>
              <td class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal max-w-[250px]">
                <?= esc($p['nama']) ?>
              </td>
              <td class="px-4 py-3 text-center text-xs border border-gray-300"><?= $p['nama_user'] ?? '-' ?></td>
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
    <?= view('components/historyperangkat') ?>
    <?= view('components/tambahperangkat') ?>

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
        <a href="?page=1" class="px-3 py-1 text-xs bg-gray-200 rounded">1</a>
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
        <a href="?page=<?= $totalPage ?>" class="px-3 py-1 text-xs bg-gray-200 rounded">
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
  function openModal(id) {
    document.getElementById(id).classList.remove("hidden");
    document.getElementById(id).classList.add("flex");

    const namaWrapper = document.getElementById("namaWrapper");
    if (namaWrapper) namaWrapper.classList.remove("hidden");
  }

  function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
    document.getElementById(id).classList.remove("flex");
  }

  // EDIT MODAL
  window.openEdit = function (id) {
    fetch("<?= base_url('dashboard/edit') ?>/" + id)
      .then(res => res.json())
      .then(data => {
        console.log(data);
        console.log("STATUS MUTASI:", data.status_mutasi);

        document.getElementById("edit_id").value = data.id;
        document.getElementById("edit_noreg").value = data.noreg;
        document.getElementById("edit_np").value = data.nama;

        document.getElementById("edit_user").value = data.id_users ?? "";
        document.getElementById("edit_status").value = data.status_mutasi ?? "";
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

        if (res.data.length === 0) {
          tbody.innerHTML =
            `<tr>
            <td colspan="5" class="text-center py-4">Belum Ada History Mutasi</td>
          </tr>`;
          return;
        }

        let no = (res.currentPage - 1) * 50 + 1;

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
  // new TomSelect("#kode_spec", {
  //   valueField: "id",
  //   labelField: "text",
  //   searchField: ["kode_spec", "nama_perangkat"],
  //   create: true,

  //   load: function(query, callback) {
  //     if (!query.length) return callback();

  //     fetch(`/perangkat/getSpec?search=${query}`)
  //       .then(res => res.json())
  //       .then(data => {
  //         callback(data.map(item => ({
  //           id: item.id,
  //           text: item.kode_spec + " - " + item.nama_perangkat,
  //           kode_spec: item.kode_spec,
  //           nama: item.nama_perangkat
  //         })));
  //       }).catch(() => callback());
  //   },

  //   onChange: function(value) {
  //     const namaInput = document.getElementById("nama");
  //     const namaWrapper = document.getElementById("namaWrapper");

  //     if (/^\d+$/.test(value)) {
  //       fetch(`/perangkat/getSpecById?id=${value}`)
  //         .then(res => res.json())
  //         .then(data => {
  //           namaInput.value = data.nama_perangkat;

  //           namaWrapper.classList.add("hidden");
  //           // namaInput.removeAttribute("readonly");
  //         });

  //     } else {
  //       namaInput.value = value;

  //       namaWrapper.classList.remove("hidden");
  //       // namaInput.removeAttribute("readonly");
  //     }
  //   }
  // });

  // 1. Deklarasi satu kali saja di luar
  let tsSpec;

  document.addEventListener("DOMContentLoaded", function () {
    // 1. Inisialisasi TomSelect
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

    // 2. Handle Submit Form
    const tambahForm = document.getElementById("tambahperangkat");
    if (tambahForm) {
      tambahForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // Ambil elemen tombol secara lokal agar tidak undefined
        const submitBtn = document.getElementById("btn_submit_tambah");
        const namaInput = document.getElementById("nama");

        if (!namaInput.value) {
          alert("Nama perangkat belum diisi!");
          return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerText = "Menyimpan...";
        }

        let formData = new FormData(this);

        // Ambil value dari TomSelect secara eksplisit
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
              // CEK MESSAGE: Hanya alert jika message bukan undefined
              if (typeof res.message !== "undefined" && res.message !== null) {
                alert(res.message);
              }
              location.reload();
            } else {
              alert(res.message || "Gagal menyimpan data!");
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = "Simpan";
              }
            }
          })
          .catch(err => {
            console.error(err);
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
      let selectedOption = specSelect.tomselect.getItem(specSelect.value);

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

  const tambahForm = document.getElementById("tambahperangkat");
  const submitTambah = document.getElementById("btn_submit_tambah");

  // if (tambahForm) {
  //   tambahForm.addEventListener("submit", function (e) {
  //     e.preventDefault();

  //     const nama = document.getElementById("nama").value;

  //     if (!nama) {
  //       alert("Nama perangkat belum diisi!");
  //       return;
  //     }

  //     submitTambah.disabled = true;

  //     let formData = new FormData(this);

  //     fetch("<?= base_url('dashboard/simpan') ?>", {
  //       method: "POST",
  //       body: formData
  //     })
  //       .then(res => res.json())
  //       .then(res => {
  //         console.log(res);

  //         if (res.success) {
  //           closeModal("tambahModal");
  //           location.reload();
  //         }
  //       })
  //       .catch(() => {
  //         submitTambah.disabled = false;
  //         submitTambah.innerText = "Simpan";
  //       });
  //   });
  // }

  if (tambahForm) {
    tambahForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // 1. Ambil elemen tombol secara langsung agar tidak undefined
      const btnSubmit = document.getElementById("btn_submit_tambah");
      const namaInput = document.getElementById("nama");

      if (!namaInput.value) {
        alert("Nama perangkat belum diisi!");
        return;
      }

      // 2. Pastikan tombol ada sebelum di-disable
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerText = "Proses...";
      }

      let formData = new FormData(this);

      // Tambahkan value TomSelect jika diperlukan
      if (typeof tsSpec !== 'undefined') {
        formData.set('id_spec', tsSpec.getValue());
      }

      fetch("<?= base_url('dashboard/simpan') ?>", {
        method: "POST",
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(res => {
          // 3. Gunakan pengecekan || untuk menghindari notif "undefined"
          if (res.success) {
            alert(res.message || "Data berhasil disimpan!");
            location.reload();
          } else {
            alert(res.message || "Terjadi kesalahan pada server.");
            if (btnSubmit) {
              btnSubmit.disabled = false;
              btnSubmit.innerText = "Simpan";
            }
          }
        })
        .catch(err => {
          console.error(err);
          alert("Gagal terhubung ke server.");
          if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Simpan";
          }
        });
    });
  }

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
                showConfirmaButton: false
              });

            } else {
              Swal.fire("Gagal", "Data tidak ditemukan", "error");
            }
          })
      };
    });
  }
</script>

<?= $this->endSection() ?>