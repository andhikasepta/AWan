<div id="editModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Edit Perangkat</h3>
        </div>

        <form id="editMutasi" class="p-4 flex flex-col justify-between">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="font-medium text-[#1C4D8D] text-sm">No Registrasi</label>
                <input type="text" id="edit_noreg"
                    class="w-full bg-[#D0E4FE] text-sm text-[#1C4D8D] font-semibold border rounded-md p-2 focus:outline-none"
                    disabled>
            </div>

            <div>
                <label class="font-medium text-[#1C4D8D] text-sm">Nama Perangkat</label>
                <input type="text" id="edit_np"
                    class="w-full bg-[#D0E4FE] text-sm text-[#1C4D8D] font-semibold border rounded-md p-2 focus:outline-none"
                    disabled>
            </div>

            <hr class="my-4">

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#1C4D8D]">Nama User</label>
                    <select id="edit_user" name="id_users" required>
                        <option value="">Pilih User</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>">
                                <?= $u['nama'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1C4D8D]">Status</label>
                    <select id="edit_status" name="status_mutasi"
                        required>
                        <option value="">Pilih Status</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>">
                                <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-medium text-[#1C4D8D] text-sm mb-2">Keterangan</label>
                <textarea id="edit_ket" name="keterangan" rows="3" placeholder="Masukkan keterangan"
                    class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="closeModal('editModal')"
                    class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                <button type="submit" id="btn_submit_edit"
                    class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">Simpan</button>
            </div>

        </form>
    </div>
</div>