<div id="historyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
    <div
        class="bg-white border border-gray-200 rounded-md w-full max-w-4xl shadow-xl flex flex-col max-h-[85vh] overflow-hidden">

        <div class="flex justify-between items-center px-6 py-4 border-b sticky top-0 bg-white z-10">
            <h2 class="text-lg text-[#1C4D8D] font-bold">History Perangkat</h2>

            <button onclick="closeModal('historyModal')"
                class="absolute right-3 top-2 text-black text-lg font-bold focus:outline-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 pt-4">
            <input type="text" id="searchHistory" value="<?= $_GET['searchHistory'] ?? '' ?>"
                class="border rounded px-3 py-2 w-full mb-4 sticky top-0" placeholder="Cari riwayat perangkat...">
        </div>

        <div class="flex-1 overflow-y-auto max-h-[400px] px-6 pb-4 rounded-md overflow-hidden">
            <div class="border shadow bg-white rounded-xl">

                <table class="min-w-full text-sm text-left">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-left">Update</th>
                            <th class="px-4 py-3 text-center">User</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody id="historyBody" class="divide-y"></tbody>

                </table>
            </div>
        </div>

        <div id="paginationHistory" class="flex justify-center gap-2 py-3 border-t"></div>

    </div>
</div>