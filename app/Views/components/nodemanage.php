<div id="nodeManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[560px] overflow-hidden flex flex-col max-h-[90vh]">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3 shrink-0">
            <h3 class="font-bold">Input Node</h3>
            <button onclick="closeNodeManage()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button type="button" onclick="switchNodeTab('manual')" id="tabNodeManual"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">
                <i class="fa-solid fa-keyboard mr-1"></i> Input Manual
            </button>
            <button type="button" onclick="switchNodeTab('csv')" id="tabNodeCsv"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]">
                <i class="fa-solid fa-file-csv mr-1"></i> Import File
            </button>
        </div>

        <div class="p-4 flex flex-col flex-1 overflow-hidden" id="tabContentNodeManual">
            <!-- Add Node Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg shrink-0">
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <select id="newNodeArep"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                        <option value="">Pilih Arep...</option>
                        <option value="Semarang">Semarang</option>
                        <option value="Tegal">Tegal</option>
                        <option value="Solo">Solo</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                        <option value="Purwokerto">Purwokerto</option>
                    </select>
                    <input type="text" id="newNodeSite" placeholder="Site Sentral"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none uppercase">
                    <input type="text" id="newNodeSentral" placeholder="Node Sentral (Opsional)"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none uppercase" oninput="if(this.value.length >= 6) document.getElementById('newNodeSite').value = this.value.substring(0, 6).toUpperCase();">
                </div>
                <button onclick="saveNewNode()"
                    class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition w-full">
                    <i class="fa-solid fa-plus mr-1"></i> Simpan Node
                </button>
            </div>

            <!-- Node Bulk Actions -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex gap-2">
                    <button type="button" id="btnBulkDeleteNodes" onclick="bulkDeleteNodes()" disabled
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus Terpilih
                    </button>
                    <button type="button" onclick="deleteAllNodes()"
                        class="bg-red-700 hover:bg-red-800 text-white px-3 py-1.5 rounded text-xs font-semibold shadow-sm transition">
                        <i class="fa-solid fa-dumpster-fire mr-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Node List -->
            <div class="mb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 text-xs"></i>
                    </div>
                    <input type="text" id="searchNode" onkeyup="filterNodes()" placeholder="Cari Arep / Site / Node..." class="w-full border border-gray-300 pl-8 pr-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                </div>
            </div>
            <div class="overflow-y-auto flex-1 min-h-[200px]">
                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-2 py-3 text-center w-[40px] border border-gray-300">
                                <input type="checkbox" id="selectAllNodes" onclick="toggleAllNodeCheckboxes(this)" class="w-3 h-3 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                            </th>
                            <th class="px-4 py-3 text-center w-[70px] border border-gray-300">Action</th>
                            <th class="px-4 py-3 text-center w-[50px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Arep</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Site Sentral</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Node Sentral</th>
                        </tr>
                    </thead>
                    <tbody id="nodeManageBody">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-xs text-gray-500"><i
                                    class="fa-solid fa-spinner fa-spin mr-1"></i> Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CSV/Excel Import Tab Content -->
        <div id="tabContentNodeCsv" class="hidden flex flex-col flex-1 overflow-hidden">
            <div id="nodeCsvUploadZone" class="p-4">
                <p class="text-xs text-blue-800 mb-2">
                    <i class="fa-solid fa-circle-info mr-1 text-xs"></i>
                    PENTING: Pastikan Anda telah mengunduh dan mengisi Template yang tersedia sebelum melakukan impor!
                </p>
                <div id="nodeCsvDropZone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-[#1C4D8D] hover:bg-blue-50/50 transition-all duration-200">
                    <div id="nodeCsvDropZoneDefault">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 font-medium">Drag & drop file di sini</p>
                        <p class="text-xs text-gray-400 mt-1">atau klik untuk memilih file</p>
                    </div>
                    <div id="nodeCsvDropZoneLoading" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-[#1C4D8D] mb-2"></i>
                        <p class="text-sm text-[#1C4D8D] font-medium mt-2">Memproses data...</p>
                    </div>
                    <input type="file" id="nodeCsvFileInput" accept=".csv,.xlsx,.xls" class="hidden">
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded">.csv</code>
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded">.xlsx</code> <code
                            class="bg-gray-100 px-1.5 py-0.5 rounded">.xls</code>
                    </p>
                    <button type="button" onclick="downloadNodeCsvTemplate()"
                        class="text-xs text-[#1C4D8D] hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Download Template
                    </button>
                </div>
            </div>

            <div id="nodeCsvPreviewArea" class="hidden flex flex-col flex-1 overflow-hidden">
                <div class="px-4 pb-2 pt-2 flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-file-lines text-gray-500"></i>
                            <span id="nodeCsvTotalCount" class="font-semibold">0</span> total
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <i class="fa-solid fa-circle-check"></i>
                            <span id="nodeCsvValidCount" class="font-semibold">0</span> tersedia
                        </span>
                        <span class="flex items-center gap-1 text-red-500">
                            <i class="fa-solid fa-circle-xmark text-nowrap"></i>
                            <span id="nodeCsvDupCount" class="font-semibold">0</span> duplikat
                        </span>
                        <span class="flex items-center gap-1 text-yellow-600">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="nodeCsvInvCount" class="font-semibold">0</span> invalid
                        </span>
                    </div>
                    <button type="button" onclick="resetNodeCsvImport()"
                        class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>

                <div class="flex-1 overflow-auto px-4 max-h-[30vh] min-h-[150px]">
                    <table class="min-w-full text-xs border border-gray-300">
                        <thead class="sticky top-0 bg-[#0F2854] text-white z-10">
                            <tr>
                                <th class="px-3 py-2 text-center border border-gray-300 w-10">No</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Arep</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Node Sentral</th>
                                <th class="px-3 py-2 text-center border border-gray-300 w-32">Status</th>
                            </tr>
                        </thead>
                        <tbody id="nodeCsvPreviewBody"></tbody>
                    </table>
                </div>

                <div class="p-4 flex justify-end gap-2 border-t border-gray-100 mt-2 shrink-0">
                    <button type="button" id="btn_import_node_csv" onclick="submitNodeCsvImport()" disabled
                        class="bg-[#1C4D8D] text-white text-sm px-4 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i class="fa-solid fa-file-import"></i>
                        <span>Import Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.openNodeManage = function () {
        document.getElementById('nodeManageModal').classList.remove('hidden');
        document.getElementById('nodeManageModal').classList.add('flex');
        loadNodes();
    }

    function closeNodeManage() {
        document.getElementById('nodeManageModal').classList.add('hidden');
        document.getElementById('nodeManageModal').classList.remove('flex');
        document.getElementById('newNodeArep').value = '';
        document.getElementById('newNodeSentral').value = '';
        document.getElementById('searchNode').value = '';
    }

    let allNodes = [];

    function loadNodes() {
        fetch('<?= base_url('dashboard/nodeList') ?>')
            .then(res => res.json())
            .then(nodes => {
                allNodes = nodes;
                renderNodes(nodes);
            })
            .catch(err => {
                document.getElementById('nodeManageBody').innerHTML = `<tr><td colspan="4" class="text-center py-4 text-xs text-red-500">Gagal memuat data</td></tr>`;
            });
    }

    function filterNodes() {
        const term = document.getElementById('searchNode').value.toLowerCase();
        const filtered = allNodes.filter(n => 
            (n.arep && n.arep.toLowerCase().includes(term)) || 
            (n.site_sentral && n.site_sentral.toLowerCase().includes(term)) ||
            (n.node_sentral && n.node_sentral.toLowerCase().includes(term))
        );
        renderNodes(filtered);
    }

    function renderNodes(nodes) {
        const tbody = document.getElementById('nodeManageBody');
        tbody.innerHTML = '';

        const selectAll = document.getElementById('selectAllNodes');
        if (selectAll) selectAll.checked = false;
        checkNodeBulkButton();

        if (!nodes.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-xs text-gray-500">Tidak ada data node</td></tr>`;
            return;
        }

        let no = 1;
        nodes.forEach(n => {
            tbody.innerHTML += `
                <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
                    <td class="px-2 py-3 text-center border border-gray-300">
                        <input type="checkbox" class="node-row-checkbox w-3 h-3 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500" value="${n.id}" onchange="checkNodeBulkButton()">
                    </td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="editNode(${n.id}, '${(n.arep||'').replace(/'/g, "\\'")}', '${(n.site_sentral||'').replace(/'/g, "\\'")}', '${(n.node_sentral||'').replace(/'/g, "\\'")}')" class="text-blue-500 hover:text-blue-400 transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <button type="button" onclick="deleteNode(${n.id})" class="text-red-500 hover:text-red-400 transition" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center text-xs border border-gray-300">${no++}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300 font-semibold">${n.arep || '-'}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300 font-mono">${n.site_sentral || '-'}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300 font-mono">${n.node_sentral || '-'}</td>
                </tr>`;
        });
    }

    function saveNewNode() {
        const arep = document.getElementById('newNodeArep').value;
        const nodeSentral = document.getElementById('newNodeSentral').value.trim().toUpperCase() || '-';
        const siteSentral = document.getElementById('newNodeSite').value.trim().toUpperCase();

        if (!arep || !siteSentral) {
            showToast('Pilih Arep dan isi Site Sentral', 'warning');
            return;
        }

        fetch('<?= base_url('dashboard/addNode') ?>', {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: new URLSearchParams({ arep, site_sentral: siteSentral, node_sentral: nodeSentral })
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    showToast('Node berhasil ditambahkan', 'success');
                    document.getElementById('newNodeSentral').value = '';
                    loadNodes();
                } else {
                    showToast(res.msg ?? 'Gagal menambahkan node', 'error');
                }
            });
    }

    function editNode(id, oldArep, oldSite, oldNode) {
        Swal.fire({
            title: '',
            html: `
                <div style="margin: -20px -20px 0 -20px;">
                    <div style="background: #1C4D8D; color: white; padding: 14px 20px; display: flex; align-items: center; gap: 8px; border-radius: 8px 8px 0 0;">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span style="font-weight: 700; font-size: 15px;">Edit Node</span>
                    </div>
                    <div style="padding: 20px 20px 4px 20px;">
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; text-align: left;">Arep</label>
                            <select id="swal-arep" style="width: 100%; border: 1px solid #D1D5DB; border-radius: 6px; padding: 9px 12px; font-size: 12px; outline: none; background: #fff; color: #374151; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1C4D8D'; this.style.boxShadow='0 0 0 2px rgba(28,77,141,0.12)'" onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option value="Semarang" ${oldArep === 'Semarang' ? 'selected' : ''}>Semarang</option>
                                <option value="Tegal" ${oldArep === 'Tegal' ? 'selected' : ''}>Tegal</option>
                                <option value="Solo" ${oldArep === 'Solo' ? 'selected' : ''}>Solo</option>
                                <option value="Yogyakarta" ${oldArep === 'Yogyakarta' ? 'selected' : ''}>Yogyakarta</option>
                                <option value="Purwokerto" ${oldArep === 'Purwokerto' ? 'selected' : ''}>Purwokerto</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; text-align: left;">Site Sentral</label>
                            <input id="swal-site" value="${oldSite}" placeholder="Site Sentral" style="width: 100%; border: 1px solid #D1D5DB; border-radius: 6px; padding: 9px 12px; font-size: 12px; outline: none; text-transform: uppercase; font-family: 'Courier New', monospace; letter-spacing: 0.5px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1C4D8D'; this.style.boxShadow='0 0 0 2px rgba(28,77,141,0.12)'" onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                        </div>
                        <div style="margin-bottom: 6px;">
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; text-align: left;">Node Sentral <span style="font-weight: 400; color: #9CA3AF;">(Opsional)</span></label>
                            <input id="swal-node" value="${oldNode !== '-' ? oldNode : ''}" placeholder="Node Sentral" style="width: 100%; border: 1px solid #D1D5DB; border-radius: 6px; padding: 9px 12px; font-size: 12px; outline: none; text-transform: uppercase; font-family: 'Courier New', monospace; letter-spacing: 0.5px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#1C4D8D'; this.style.boxShadow='0 0 0 2px rgba(28,77,141,0.12)'" onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'" oninput="if(this.value.length >= 6) document.getElementById('swal-site').value = this.value.substring(0, 6).toUpperCase();">
                        </div>
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Update',
            cancelButtonText: '<i class="fa-solid fa-xmark mr-1"></i> Batal',
            confirmButtonColor: '#1C4D8D',
            cancelButtonColor: '#6B7280',
            customClass: {
                popup: 'swal2-edit-node-popup',
                confirmButton: 'swal2-edit-node-confirm',
                cancelButton: 'swal2-edit-node-cancel',
            },
            width: '420px',
            padding: '0 0 20px 0',
            preConfirm: () => {
                const arep = document.getElementById('swal-arep').value;
                const site_sentral = document.getElementById('swal-site').value.trim().toUpperCase();
                const node_sentral = document.getElementById('swal-node').value.trim().toUpperCase() || '-';
                if (!arep || !site_sentral) {
                    Swal.showValidationMessage('Arep dan Site Sentral wajib diisi');
                }
                return { arep, site_sentral, node_sentral };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';
                
                fetch('<?= base_url('dashboard/updateNode') ?>/' + id, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: new URLSearchParams({
                        ...result.value,
                        csrf_test_name: csrfToken
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast('Node berhasil diupdate', 'success');
                        loadNodes();
                    } else {
                        showToast(res.msg ?? 'Gagal update node', 'error');
                    }
                });
            }
        });
    }

    function deleteNode(id) {
        Swal.fire({
            title: 'Hapus node ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?= base_url('dashboard/deleteNode') ?>/' + id, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            showToast('Node berhasil dihapus', 'success');
                            loadNodes();
                        } else {
                            showToast(res.msg ?? 'Gagal menghapus node', 'error');
                        }
                    });
            }
        });
    }

    function toggleAllNodeCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.node-row-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        checkNodeBulkButton();
    }

    function checkNodeBulkButton() {
        const checkboxes = document.querySelectorAll('.node-row-checkbox:checked');
        const btn = document.getElementById('btnBulkDeleteNodes');
        if (btn) {
            btn.disabled = checkboxes.length === 0;
            if (checkboxes.length > 0) {
                btn.innerHTML = `<i class="fa-solid fa-trash-can mr-1"></i> Hapus (${checkboxes.length})`;
            } else {
                btn.innerHTML = `<i class="fa-solid fa-trash-can mr-1"></i> Hapus Terpilih`;
            }
        }
    }

    function bulkDeleteNodes() {
        const checked = document.querySelectorAll('.node-row-checkbox:checked');
        if (checked.length === 0) return;

        const ids = Array.from(checked).map(cb => cb.value);

        Swal.fire({
            title: `Hapus ${ids.length} node?`,
            text: "Node yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';
                
                fetch('<?= base_url('dashboard/bulkDeleteNodes') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        showToast(`Berhasil menghapus ${res.deleted} node`, 'success');
                        loadNodes();
                    } else {
                        showToast(res.message || 'Gagal menghapus node', 'error');
                    }
                })
                .catch(() => showToast('Kesalahan server', 'error'));
            }
        });
    }

    function deleteAllNodes() {
        Swal.fire({
            title: 'Hapus SEMUA Node?',
            text: "Tindakan ini akan mengosongkan seluruh data Node dan sangat berbahaya! Lanjutkan?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Kosongkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Ini adalah konfirmasi terakhir. Semua Node akan lenyap secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'KONFIRMASI HAPUS SEMUA',
                    cancelButtonText: 'Batal'
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
                        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

                        fetch('<?= base_url('dashboard/deleteAllNodes') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                showToast('Seluruh data Node telah dikosongkan', 'success');
                                loadNodes();
                            } else {
                                showToast(res.message || 'Gagal menghapus semua node', 'error');
                            }
                        })
                        .catch(() => showToast('Kesalahan server', 'error'));
                    }
                });
            }
        });
    }

    // Tabs Logic
    function switchNodeTab(tab) {
        const tabManual = document.getElementById('tabNodeManual');
        const tabCsv = document.getElementById('tabNodeCsv');
        const contentManual = document.getElementById('tabContentNodeManual');
        const contentCsv = document.getElementById('tabContentNodeCsv');

        const activeClass = "flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]";
        const inactiveClass = "flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]";

        if (tab === 'manual') {
            tabManual.className = activeClass;
            tabCsv.className = inactiveClass;
            contentManual.classList.remove('hidden');
            contentCsv.classList.add('hidden');
        } else {
            tabCsv.className = activeClass;
            tabManual.className = inactiveClass;
            contentCsv.classList.remove('hidden');
            contentManual.classList.add('hidden');
        }
    }

    // CSV/Excel Download Template
    function downloadNodeCsvTemplate() {
        if (typeof XLSX !== 'undefined') {
            const ws = XLSX.utils.json_to_sheet([
                { arep: "Semarang", node_sentral: "SMGHCPGA01" },
                { arep: "Solo", node_sentral: "SLOHCPGA02" }
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template");
            XLSX.writeFile(wb, "template_node.xlsx");
        } else {
            const csvContent = "arep,node_sentral\nSemarang,SMGHCPGA01\nSolo,SLOHCPGA02";
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template_node.csv';
            a.click();
            URL.revokeObjectURL(url);
        }
    }

    // Drag and Drop & File Select
    const nodeCsvDropZone = document.getElementById('nodeCsvDropZone');
    const nodeCsvFileInput = document.getElementById('nodeCsvFileInput');
    let nodeCsvParsedData = [];

    nodeCsvDropZone.addEventListener('click', () => nodeCsvFileInput.click());
    nodeCsvDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        nodeCsvDropZone.classList.add('border-[#1C4D8D]', 'bg-blue-50');
    });
    nodeCsvDropZone.addEventListener('dragleave', () => {
        nodeCsvDropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
    });
    nodeCsvDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        nodeCsvDropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
        if (e.dataTransfer.files.length) {
            handleNodeCsvFile(e.dataTransfer.files[0]);
        }
    });
    nodeCsvFileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleNodeCsvFile(e.target.files[0]);
        }
    });

    function resetNodeCsvDropZoneUI() {
        const defaultZone = document.getElementById('nodeCsvDropZoneDefault');
        const loadingZone = document.getElementById('nodeCsvDropZoneLoading');
        if (defaultZone && loadingZone) {
            defaultZone.classList.remove('hidden');
            loadingZone.classList.add('hidden');
        }
    }

    function parseNodeCsvLine(line, delimiter = ',') {
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

    function handleNodeCsvFile(file) {
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        const validExts = ['csv', 'xlsx', 'xls'];

        if (!validExts.includes(ext)) {
            showToast('Format file harus .csv, .xlsx, atau .xls', 'warning');
            return;
        }

        const defaultZone = document.getElementById('nodeCsvDropZoneDefault');
        const loadingZone = document.getElementById('nodeCsvDropZoneLoading');
        if (defaultZone && loadingZone) {
            defaultZone.classList.add('hidden');
            loadingZone.classList.remove('hidden');
        }

        setTimeout(() => {
            if (ext === 'csv') {
            const reader = new FileReader();
            reader.onload = function (e) {
                const text = e.target.result;
                const lines = text.split(/\r?\n/).filter(l => l.trim());

                if (lines.length < 2) {
                    showToast('File kosong atau hanya berisi header', 'warning');
                    resetNodeCsvDropZoneUI();
                    return;
                }

                let delimiter = ',';
                const firstLine = lines[0];
                if ((firstLine.match(/;/g) || []).length > (firstLine.match(/,/g) || []).length) {
                    delimiter = ';';
                }

                const header = parseNodeCsvLine(lines[0], delimiter).map(h => h.toLowerCase().replace(/[^a-z0-9]/g, ''));
                const arepIdx = header.indexOf('arep');
                const nodeIdx = header.indexOf('nodesentral');

                if (arepIdx === -1 || nodeIdx === -1) {
                    showToast('Header harus mengandung kolom "arep" dan "node_sentral"', 'error');
                    resetNodeCsvDropZoneUI();
                    return;
                }

                nodeCsvParsedData = [];
                for (let i = 1; i < lines.length; i++) {
                    const cols = parseNodeCsvLine(lines[i], delimiter);
                    const arep = (cols[arepIdx] || '').trim();
                    const node_sentral = (cols[nodeIdx] || '').trim().toUpperCase();

                    if (arep || node_sentral) {
                        nodeCsvParsedData.push({ arep, node_sentral, status: 'checking', message: 'Memeriksa...' });
                    }
                }
                finishNodeFileParse();
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
                        showToast('File Excel kosong', 'warning');
                        resetNodeCsvDropZoneUI();
                        return;
                    }

                    const firstRow = jsonData[0];
                    const keys = Object.keys(firstRow).map(k => k.toLowerCase().replace(/[^a-z0-9]/g, ''));
                    if (!keys.includes('arep') || !keys.includes('nodesentral')) {
                        showToast('Header harus mengandung kolom "arep" dan "node_sentral"', 'error');
                        resetNodeCsvDropZoneUI();
                        return;
                    }

                    nodeCsvParsedData = [];
                    jsonData.forEach(row => {
                        let arep = '';
                        let node_sentral = '';
                        for (let k in row) {
                            const cleanKey = k.toLowerCase().replace(/[^a-z0-9]/g, '');
                            if (cleanKey === 'arep') arep = String(row[k]).trim();
                            if (cleanKey === 'nodesentral') node_sentral = String(row[k]).trim().toUpperCase();
                        }
                        if (arep || node_sentral) {
                            nodeCsvParsedData.push({ arep, node_sentral, status: 'checking', message: 'Memeriksa...' });
                        }
                    });
                    finishNodeFileParse();
                } catch (err) {
                    showToast('Gagal membaca file Excel', 'error');
                    resetNodeCsvDropZoneUI();
                }
            };
            reader.readAsArrayBuffer(file);
        }
        }, 50);
    }

    function finishNodeFileParse() {
        document.getElementById('nodeCsvUploadZone').classList.add('hidden');
        document.getElementById('nodeCsvPreviewArea').classList.remove('hidden');
        document.getElementById('nodeCsvPreviewArea').classList.add('flex');
        
        nodeCsvParsedData.forEach(row => {
            if (!row.arep || !row.node_sentral) {
                row.status = 'invalid';
                row.message = 'Arep/Node Kosong';
            } else {
                row.status = 'valid';
                row.message = 'Tersedia';
            }
        });
        
        renderNodeCsvPreview();
        
        const validCount = nodeCsvParsedData.filter(r => r.status === 'valid').length;
        document.getElementById('btn_import_node_csv').disabled = (validCount === 0);
    }

    function renderNodeCsvPreview() {
        const tbody = document.getElementById('nodeCsvPreviewBody');
        tbody.innerHTML = '';
        let valid = 0, invalid = 0, dup = 0;

        nodeCsvParsedData.forEach((row, idx) => {
            if (row.status === 'valid') valid++;
            else if (row.status === 'duplicate') dup++;
            else invalid++;

            let statusBadge = '';
            if (row.status === 'checking') statusBadge = `<span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px]"><i class="fa-solid fa-spinner fa-spin mr-1"></i>${row.message}</span>`;
            else if (row.status === 'valid') statusBadge = `<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px]"><i class="fa-solid fa-check mr-1"></i>${row.message}</span>`;
            else if (row.status === 'duplicate') statusBadge = `<span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px]"><i class="fa-solid fa-xmark mr-1"></i>${row.message}</span>`;
            else statusBadge = `<span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-[10px]"><i class="fa-solid fa-exclamation mr-1"></i>${row.message}</span>`;

            tbody.innerHTML += `
                <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-100">
                    <td class="px-3 py-2 text-center text-gray-500">${idx + 1}</td>
                    <td class="px-3 py-2 font-medium text-gray-700">${row.arep || '-'}</td>
                    <td class="px-3 py-2 font-mono text-[#1C4D8D]">${row.node_sentral || '-'}</td>
                    <td class="px-3 py-2 text-center">${statusBadge}</td>
                </tr>
            `;
        });

        document.getElementById('nodeCsvTotalCount').innerText = nodeCsvParsedData.length;
        document.getElementById('nodeCsvValidCount').innerText = valid;
        document.getElementById('nodeCsvDupCount').innerText = dup;
        document.getElementById('nodeCsvInvCount').innerText = invalid;
    }

    function resetNodeCsvImport() {
        nodeCsvParsedData = [];
        document.getElementById('nodeCsvFileInput').value = '';
        document.getElementById('nodeCsvUploadZone').classList.remove('hidden');
        document.getElementById('nodeCsvPreviewArea').classList.add('hidden');
        document.getElementById('nodeCsvPreviewArea').classList.remove('flex');
        resetNodeCsvDropZoneUI();
    }

    function submitNodeCsvImport() {
        const btn = document.getElementById('btn_import_node_csv');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

        const validRows = nodeCsvParsedData.filter(r => r.status === 'valid').map(r => ({ arep: r.arep, node_sentral: r.node_sentral }));

        fetch('<?= base_url('dashboard/importNodes') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ rows: validRows })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showToast(`Berhasil import ${res.inserted} node`, 'success');
                resetNodeCsvImport();
                loadNodes();
                switchNodeTab('manual');
            } else {
                showToast(res.message || 'Gagal import node', 'error');
            }
        })
        .catch(err => showToast('Kesalahan server', 'error'))
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>