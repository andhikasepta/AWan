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
                <button type="button" onclick="confirmDelete(<?= $p['id'] ?>)" class="hover:text-blue-400 mr-1 transition">
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
    <?= view('components/usermanage') ?>

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
  document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
      loadUsers();
    }
  });

  document.addEventListener("keydown", function(e){
    if(e.key === "Enter"){
      const input = document.getElementById("newUserInput");
      if(input){
        e.preventDefault();
        saveNewUser();
      }
    }
  }); 

  document.addEventListener("input", function(e){
    if(e.target.id === "searchUser"){
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

    if(form && id !== "editModal"){
      form.reset();
    }

    if(id === "tambahModal" && tsSpec){
      tsSpec.clear();
    }
  }

  // USER MANAGE
  window.openUserManage = function () {
    openModal('userManageModal');
    loadUsers();
  }

  let allUsers = [];
  function loadUsers(){
    fetch("<?= base_url('dashboard/userList') ?>")
    .then(res => res.json() )
    .then(res => {
      allUsers = res;
      renderUsers(res);
    });
  }

  function renderUsers(users){
      const tbody = document.getElementById("userManageBody");
      const keyword = (document.getElementById("searchUser")?.value || "").toLowerCase();

      tbody.innerHTML = "";

      if (!users.length){
        tbody.innerHTML = `
        <tr>
          <td colspan="3" class="text-center py-4">
            User tidak ditemukan
          </td>
        </tr>`;
        return;
      }

      let no = 1;
      users.forEach(user=>{
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

    function highlightText(text, keyword){
      if(!keyword) return text;

      const regex = new RegExp(`(${keyword})`, "gi");
      return text.replace(regex, '<span class="bg-blue-200 rounded">$1</span>');
    }

    function editUser(el){
    const userId = el.dataset.id;
    const namaLama = el.dataset.nama;

    const row = el.closest("tr");
    row.classList.add("bg-[#F9FBFF]", "ring-1", "ring-[#1C4D8D]/10");

    const tdNama = row.children[2];

    tdNama.innerHTML=`
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
    tdAction.innerHTML=`
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

    setTimeout(()=>{
      const input = document.getElementById(`edit_nama_${userId}`);
      input.focus();

      input.select();
    }, 100);
  }

  function saveUser(id){
    const nama = document.getElementById(`edit_nama_${id}`).value;

    fetch("<?= base_url('dashboard/updateUser') ?>/" + id, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({nama})
    })
    .then(res=>res.json())
    .then(res=>{
      if(res.success){
        showToast("User berhasil diubah", "success");
        loadUsers();
      }else{
        showToast("Gagal mengubah user", "error");
      }
    });
  }

  function cancelEdit(el, id, namaLama){
    loadUsers();
    const row = el.closest("tr");
    row.classList.remove("bg-[#F9FBFF]", "ring-1", "ring-[#1C4D8D]/10");
  }

  function addUser(){
    const tbody = document.getElementById("userManageBody");
    
    if(document.getElementById("newUserRow")) return;

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

  function saveNewUser(){
    const input = document.getElementById("newUserInput");
    const nama = input.value;

    if(!nama.trim()){
      showToast("Nama User Kosong", "warning");
      return;
    }

    fetch("<?= base_url('dashboard/addUser') ?>", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({nama})
    })
    .then(res => res.json())
    .then(res => {
      if(res.success){
        showToast("Berhasil Menambahkan User", "success");
        loadUsers();
        refreshUserDropdown();
      }else{
        showToast("Gagal Menambahkan User", "error");
      }
    });
  }

  function cancelNewUser(){
    const row = document.getElementById("newUserRow");
    if(row)row.remove();
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
          }else{
            showToast("Gagal menghapus user", "error");
          }
        });
      }
    });
  }

  function refreshUserDropdown(){
    fetch("<?= base_url('dashboard/userList') ?>")
    .then(res=>res.json())
    .then(users=>{
      const select = document.getElementById("isiUser");

      const selected = select.value;
      select.innerHTML = `<option value="">Semua User</option>`;

      users.forEach(u=>{
        select.innerHTML+=`<option value="${u.id}">${u.nama}</option>`;
      });

      select.value = selected;
    });
  }

  function addUserToTomSelect(user){
    const select = document.getElementById("edit_user");

    if(select && select.tomselect){
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
        .then(res=>res.json())
        .then(users=>{
          const select = document.getElementById("edit_user");

          if(select.tomselect){
            select.tomselect.destroy();
          }

          select.innerHTML = `<option value="">Pilih User</option>`;
          users.forEach(u=>{
            select.innerHTML+=`<option value="${u.id}">${u.nama}</option>`;
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
            <td class="px-4 py-3 text-center border border-gray-300">${no++}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[125px] border border-gray-300">${row.updated_at ?? '-'}</td>
            <td class="px-4 py-3 text-center border border-gray-300">${row.nm_user ?? '-'}</td>
            <td class="px-4 py-3 text-center border border-gray-300">${row.status ?? '-'}</td>            
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[200px] border border-gray-300">${row.keterangan ?? '-'}</td>
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
</script>

<?= $this->endSection() ?>