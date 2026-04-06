<div id="tambahModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white border border-gray-200 rounded-md shadow-md w-full max-w-md p-6 relative">

        <button onclick="closeModal('tambahModal')" class="absolute right-3 top-3 text-black text-lg font-bold focus:outline-none">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h2 class="text-lg text-[#1C4D8D] font-bold">Tambah Perangkat</h2>
        
        <form id="tambahperangkat" class="space-y-4">
            <input type="hidden" name="id" id="tambah_id">

            <div>
                <label class="font-medium text-[#1C4D8D] text-sm">No Registrasi</label>
                <input type="text" id="tambah_noreg" name="noreg" placeholder="Mau buat apa?" class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
            </div>
            
            <div>
                <label class="font-medium text-[#1C4D8D] text-sm mb-2">Nama Perangkat</label>
                <input type="text" id="tambah_np" name="nama" placeholder="Mau buat apa?" class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="closeModal('tambahModal')" class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                <button type="submit" class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#7FB3D5] transition">Simpan</button>
            </div>

        </form>
    </div>
</div>