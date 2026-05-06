<div id="historyModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl overflow-hidden">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">History Perangkat</h3>

            <button onclick="closeModal('historyModal')"
                class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <div class="px-6 pt-4">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchHistory" value="<?= $_GET['searchHistory'] ?? '' ?>" placeholder="Cari User" class="border border-gray-400 pl-8 pr-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
            </div>
        </div>

        <div class="p-4 flex flex-col justify-between overflow-hidden">
            <div class="border shadow bg-white rounded-xl">

                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Update</th>
                            <th class="px-4 py-3 text-center border border-gray-300">User</th>
                            <th class="px-4 py-3 text-center border border-gray-300">Status</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody id="historyBody" class="divide-y"></tbody>

                </table>
            </div>
        </div>

        <div id="paginationHistory" class="flex justify-center gap-2 py-3 border-t"></div>

    </div>
</div>