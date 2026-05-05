<div id="tambahModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Tambah Perangkat</h3>
        </div>
        
        <form id="tambahperangkat" class="p-4 flex flex-col justify-between">
            <input type="hidden" name="id" id="tambah_id">

            <div class="grid grid-cols-1 md:grid-cols-[3fr_1fr] gap-6 mb-5">

                <div class="w-full flex flex-col relative">
                    <label class="font-semibold text-[#1C4D8D] text-sm">Kode Spec</label>
                    <select id="kode_spec" name="id_spec" placeholder="Masukkan kode spec"></select>

                    <div id="namaWrapper">
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama perangkat" class="w-full border mt-2 rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                    </div>
                </div>
            
                <div class="flex flex-col">
                    <label class="font-semibold text-[#1C4D8D] text-sm">Kode ID</label>
                    <input type="text" id="kode_id" name="kode_id" placeholder="Kode ID" class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                </div>

            </div>

            <small id ="noregWarning" class="text-red-500 hidden">
                Nomor Registrasi sudah terdaftar!
            </small>

            <small id="noregOK" class="text-green-500 hidden">
                Nomor Registrasi tersedia
            </small>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="closeModal('tambahModal')" class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                <button type="submit" id="btn_submit_tambah" class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">Simpan</button>
            </div>

        </form>
    </div>
</div>