<div id="adminManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[620px] overflow-hidden">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Admin Manage</h3>
            <button onclick="closeAdminManage()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <div class="p-4 flex flex-col overflow-y-auto max-h-[75vh]">

            <!-- Add Admin Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs font-semibold text-[#1C4D8D] mb-2"><i class="fa-solid fa-user-plus mr-1"></i>Tambah Admin Baru</p>
                <div class="grid grid-cols-1 gap-2">
                    <input type="text" id="newAdminNama" placeholder="Nama Lengkap"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                    <input type="text" id="newAdminUsername" placeholder="Username"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                    <select id="newAdminRegion" class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="region">
                        <option value="">Pilih Region</option>
                    </select>
                    <select id="newAdminArea" class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="area">
                        <option value="">Pilih Area</option>
                    </select>
                    <select id="newAdminRole" class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                        <option value="0">Admin Regional</option>
                        <option value="1">Super Admin</option>
                    </select>
                    <button onclick="saveNewAdmin()"
                        class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition">
                        <i class="fa-solid fa-plus mr-1"></i> Simpan Admin
                    </button>
                </div>
            </div>

            <!-- Admin List -->
            <div class="mb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 text-xs"></i>
                    </div>
                    <input type="text" id="searchAdmin" onkeyup="filterAdmins()" placeholder="Cari Nama / Username..." class="w-full border border-gray-300 pl-8 pr-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center w-[70px] border border-gray-300">Action</th>
                            <th class="px-4 py-3 text-center w-[50px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Nama</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Username</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Region</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Area</th>
                            <th class="px-4 py-3 text-center border border-gray-300 whitespace-nowrap">Role</th>
                            <th class="px-4 py-3 text-center border border-gray-300">TTD</th>
                        </tr>
                    </thead>
                    <tbody id="adminManageBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="editAdminModal" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[400px] overflow-hidden">
        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Edit Admin</h3>
            <button onclick="closeEditAdmin()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>
        <div class="p-4 flex flex-col">
            <input type="hidden" id="editAdminId">
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="editAdminNama" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" id="editAdminUsername" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
            </div>
            <div class="mb-3 grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Region</label>
                    <select id="editAdminRegion" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="region">
                        <option value="">-- Pilih --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Area</label>
                    <select id="editAdminArea" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="area">
                        <option value="">-- Pilih --</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Role</label>
                <select id="editAdminRole" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                    <option value="0">Admin Regional</option>
                    <option value="1">Super Admin</option>
                </select>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="closeEditAdmin()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-xs font-semibold transition">Batal</button>
                <button onclick="updateAdminAction()" class="px-4 py-2 bg-[#1C4D8D] hover:bg-[#3E679E] text-white rounded-md text-xs font-semibold transition">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- TTD Upload Modal -->
<div id="ttdUploadModal" class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[400px] overflow-hidden">
        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Upload Tanda Tangan (TTD)</h3>
            <button onclick="closeTtdUpload()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>
        <div class="p-4 flex flex-col items-center">
            <input type="hidden" id="ttdAdminId">
            <p id="ttdAdminName" class="text-sm font-semibold text-gray-700 mb-3"></p>

            <!-- Preview -->
            <div id="ttdPreviewContainer" class="mb-3 hidden">
                <p class="text-xs text-gray-500 mb-1 text-center">TTD saat ini:</p>
                <img id="ttdPreviewImg" src="" alt="TTD Preview" class="max-h-[80px] max-w-[200px] border border-gray-200 rounded p-1 mx-auto">
            </div>

            <!-- File Input -->
            <div class="w-full mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih File PNG</label>
                <input type="file" id="ttdFileInput" accept=".png,image/png"
                    class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                <p class="text-[10px] text-gray-400 mt-1">Format: PNG, Maksimal 2MB</p>
            </div>

            <!-- New file preview -->
            <div id="ttdNewPreview" class="mb-3 hidden">
                <p class="text-xs text-gray-500 mb-1 text-center">Preview file baru:</p>
                <img id="ttdNewPreviewImg" src="" alt="New TTD" class="max-h-[80px] max-w-[200px] border border-dashed border-blue-300 rounded p-1 mx-auto">
            </div>

            <div class="flex gap-2 w-full mt-2">
                <button onclick="uploadTtdAction()" id="btnUploadTtd"
                    class="flex-1 bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition">
                    <i class="fa-solid fa-upload mr-1"></i> Upload TTD
                </button>
                <button onclick="deleteTtdAction()" id="btnDeleteTtd"
                    class="flex-1 bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-md text-xs font-semibold transition hidden">
                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus TTD
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.openAdminManage = function () {
        document.getElementById('adminManageModal').classList.remove('hidden');
        document.getElementById('adminManageModal').classList.add('flex');
        loadAdmins();
    }

    function closeAdminManage() {
        document.getElementById('adminManageModal').classList.add('hidden');
        document.getElementById('adminManageModal').classList.remove('flex');
        document.getElementById('newAdminNama').value = '';
        document.getElementById('newAdminUsername').value = '';
        document.getElementById('newAdminRegion').value = '';
        document.getElementById('newAdminArea').value = '';
        document.getElementById('searchAdmin').value = '';
    }

    let allAdmins = [];

    function loadAdmins() {
        fetch('<?= base_url('dashboard/adminList') ?>')
            .then(res => res.json())
            .then(admins => {
                allAdmins = admins;
                renderAdmins(admins);
            });
    }

    function filterAdmins() {
        const term = document.getElementById('searchAdmin').value.toLowerCase();
        const filtered = allAdmins.filter(a => 
            a.nama.toLowerCase().includes(term) || a.username.toLowerCase().includes(term)
        );
        renderAdmins(filtered);
    }

    function renderAdmins(admins) {
        const tbody = document.getElementById('adminManageBody');
        const currentId = <?= session('admin')['id'] ?? 0 ?>;
        tbody.innerHTML = '';

        if (!admins.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-xs text-gray-500">Tidak ada admin</td></tr>`;
            return;
        }

        let no = 1;
        admins.forEach(a => {
            const isSelf = a.id == currentId;
            const hasTtd = a.ttd_path && a.ttd_path.length > 0;
            tbody.innerHTML += `
                <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
                    <td class="px-4 py-3 text-center border border-gray-300">
                        ${isSelf
                            ? `<span class="text-xs text-gray-400 italic">Anda</span>`
                            : `<div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="editAdmin(${a.id}, '${a.nama.replace(/'/g, "\\'")}', '${a.username.replace(/'/g, "\\'")}', '${(a.region||'').replace(/'/g, "\\'")}', '${(a.area||'').replace(/'/g, "\\'")}', ${a.is_super || 0})" class="text-blue-500 hover:text-blue-400 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button type="button" onclick="resetAdminPassword(${a.id}, '${a.username.replace(/'/g, "\\'")}')" class="text-yellow-500 hover:text-yellow-400 transition" title="Reset Password">
                                    <i class="fa-solid fa-key text-xs"></i>
                                </button>
                                <button type="button" onclick="deleteAdmin(${a.id})" class="text-red-500 hover:text-red-400 transition" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                               </div>`
                        }
                    </td>
                    <td class="px-4 py-3 text-center text-xs border border-gray-300">${no++}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300">${a.nama}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300">${a.username}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300">${a.region || '-'}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300">${a.area || '-'}</td>
                    <td class="px-4 py-3 text-center text-xs border border-gray-300 whitespace-nowrap">
                        ${(a.is_super == 1 || a.is_super === true || a.is_super === 't') 
                            ? '<span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">Super Admin</span>' 
                            : '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">Admin Regional</span>'}
                    </td>
                    <td class="px-4 py-3 text-center text-xs border border-gray-300">
                        <button type="button" onclick="openTtdUpload(${a.id}, '${a.nama.replace(/'/g, "\\'")}', ${hasTtd})" 
                            class="transition" title="${hasTtd ? 'Lihat/Ubah TTD' : 'Upload TTD'}">
                            ${hasTtd 
                                ? '<i class="fa-solid fa-signature text-green-600 hover:text-green-500 text-sm"></i>' 
                                : '<i class="fa-solid fa-upload text-gray-400 hover:text-gray-600 text-sm"></i>'}
                        </button>
                    </td>
                </tr>`;
        });
    }

    function saveNewAdmin() {
        const nama     = document.getElementById('newAdminNama').value.trim();
        const username = document.getElementById('newAdminUsername').value.trim();
        const region   = document.getElementById('newAdminRegion').value.trim();
        const area     = document.getElementById('newAdminArea').value.trim();
        const is_super = document.getElementById('newAdminRole').value;

        if (!nama || !username) {
            showToast('Semua field wajib diisi', 'warning');
            return;
        }

        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

        fetch('<?= base_url('dashboard/addAdmin') ?>', {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: new URLSearchParams({ nama, username, region, area, is_super, csrf_test_name: csrfToken })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast('Admin berhasil ditambahkan', 'success');
                document.getElementById('newAdminNama').value = '';
                document.getElementById('newAdminUsername').value = '';
                document.getElementById('newAdminRegion').value = '';
                document.getElementById('newAdminArea').value = '';
                loadAdmins();
            } else {
                showToast(res.msg ?? 'Gagal menambahkan admin', 'error');
            }
        });
    }

    function resetAdminPassword(id, username) {
        Swal.fire({
            title: 'Reset Password?',
            html: `Yakin ingin mereset password admin <b>${username}</b> menjadi kosong?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

                fetch('<?= base_url('dashboard/resetAdminPassword') ?>/' + id, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast('Password admin berhasil direset', 'success');
                    } else {
                        showToast(res.msg ?? 'Gagal mereset password', 'error');
                    }
                });
            }
        });
    }

    function deleteAdmin(id) {
        Swal.fire({
            title: 'Hapus admin ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

                fetch('<?= base_url('dashboard/deleteAdmin') ?>/' + id, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast('Admin berhasil dihapus', 'success');
                        loadAdmins();
                    } else {
                        showToast(res.msg ?? 'Gagal menghapus admin', 'error');
                    }
                });
            }
        });
    }

    function editAdmin(id, oldNama, oldUsername, oldRegion, oldArea, oldIsSuper) {
        document.getElementById('editAdminId').value = id;
        document.getElementById('editAdminNama').value = oldNama;
        document.getElementById('editAdminUsername').value = oldUsername;
        document.getElementById('editAdminRegion').value = oldRegion;
        document.getElementById('editAdminArea').value = oldArea;
        document.getElementById('editAdminRole').value = (oldIsSuper == 1 || oldIsSuper === true || oldIsSuper === 't') ? '1' : '0';
        document.getElementById('editAdminModal').classList.remove('hidden');
        document.getElementById('editAdminModal').classList.add('flex');
    }

    function closeEditAdmin() {
        document.getElementById('editAdminModal').classList.add('hidden');
        document.getElementById('editAdminModal').classList.remove('flex');
    }

    function updateAdminAction() {
        const id = document.getElementById('editAdminId').value;
        const nama = document.getElementById('editAdminNama').value.trim();
        const username = document.getElementById('editAdminUsername').value.trim();
        const region = document.getElementById('editAdminRegion').value.trim();
        const area = document.getElementById('editAdminArea').value.trim();
        const is_super = document.getElementById('editAdminRole').value;

        if (!nama || !username) {
            showToast('Nama dan Username wajib diisi', 'warning');
            return;
        }

        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

        fetch('<?= base_url('dashboard/updateAdmin') ?>/' + id, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken
            },
            body: new URLSearchParams({
                nama, username, region, area, is_super,
                csrf_test_name: csrfToken
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast('Admin berhasil diupdate', 'success');
                closeEditAdmin();
                loadAdmins();
            } else {
                showToast(res.msg ?? 'Gagal update admin', 'error');
            }
        });
    }

    function toggleAdminPwd() {
        const input = document.getElementById('newAdminPassword');
        const icon  = document.getElementById('adminPwdEye');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }

    // ── TTD Upload Functions ──────────────────────────────────────────────

    function openTtdUpload(adminId, adminName, hasTtd) {
        document.getElementById('ttdAdminId').value = adminId;
        document.getElementById('ttdAdminName').textContent = adminName;
        document.getElementById('ttdFileInput').value = '';
        document.getElementById('ttdNewPreview').classList.add('hidden');

        if (hasTtd) {
            document.getElementById('ttdPreviewContainer').classList.remove('hidden');
            document.getElementById('ttdPreviewImg').src = '<?= base_url('dashboard/getAdminTtd') ?>/' + adminId + '?t=' + Date.now();
            document.getElementById('btnDeleteTtd').classList.remove('hidden');
        } else {
            document.getElementById('ttdPreviewContainer').classList.add('hidden');
            document.getElementById('btnDeleteTtd').classList.add('hidden');
        }

        document.getElementById('ttdUploadModal').classList.remove('hidden');
        document.getElementById('ttdUploadModal').classList.add('flex');
    }

    function closeTtdUpload() {
        document.getElementById('ttdUploadModal').classList.add('hidden');
        document.getElementById('ttdUploadModal').classList.remove('flex');
    }

    document.getElementById('ttdFileInput').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.type !== 'image/png') {
                showToast('Format file harus PNG', 'warning');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('ttdNewPreviewImg').src = e.target.result;
                document.getElementById('ttdNewPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    function uploadTtdAction() {
        const adminId = document.getElementById('ttdAdminId').value;
        const fileInput = document.getElementById('ttdFileInput');
        const file = fileInput.files[0];

        if (!file) {
            showToast('Pilih file TTD terlebih dahulu', 'warning');
            return;
        }

        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

        const formData = new FormData();
        formData.append('ttd_file', file);
        formData.append('csrf_test_name', csrfToken);

        fetch('<?= base_url('dashboard/uploadAdminTtd') ?>/' + adminId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast(res.msg ?? 'TTD berhasil diupload', 'success');
                closeTtdUpload();
                loadAdmins();
            } else {
                showToast(res.msg ?? 'Gagal upload TTD', 'error');
            }
        });
    }

    function deleteTtdAction() {
        const adminId = document.getElementById('ttdAdminId').value;

        Swal.fire({
            title: 'Hapus TTD?',
            text: 'Tanda tangan ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

                fetch('<?= base_url('dashboard/deleteAdminTtd') ?>/' + adminId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: new URLSearchParams({ csrf_test_name: csrfToken })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast(res.msg ?? 'TTD berhasil dihapus', 'success');
                        closeTtdUpload();
                        loadAdmins();
                    } else {
                        showToast(res.msg ?? 'Gagal hapus TTD', 'error');
                    }
                });
            }
        });
    }
</script>
