<div id="regionalManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">
        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Regional Manage</h3>
            <button onclick="closeModal('regionalManageModal')" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <div class="p-4 flex flex-col overflow-y-auto max-h-[75vh]">
            <!-- Add Regional Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs font-semibold text-[#1C4D8D] mb-2"><i class="fa-solid fa-plus mr-1"></i>Tambah Regional Baru</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    <input type="text" id="newRegionalRegion" placeholder="Region (Contoh: CJDO)"
                        class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                    <input type="text" id="newRegionalArea" placeholder="Area (Contoh: Semarang)"
                        class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                </div>
                <button onclick="saveNewRegional()"
                    class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition w-full md:w-auto">
                    <i class="fa-solid fa-save mr-1"></i> Simpan Regional
                </button>
            </div>

            <!-- Regional List -->
            <div class="overflow-y-auto flex-1">
                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center w-[60px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Region</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Area</th>
                            <th class="px-4 py-3 text-center w-[80px] border border-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody id="regionalManageBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function openRegionalManage() {
        document.getElementById('regionalManageModal').classList.remove('hidden');
        document.getElementById('regionalManageModal').classList.add('flex');
        loadRegionalManageList();
    }

    function loadRegionalManageList() {
        fetch("<?= base_url('dashboard/regionalList') ?>")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('regionalManageBody');
                tbody.innerHTML = '';
                if (!data.length) {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-xs">Belum ada data regional</td></tr>`;
                    return;
                }
                
                let no = 1;
                data.forEach(item => {
                    tbody.innerHTML += `
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="px-4 py-3 text-center text-xs border border-gray-300">${no++}</td>
                            <td class="px-4 py-3 text-left text-xs border border-gray-300">${item.region}</td>
                            <td class="px-4 py-3 text-left text-xs border border-gray-300">${item.area}</td>
                            <td class="px-4 py-3 text-center text-xs border border-gray-300">
                                <button type="button" onclick="deleteRegional(${item.id})" class="text-red-500 hover:text-red-600 transition" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            });
    }

    function saveNewRegional() {
        const region = document.getElementById('newRegionalRegion').value.trim();
        const area = document.getElementById('newRegionalArea').value.trim();

        if (!region || !area) {
            showToast('Region dan Area wajib diisi', 'warning');
            return;
        }

        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

        fetch("<?= base_url('dashboard/addRegional') ?>", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken
            },
            body: new URLSearchParams({ region, area, csrf_test_name: csrfToken })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast('Regional berhasil ditambahkan', 'success');
                document.getElementById('newRegionalRegion').value = '';
                document.getElementById('newRegionalArea').value = '';
                loadRegionalManageList();
                // We'll update the TomSelect instances when the modal closes or by reloading
                if (typeof window.refreshRegionalTomSelects === 'function') {
                    window.refreshRegionalTomSelects();
                }
            } else {
                showToast(res.msg ?? 'Gagal menambahkan regional', 'error');
            }
        });
    }

    function deleteRegional(id) {
        Swal.fire({
            title: 'Hapus Regional?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("<?= base_url('dashboard/deleteRegional') ?>/" + id, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast('Regional berhasil dihapus', 'success');
                        loadRegionalManageList();
                        if (typeof window.refreshRegionalTomSelects === 'function') {
                            window.refreshRegionalTomSelects();
                        }
                    } else {
                        showToast(res.msg ?? 'Gagal menghapus regional', 'error');
                    }
                });
            }
        });
    }
</script>
