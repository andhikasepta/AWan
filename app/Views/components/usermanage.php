<div id="userManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">User Manage</h3>

            <button onclick="closeModal('userManageModal')"
                class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <div class="p-4 flex flex-col overflow-y-auto max-h-[70vh]">

            <div class="p-2 border-b flex justify-between items-center gap-2">
              <button onclick="addUser()" class="bg-[#1C4D8D] text-white px-4 py-2 rounded-md text-xs">
                <i class="fa-solid fa-plus"></i>
                Tambah User
              </button>

              <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchUser" placeholder="Cari User" class="border border-gray-400 pl-8 pr-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
              </div>
            </div>

            <div class="overflow-y-auto flex-1">
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