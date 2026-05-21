<div id="tambahModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] max-h-[85vh] overflow-hidden flex flex-col transition-all duration-300"
        id="tambahModalContent" style="max-width:500px">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Tambah Perangkat</h3>
            <button onclick="closeModal('tambahModal')" class="text-white hover:text-gray-300 transition">
                <i class="fa-solid fa-xmark fa-lg"></i>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button type="button" onclick="switchTambahTab('manual')" id="tabManual"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">
                <i class="fa-solid fa-keyboard mr-1"></i> Input Manual
            </button>
            <button type="button" onclick="switchTambahTab('csv')" id="tabCsv"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]">
                <i class="fa-solid fa-file-csv mr-1"></i> Import File
            </button>
        </div>

        <div id="tabContentManual">
            <form id="tambahperangkat" class="p-4 flex flex-col justify-between">
                <input type="hidden" name="id" id="tambah_id">

                <div class="grid grid-cols-1 md:grid-cols-[3fr_1fr] gap-6 mb-5">
                    <div class="w-full flex flex-col relative">
                        <label class="font-semibold text-[#1C4D8D] text-sm">Kode Spec</label>
                        <select id="kode_spec" name="id_spec" placeholder="Masukkan kode spec"></select>

                        <div id="namaWrapper">
                            <input type="text" id="nama" name="nama" placeholder="Masukkan nama perangkat"
                                class="w-full border mt-2 rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="font-semibold text-[#1C4D8D] text-sm">Kode ID</label>
                        <input type="text" id="kode_id" name="kode_id" placeholder="Kode ID"
                            class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                    </div>
                </div>

                <small id="noregWarning" class="text-red-500 hidden">
                    Nomor Registrasi sudah terdaftar!
                </small>

                <small id="noregOK" class="text-green-500 hidden">
                    Nomor Registrasi tersedia
                </small>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="closeModal('tambahModal')"
                        class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                    <button type="submit" id="btn_submit_tambah"
                        class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">Simpan</button>
                </div>

            </form>
        </div>

        <div id="tabContentCsv" class="hidden flex flex-col flex-1 overflow-hidden">

            <div id="csvUploadZone" class="p-4">
                <p class="text-xs text-blue-800 mb-2">
                    <i class="fa-solid fa-circle-info mr-1 text-xs"></i>
                    PENTING: Pastikan Anda telah mengunduh dan mengisi Template yang tersedia sebelum melakukan
                    impor!
                </p>
                <div id="csvDropZone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-[#1C4D8D] hover:bg-blue-50/50 transition-all duration-200">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 font-medium">Drag & drop file di sini</p>
                    <p class="text-xs text-gray-400 mt-1">atau klik untuk memilih file</p>
                    <input type="file" id="csvFileInput" accept=".csv,.xlsx,.xls" class="hidden">
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded">.csv</code>
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded">.xlsx</code> <code
                            class="bg-gray-100 px-1.5 py-0.5 rounded">.xls</code>
                    </p>
                    <button type="button" onclick="downloadCsvTemplate()"
                        class="text-xs text-[#1C4D8D] hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Download Template
                    </button>
                </div>
            </div>

            <div id="csvPreviewArea" class="hidden flex flex-col flex-1 overflow-hidden">

                <div class="px-4 pb-2 pt-2 flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-file-lines text-gray-500"></i>
                            <span id="csvTotalCount" class="font-semibold">0</span> total
                        </span>
                        <span class="flex items-center gap-1 text-green-600">
                            <i class="fa-solid fa-circle-check"></i>
                            <span id="csvValidCount" class="font-semibold">0</span> tersedia
                        </span>
                        <span class="flex items-center gap-1 text-red-500">
                            <i class="fa-solid fa-circle-xmark text-nowrap"></i>
                            <span id="csvDupCount" class="font-semibold">0</span> duplikat
                        </span>
                        <span class="flex items-center gap-1 text-yellow-600">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="csvInvCount" class="font-semibold">0</span> invalid
                        </span>
                    </div>
                    <button type="button" onclick="resetCsvImport()"
                        class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>

                <!-- Preview Table -->
                <div class="flex-1 overflow-auto px-4 max-h-[40vh]">
                    <table class="min-w-full text-xs border border-gray-300">
                        <thead class="sticky top-0 bg-[#0F2854] text-white z-10">
                            <tr>
                                <th class="px-3 py-2 text-center border border-gray-300 w-10">No</th>
                                <th class="px-3 py-2 text-left border border-gray-300">No Registrasi</th>
                                <th class="px-3 py-2 text-left border border-gray-300">Nama Perangkat</th>
                                <th class="px-3 py-2 text-center border border-gray-300 w-32">Status</th>
                            </tr>
                        </thead>
                        <tbody id="csvPreviewBody"></tbody>
                    </table>
                </div>

                <div class="p-4 flex justify-end gap-2 border-t border-gray-100 mt-2">
                    <button type="button" onclick="closeModal('tambahModal')"
                        class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                    <button type="button" id="btn_import_csv" onclick="submitCsvImport()" disabled
                        class="bg-[#1C4D8D] text-white text-sm px-4 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i class="fa-solid fa-file-import"></i>
                        <span>Import Data</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>