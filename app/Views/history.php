<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-[1450px] mx-auto w-full flex-1 flex flex-col">
    <?php $isBulk = strpos($_GET['search'] ?? '', ';') !== false; ?>
    <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center relative z-[20]">
        <div class="relative flex items-center transition-all duration-500 ease-in-out" id="searchContainer" style="width: <?= $isBulk ? '350px' : '200px' ?>;">
            <input type="hidden" name="type" value="<?= esc($currentType ?? 'perangkat') ?>">
            <input type="text" id="searchInput" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="<?= $isBulk ? 'Bulk search (pisahkan dengan ;)' : 'Search...' ?>"
                class="border text-xs rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-[#1C4D8D] pr-8 transition-all duration-500 ease-in-out">
            <button type="button" onclick="toggleBulkSearch()" class="absolute right-2 text-[#1C4D8D] hover:text-[#3E679E] transition" title="Toggle Bulk Search">
                <i class="fa-solid fa-layer-group"></i>
            </button>
        </div>

        <div class="w-48">
            <select id="filter_status" name="status" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
                <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang
                </option>
                <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
                <option value="Pengiriman" <?= (($_GET['status'] ?? '') == 'Pengiriman') ? 'selected' : '' ?>>Pengiriman
                </option>
                <option value="Terkirim" <?= (($_GET['status'] ?? '') == 'Terkirim') ? 'selected' : '' ?>>Terkirim</option>
            </select>
        </div>

        <div class="w-48">
            <select id="filter_mutasi" name="filter_mutasi" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
                <option value="">Semua Mutasi</option>
                <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi
                </option>
                <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>
                    Crosscheck INTAN</option>
                <option value="check" <?= (($_GET['filter_mutasi'] ?? '') == 'check') ? 'selected' : '' ?>>Checked</option>
            </select>
        </div>

        <div class="w-48">
            <select id="filter_user" name="user" onchange="this.form.submit()" class="w-full border border-gray-300 px-3 py-2 text-xs rounded-md focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white cursor-pointer">
                <option value="">Semua User</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (($_GET['user'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                        <?= esc($u['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="<?= base_url('history') ?>" class="bg-[#1C4D8D] px-3 py-2 text-xs rounded-lg hover:bg-[#7AAACE] transition text-white">
            <span>Refresh
                <i class="fa-solid fa-redo"></i>
            </span>
        </a>
    </form>



    <div class="flex-1 bg-white rounded-md shadow flex flex-col overflow-hidden">
        <div class="flex-1 overflow-auto max-h-[calc(100vh-290px)]">

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
            <table class="min-w-full text-xs text-left border border-gray-300">
                <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                    <tr>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300">Action</th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300">No</th>
                        <th class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('noreg')">No Registrasi <?= sortIcon('noreg', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('nama')">Nama Perangkat <?= sortIcon('nama', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('user')">User <?= sortIcon('user', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-left border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('keterangan')">Keterangan <?= sortIcon('keterangan', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('status')">Status <?= sortIcon('status', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('created')">Created <?= sortIcon('created', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('updated')">Updated <?= sortIcon('updated', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition text-nowrap"
                            onclick="sortTable('updated_by')">Updated By <?= sortIcon('updated_by', $currentSort, $currentDir) ?></th>
                        <th class="px-4 py-3 text-xs text-center border border-gray-300 cursor-pointer select-none hover:bg-[#1a3d6e] transition"
                            onclick="sortTable('mutasi')">Mutasi <?= sortIcon('mutasi', $currentSort, $currentDir) ?></th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <?php $no = ($currentPage - 1) * $limit + 1;
                    foreach ($history as $h): ?>

                        <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
                            <td class="px-4 py-3 text-center text-sm
                             text-blue-700 border border-gray-300">
                                <?php if (!empty($h['id_perangkat'])): ?>
                                    <button type="button" onclick="openHistory(<?= $h['id_perangkat'] ?>)"
                                        class="hover:text-blue-400 mr-1 transition">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                <?php elseif (!empty($h['id_non_reg'])): ?>
                                    <button type="button" onclick="openNonRegHistory(<?= $h['id_non_reg'] ?>, '<?= esc(addslashes($h['nm_perangkat'])) ?>')"
                                        class="hover:text-blue-400 mr-1 transition" title="View History">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                <?php endif; ?>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300"><?= $no++ ?></td>
                            <td class="px-4 py-3 text-xs text-left border border-gray-300"><?= esc($h['noreg']) ?></td>
                            <td
                                class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal min-w-[250px]">
                                <?= esc($h['nm_perangkat']) ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300 text-nowrap">
                                <?= $h['nm_user'] ?? '-' ?>
                            </td>
                            <td
                                class="px-4 py-3 text-left text-xs border border-gray-300 break-words whitespace-normal min-w-[250px]">
                                <?= esc($h['keterangan']) ?: '-' ?>
                            </td>

                            <td class="px-4 py-3 text-xs text-center border border-gray-300">
                                <span class="px-2 py-1 rounded text-xs
                                    <?= $h['status'] == 'Dibawa' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                                    <?= $h['status'] == 'Terpasang' ? 'bg-blue-200 text-blue-800' : '' ?>
                                    <?= $h['status'] == 'Kembali' ? 'bg-green-200 text-green-800' : '' ?>
                                    <?= $h['status'] == 'Pengiriman' ? 'bg-orange-200 text-orange-800' : '' ?>
                                    <?= $h['status'] == 'Terkirim' ? 'bg-purple-200 text-purple-800' : '' ?>
                                    ">
                                    <?= $h['status'] ?? '-' ?>
                                </span>
                            </td>

                            <td class="px-4 py-3 text-xs text-center border border-gray-300 text-nowrap">
                                <?= $h['created_at'] ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300 text-nowrap">
                                <?= $h['updated_at'] ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300 text-nowrap">
                                <?= esc($h['updated_by'] ?? '-') ?>
                            </td>

                            <td class="px-4 py-3 text-xs text-center border border-gray-300">
                                <?php if (in_array($h['status'], ['Terpasang', 'Terkirim'])): ?>
                                    <?php if ($h['is_checked'] == 1): ?>
                                        <span class="px-2 py-1 rounded text-xs bg-lime-400 text-lime-800">Checked</span>
                                    <?php else: ?>
                                        <span
                                            class="inline-block text-center whitespace-nowrap px-2 py-1 rounded text-xs bg-blue-500 text-white"
                                            data-id="<?= $h['id'] ?>">
                                            Crosscheck INTAN
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($h['is_checked'] == 0): ?>
                                        <span
                                            class="inline-block text-center whitespace-nowrap px-2 py-1 rounded text-xs bg-yellow-400 text-yellow-900">Belum
                                            Mutasi</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?= view('components/historyperangkat') ?>
    </div>

    <div class="py-1 mt-2">
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
    function openModal(id) {
        document.getElementById(id).classList.remove("hidden");
        document.getElementById(id).classList.add("flex");
    }

    function closeModal(id) {
        document.getElementById(id).classList.add("hidden");
        document.getElementById(id).classList.remove("flex");
    }
    // HISTORY MODAL
    window.openHistory = function (id) {
        openModal("historyModal");

        const input = document.getElementById("searchHistory");
        input.dataset.id = id;
        input.dataset.type = 'perangkat';

        loadHistory(id, 1);
    }
    
    window.openNonRegHistory = function (id, name) {
        openModal("historyModal");

        const input = document.getElementById("searchHistory");
        input.dataset.id = id;
        input.dataset.type = 'nonreg';

        loadHistory(id, 1, '', 'nonreg');
    }

    function loadHistory(id, page = 1, search = '', type = 'perangkat') {
        const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';

        const url = type === 'nonreg' ? `<?= base_url('dashboard/nonreg_history') ?>/${id}` : `<?= base_url('history/log') ?>/${id}`;

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken
            },
            body: `page=${page}&searchHistory=${encodeURIComponent(search)}&csrf_test_name=${csrfToken}`
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

                let no = (res.currentPage - 1) * 10 + 1;

                res.data.forEach(row => {
                    tbody.innerHTML += `
          <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
            <td class="px-4 py-3 text-center">${no++}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[125px]">${row.updated_at ?? '-'}</td>
            <td class="px-4 py-3 text-center">${row.nm_user ?? '-'}</td>
            <td class="px-4 py-3 text-center">${row.status ?? '-'}</td>            
            <td class="px-4 py-3 text-center">${row.updated_by ?? '-'}</td>
            <td class="px-4 py-3 text-left break-words whitespace-normal max-w-[200px]">${row.keterangan ?? '-'}</td>
          </tr>`;
                });

                renderPagination(id, res.currentPage, res.totalPage, search, type);
            });
    }

    function renderPagination(id, currentPage, totalPage, search, type = 'perangkat') {
        let container = document.getElementById("paginationHistory");

        container.innerHTML = "";

        for (let i = 1; i <= totalPage; i++) {
            container.innerHTML += `
      <button onclick="loadHistory(${id}, ${i}, '${search}', '${type}')" class="px-3 py-1 text-xs rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-200'}">${i}</button>`;
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
            let type = this.dataset.type || 'perangkat';
            loadHistory(id, 1, this.value, type);
        });
    }
</script>
<script>
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


</script>
<?= $this->endSection() ?>