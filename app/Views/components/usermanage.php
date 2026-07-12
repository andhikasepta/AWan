<style>
/* Native regional dropdown styling - match input fields */
.regional-dropdown {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: #fff;
    cursor: pointer;
}
.regional-dropdown::-ms-expand {
    display: none;
}
</style>
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

            <!-- Add User Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <p class="text-xs font-semibold text-[#1C4D8D] mb-2"><i class="fa-solid fa-user-plus mr-1"></i>Tambah User Baru</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    <input type="text" id="newUserInput" placeholder="Nama User"
                        class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none md:col-span-2">
                    <select id="newUserRegion" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="region">
                        <option value="">Pilih Region</option>
                    </select>
                    <select id="newUserArea" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="area" multiple placeholder="Pilih Area">
                    </select>
                </div>
                <button onclick="saveNewUser()"
                    class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition">
                    <i class="fa-solid fa-plus mr-1"></i> Simpan User
                </button>
            </div>

            <!-- User List -->
            <div class="mb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 text-xs"></i>
                    </div>
                    <input type="text" id="searchUser" placeholder="Cari Nama User..." class="w-full border border-gray-300 pl-8 pr-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
                </div>
            </div>

            <div class="overflow-y-auto flex-1">
                <table class="min-w-full text-sm text-left border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#0F2854] text-white">
                        <tr>
                            <th class="px-4 py-3 text-center w-[80px] border border-gray-300">Action</th>
                            <th class="px-4 py-3 text-center w-[80px] border border-gray-300">No</th>
                            <th class="px-4 py-3 text-left border border-gray-300">User</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Region</th>
                            <th class="px-4 py-3 text-left border border-gray-300">Area</th>
                        </tr>
                    </thead>

                    <tbody id="userManageBody"></tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div id="editUserModal" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[400px] overflow-hidden">
        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Edit User</h3>
            <button onclick="closeEditUser()" class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>
        <div class="p-4 flex flex-col overflow-y-auto max-h-[80vh]">
            <input type="hidden" id="editUserId">
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama User</label>
                <input type="text" id="editUserNama" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Region</label>
                <select id="editUserRegion" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="region">
                    <option value="">-- Pilih --</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Area</label>
                <select id="editUserArea" class="w-full border border-gray-300 px-3 py-2 rounded-md text-xs focus:ring-1 focus:ring-[#1C4D8D] outline-none regional-dropdown" data-type="area" multiple placeholder="Pilih Area">
                </select>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="closeEditUser()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-xs font-semibold transition">Batal</button>
                <button onclick="saveUserAction()" class="px-4 py-2 bg-[#1C4D8D] hover:bg-[#3E679E] text-white rounded-md text-xs font-semibold transition">Update</button>
            </div>
        </div>
    </div>
</div>