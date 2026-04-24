<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-[1450px] mx-auto w-full flex-1 flex flex-col">
    <form method="get" class="bg-white p-2 rounded-md shadow mb-4 flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search..."
            class="border text-xs rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">

        <div>
            <select name="status" onchange="this.form.submit()"
                class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
                <option value="">Semua Status</option>
                <option value="Dibawa" <?= (($_GET['status'] ?? '') == 'Dibawa') ? 'selected' : '' ?>>Dibawa</option>
                <option value="Terpasang" <?= (($_GET['status'] ?? '') == 'Terpasang') ? 'selected' : '' ?>>Terpasang
                </option>
                <option value="Kembali" <?= (($_GET['status'] ?? '') == 'Kembali') ? 'selected' : '' ?>>Kembali</option>
            </select>
        </div>

        <div>
            <select name="filter_mutasi" onchange="this.form.submit()"
                class="border px-4 py-2 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1C4D8D]">
                <option value="">Semua Mutasi</option>
                <option value="belum" <?= (($_GET['filter_mutasi'] ?? '') == 'belum') ? 'selected' : '' ?>>Belum Mutasi
                </option>
                <option value="crosscheck" <?= (($_GET['filter_mutasi'] ?? '') == 'crosscheck') ? 'selected' : '' ?>>
                    Crosscheck INTAN</option>
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

        <a href="/history" class="bg-[#1C4D8D] px-3 py-2 text-xs rounded-lg hover:bg-[#7FB3D5] transition text-white">
            <span>Refresh
                <i class="fa-solid fa-redo"></i>
            </span>
        </a>
    </form>

    <div class="flex-1 bg-white rounded-md shadow flex flex-col overflow-hidden">
        <div class="flex-1 overflow-auto max-h-[calc(100vh-250px)]">

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
                    foreach ($history as $h): ?>

                        <tr class="text-[#656565] odd:bg-white even:bg-[#EFEFEF] hover:text-black">
                            <td class="px-4 py-3 text-center text-sm
                             text-blue-700 border border-gray-300">
                                <button type="button" onclick="openHistory(<?= $h['id_perangkat'] ?>)" class="hover:text-blue-400 mr-1 transition">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300"><?= $no++ ?></td>
                            <td class="px-4 py-3 text-xs text-left border border-gray-300"><?= esc($h['noreg']) ?></td>
                            <td class="px-4 py-3 text-xs text-left border border-gray-300"><?= esc($h['nm_perangkat']) ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-center border border-gray-300"><?= $h['nm_user'] ?? '-' ?>
                            </td>
                            <td
                                class="px-4 py-3 text-xs text-left border border-gray-300 break-words whitespace-normal max-w-[225px]">
                                <?= esc($h['keterangan']) ?: '-' ?>
                            </td>

                            <td class="px-4 py-3 text-xs text-center border border-gray-300">
                                <span class="px-2 py-1 rounded text-xs
                                    <?= $h['status'] == 'Dibawa' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                                    <?= $h['status'] == 'Terpasang' ? 'bg-blue-200 text-blue-800' : '' ?>
                                    <?= $h['status'] == 'Kembali' ? 'bg-green-200 text-green-800' : '' ?>
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

                            <td class="px-4 py-3 text-xs text-center border border-gray-300">
                                <?php if ($h['status'] == 'Terpasang'): ?>
                                    <?php if ($h['is_checked'] == 1): ?>
                                        <span class="px-2 py-1 rounded text-xs bg-green-400 text-white">Checked</span>
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
    }

    function closeModal(id) {
        document.getElementById(id).classList.add("hidden");
        document.getElementById(id).classList.remove("flex");
    }
    // HISTORY MODAL
    window.openHistory = function(id) {
        openModal("historyModal");

        const input = document.getElementById("searchHistory");
        input.dataset.id = id;

        loadHistory(id, 1);
    }

    function loadHistory(id, page = 1, search = '') {
        fetch(`<?= base_url('history/log') ?>/${id}`, {
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
</script>
<?= $this->endSection() ?>