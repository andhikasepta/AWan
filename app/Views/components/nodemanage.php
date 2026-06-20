<div id="nodeManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[600px] overflow-hidden flex flex-col max-h-[90vh]">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3 shrink-0">
            <h3 class="font-bold">Input Node</h3>
            <button onclick="closeNodeManage()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <div class="p-4 flex flex-col flex-1 overflow-hidden">

            <!-- Add Node Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg shrink-0">
                <p class="text-xs font-semibold text-[#1C4D8D] mb-2"><i
                        class="fa-solid fa-network-wired mr-1"></i>Tambah Node Baru</p>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <select id="newNodeArep"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                        <option value="">Pilih Arep...</option>
                        <option value="Semarang">Semarang</option>
                        <option value="Tegal">Tegal</option>
                        <option value="Solo">Solo</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                        <option value="Purwokerto">Purwokerto</option>
                    </select>
                    <input type="text" id="newNodeSentral" placeholder="Node Sentral (ex: SMGHCPGA01)"
                        class="border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none uppercase">
                </div>
                <button onclick="saveNewNode()"
                    class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition w-full">
                    <i class="fa-solid fa-plus mr-1"></i> Simpan Node
                </button>
            </div>

            <!-- Node List -->
            <div class="overflow-y-auto flex-1 min-h-[200px]">
                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center w-[70px] border border-gray-300">Action</th>
                            <th class="px-4 py-3 text-center w-[50px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Arep</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Node Sentral</th>
                        </tr>
                    </thead>
                    <tbody id="nodeManageBody">
                        <tr>
                            <td colspan="4" class="text-center py-4 text-xs text-gray-500"><i
                                    class="fa-solid fa-spinner fa-spin mr-1"></i> Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
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
    }

    function loadNodes() {
        fetch('<?= base_url('dashboard/nodeList') ?>')
            .then(res => res.json())
            .then(nodes => renderNodes(nodes))
            .catch(err => {
                document.getElementById('nodeManageBody').innerHTML = `<tr><td colspan="4" class="text-center py-4 text-xs text-red-500">Gagal memuat data</td></tr>`;
            });
    }

    function renderNodes(nodes) {
        const tbody = document.getElementById('nodeManageBody');
        tbody.innerHTML = '';

        if (!nodes.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-xs text-gray-500">Tidak ada data node</td></tr>`;
            return;
        }

        let no = 1;
        nodes.forEach(n => {
            tbody.innerHTML += `
                <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
                    <td class="px-4 py-3 text-center border border-gray-300">
                        <button type="button" onclick="deleteNode(${n.id})" class="text-red-500 hover:text-red-400 transition" title="Hapus">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center text-xs border border-gray-300">${no++}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300 font-semibold">${n.arep}</td>
                    <td class="px-4 py-3 text-left text-xs border border-gray-300 font-mono">${n.node_sentral}</td>
                </tr>`;
        });
    }

    function saveNewNode() {
        const arep = document.getElementById('newNodeArep').value;
        const nodeSentral = document.getElementById('newNodeSentral').value.trim().toUpperCase();

        if (!arep || !nodeSentral) {
            showToast('Pilih Arep dan isi Node Sentral', 'warning');
            return;
        }

        fetch('<?= base_url('dashboard/addNode') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ arep, node_sentral: nodeSentral })
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
</script>