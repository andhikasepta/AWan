<div id="userManageModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4 z-50">
    <div
        class="bg-white border border-gray-200 rounded-md w-full max-w-lg shadow-xl flex flex-col max-h-[85vh] overflow-hidden">

        <div class="flex justify-between items-center px-6 py-4 border-b sticky top-0 bg-white z-10">
            <h2 class="text-lg text-[#1C4D8D] font-bold">User Manage</h2>

            <button onclick="closeModal('userManageModal')"
                class="text-black text-lg font-bold focus:outline-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 pb-4 rounded-md">

            <div class="p-4 flex justify-between items-center">
              <button onclick="addUser()" class="bg-[#1C4D8D] text-white px-4 py-2 rounded-md text-xs">
                <i class="fa-solid fa-plus"></i>
                Tambah User
              </button>
            </div>

                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center w-[80px] border border-gray-300">Action</th>
                            <th class="px-4 py-3 text-center w-[80px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">User</th>
                        </tr>
                    </thead>

                    <tbody id="userManageBody"></tbody>

                </table>
            </div>
        </div>
    </div>
</div>