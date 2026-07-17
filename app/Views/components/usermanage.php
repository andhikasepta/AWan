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
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[600px] overflow-hidden flex flex-col max-h-[90vh]">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3 shrink-0">
            <h3 class="font-bold">User Manage</h3>
            <button onclick="closeModal('userManageModal')"
                class="text-white hover:text-gray-400 transition">
                <i class="fa-solid fa-xmark fa-xl"></i>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button type="button" onclick="switchUserTab('manual')" id="tabUserManual"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">
                <i class="fa-solid fa-keyboard mr-1"></i> Input Manual
            </button>
            <button type="button" onclick="switchUserTab('csv')" id="tabUserCsv"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]">
                <i class="fa-solid fa-file-csv mr-1"></i> Import File
            </button>
        </div>

        <div class="p-4 flex flex-col flex-1 overflow-hidden" id="tabContentUserManual">
            <!-- Add User Form -->
            <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg overflow-visible relative z-20 shrink-0">
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
                    class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white px-4 py-2 rounded-md text-xs font-semibold transition w-full md:w-auto">
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

            <div class="overflow-y-auto flex-1 min-h-[200px]">
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

        <!-- CSV/Excel Import Tab Content -->
        <div id="tabContentUserCsv" class="hidden flex flex-col flex-1 overflow-hidden">
            <div id="userCsvUploadZone" class="p-4">
                <p class="text-xs text-blue-800 mb-2">
                    <i class="fa-solid fa-circle-info mr-1 text-xs"></i>
                    PENTING: Pastikan Anda telah mengunduh dan mengisi Template yang tersedia sebelum melakukan impor!
                </p>
                <div id="userCsvDropZone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-[#1C4D8D] hover:bg-blue-50/50 transition-all duration-200">
                    <div id="userCsvDropZoneDefault">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 font-medium">Drag & drop file di sini</p>
                        <p class="text-xs text-gray-400 mt-1">atau klik untuk memilih file</p>
                    </div>
                    <div id="userCsvDropZoneLoading" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-[#1C4D8D] mb-2"></i>
                        <p class="text-sm text-[#1C4D8D] font-medium mt-2">Memproses data...</p>
                    </div>
                    <input type="file" id="userCsvFileInput" accept=".csv,.xlsx,.xls" class="hidden">
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded">.csv</code>
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded">.xlsx</code> <code
                            class="bg-gray-100 px-1.5 py-0.5 rounded">.xls</code>
                    </p>
                    <button type="button" onclick="downloadUserCsvTemplate()"
                        class="text-xs text-[#1C4D8D] hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Download Template
                    </button>
                </div>
            </div>

            <div id="userCsvPreviewArea" class="hidden flex flex-col flex-1 overflow-hidden">
                <div class="px-4 pb-2 pt-2 flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-users text-gray-500"></i>
                            <span id="userCsvTotalCount" class="font-semibold">0</span> total
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <i class="fa-solid fa-circle-check"></i>
                            <span id="userCsvValidCount" class="font-semibold">0</span> valid
                        </span>
                        <span class="flex items-center gap-1 text-red-500">
                            <i class="fa-solid fa-circle-xmark text-nowrap"></i>
                            <span id="userCsvDupCount" class="font-semibold">0</span> duplikat
                        </span>
                        <span class="flex items-center gap-1 text-yellow-600">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="userCsvInvCount" class="font-semibold">0</span> invalid
                        </span>
                    </div>
                    <button type="button" onclick="resetUserCsvImport()"
                        class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>

                <div class="flex-1 overflow-auto px-4 max-h-[40vh] min-h-[150px]">
                    <table class="min-w-full text-xs border border-gray-300">
                        <thead class="sticky top-0 bg-[#0F2854] text-white z-10">
                            <tr>
                                <th class="px-3 py-2 text-center border border-gray-300 w-10">No</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Nama User</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Region</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Area</th>
                                <th class="px-3 py-2 text-center border border-gray-300 w-32">Status</th>
                            </tr>
                        </thead>
                        <tbody id="userCsvPreviewBody"></tbody>
                    </table>
                </div>

                <div class="p-4 flex justify-end gap-2 border-t border-gray-100 mt-2 shrink-0">
                    <button type="button" id="btn_import_user_csv" onclick="submitUserCsvImport()" disabled
                        class="bg-[#1C4D8D] text-white text-sm px-4 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i class="fa-solid fa-file-import"></i>
                        <span>Import Data</span>
                    </button>
                </div>
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