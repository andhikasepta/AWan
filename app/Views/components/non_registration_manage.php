<div id="nonRegManageModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] max-h-[85vh] overflow-hidden flex flex-col transition-all duration-300"
        id="nonRegManageContent" style="max-width:500px">

        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
            <h3 class="font-bold">Tambah Material</h3>
            <button onclick="closeNonRegManage()" class="text-white hover:text-gray-300 transition">
                <i class="fa-solid fa-xmark fa-lg"></i>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button type="button" onclick="switchNonRegTab('manual')" id="nonRegTabManual"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-[#1C4D8D] text-[#1C4D8D]">
                <i class="fa-solid fa-keyboard mr-1"></i> Input Manual
            </button>
            <button type="button" onclick="switchNonRegTab('csv')" id="nonRegTabCsv"
                class="flex-1 px-4 py-2.5 text-xs font-semibold text-center transition-all duration-200 border-b-2 border-transparent text-gray-400 hover:text-[#1C4D8D]">
                <i class="fa-solid fa-file-excel mr-1"></i> Import File
            </button>
        </div>

        <div id="nonRegContentManual">
            <form id="formNonReg" class="p-4 flex flex-col justify-between">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="nonRegId">

                <div class="grid grid-cols-1 md:grid-cols-[3fr_1fr] gap-6 mb-5">
                    <div class="w-full flex flex-col relative">
                        <label class="font-semibold text-[#1C4D8D] text-sm">Kode Spec</label>
                        <select id="nonRegKodeSpec" name="kode_spec" placeholder="Masukkan kode spec"></select>

                        <div id="nonRegNamaWrapper">
                            <input type="text" id="nonRegNama" name="nama_material" placeholder="Masukkan nama material"
                                class="w-full border mt-2 rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="font-semibold text-[#1C4D8D] text-sm">Quantity</label>
                        <input type="number" id="nonRegQty" name="quantity" placeholder="Qty" min="1"
                            class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="closeNonRegManage()"
                        class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
                    <button type="submit" id="btn_submit_nonreg"
                        class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">Simpan</button>
                </div>
            </form>
        </div>

        <div id="nonRegContentCsv" class="hidden h-full flex-col">
            <div id="nonRegCsvDropZoneArea" class="p-4 flex-1 overflow-auto">
                <p class="text-xs text-blue-800 mb-2">
                    <i class="fa-solid fa-circle-info mr-1 text-xs"></i>
                    Pastikan Anda mengunggah file sesuai format (Excel/CSV).
                </p>
                <div id="nonRegCsvDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-[#1C4D8D] hover:bg-blue-50/50 transition-all duration-200">
                    <div id="nonRegCsvDropZoneDefault">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 font-medium">Drag & drop file di sini</p>
                        <p class="text-xs text-gray-400 mt-1">atau klik untuk memilih file</p>
                    </div>
                    <form id="nonRegExcelForm" enctype="multipart/form-data">
                        <input type="file" id="nonRegExcelFile" name="file_excel" accept=".csv,.xlsx,.xls" class="hidden">
                    </form>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <p class="text-xs text-gray-400">Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded">.csv</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded">.xlsx</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded">.xls</code></p>
                </div>
            </div>

            <div class="p-4 flex justify-end gap-2 border-t border-gray-100 mt-2">
                <button type="button" onclick="closeNonRegManage()"
                    class="bg-[#D9D9D9] text-[#1C4D8D] text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#EFEFEF] transition">Batal</button>
            </div>
        </div>

    </div>
</div>

<script>
    let tsNonReg = null;

    function openNonRegManage() {
        document.getElementById('nonRegManageModal').classList.remove('hidden');
        document.getElementById('nonRegManageModal').classList.add('flex');
        resetNonRegForm();
        switchNonRegTab('manual');
        document.querySelector('#nonRegManageContent h3').innerText = 'Tambah Material';
        if (tsNonReg) {
            tsNonReg.enable();
        }
        document.getElementById('nonRegNama').readOnly = false;
    }

    function openNonRegEdit(id, kode_spec, nama_material, qty) {
        document.getElementById('nonRegManageModal').classList.remove('hidden');
        document.getElementById('nonRegManageModal').classList.add('flex');
        resetNonRegForm();
        switchNonRegTab('manual');
        document.querySelector('#nonRegManageContent h3').innerText = 'Edit Material';
        
        if (tsNonReg) {
            tsNonReg.addOption({
                id: id,
                text: kode_spec + " - " + nama_material,
                kode_spec: kode_spec,
                nama: nama_material
            });
            tsNonReg.setValue(id);
            tsNonReg.disable();
        }
        
        document.getElementById('nonRegId').value = id;
        document.getElementById('nonRegQty').value = qty;
        
        const namaInput = document.getElementById("nonRegNama");
        namaInput.value = nama_material;
        namaInput.readOnly = true;
        document.getElementById("nonRegNamaWrapper").classList.add("hidden");
    }

    function closeNonRegManage() {
        document.getElementById('nonRegManageModal').classList.add('hidden');
        document.getElementById('nonRegManageModal').classList.remove('flex');
    }

    function switchNonRegTab(tab) {
        const manualTab = document.getElementById('nonRegTabManual');
        const csvTab = document.getElementById('nonRegTabCsv');
        const manualContent = document.getElementById('nonRegContentManual');
        const csvContent = document.getElementById('nonRegContentCsv');

        if (tab === 'manual') {
            manualTab.classList.add('border-[#1C4D8D]', 'text-[#1C4D8D]');
            manualTab.classList.remove('border-transparent', 'text-gray-400', 'hover:text-[#1C4D8D]');
            csvTab.classList.remove('border-[#1C4D8D]', 'text-[#1C4D8D]');
            csvTab.classList.add('border-transparent', 'text-gray-400', 'hover:text-[#1C4D8D]');
            
            manualContent.classList.remove('hidden');
            csvContent.classList.add('hidden');
            csvContent.classList.remove('flex');
        } else {
            csvTab.classList.add('border-[#1C4D8D]', 'text-[#1C4D8D]');
            csvTab.classList.remove('border-transparent', 'text-gray-400', 'hover:text-[#1C4D8D]');
            manualTab.classList.remove('border-[#1C4D8D]', 'text-[#1C4D8D]');
            manualTab.classList.add('border-transparent', 'text-gray-400', 'hover:text-[#1C4D8D]');
            
            csvContent.classList.remove('hidden');
            csvContent.classList.add('flex');
            manualContent.classList.add('hidden');
        }
    }

    function resetNonRegForm() {
        document.getElementById('formNonReg').reset();
        document.getElementById('nonRegId').value = '';
        document.getElementById('nonRegNamaWrapper').classList.remove('hidden');
        
        if (tsNonReg) {
            tsNonReg.clear();
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const el = document.getElementById("nonRegKodeSpec");
        if (el) {
            tsNonReg = new TomSelect(el, {
                valueField: "id",
                labelField: "text",
                searchField: ["kode_spec", "nama_material"],
                create: true,
                controlClass: 'ts-control no-arrow',
                load: function (query, callback) {
                    if (!query.length) return callback();
                    fetch(`/dashboard/nonreg/getNonReg?search=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            callback(data.map(item => ({
                                id: item.id,
                                text: item.kode_spec + " - " + item.nama_material,
                                kode_spec: item.kode_spec,
                                nama: item.nama_material
                            })));
                        }).catch(() => callback());
                },
                onChange: function (value) {
                    const namaInput = document.getElementById("nonRegNama");
                    const idInput = document.getElementById("nonRegId");
                    const namaWrapper = document.getElementById("nonRegNamaWrapper");
                    
                    if (!value) {
                        idInput.value = "";
                        namaInput.value = "";
                        namaWrapper.classList.remove("hidden");
                        return;
                    }

                    const option = this.options[value];

                    if (option && option.nama) {
                        // Existing
                        idInput.value = option.id;
                        namaInput.value = option.nama;
                        namaWrapper.classList.add("hidden");
                    } else {
                        // New
                        idInput.value = "";
                        namaInput.value = "";
                        namaWrapper.classList.remove("hidden");
                    }
                }
            });
        }

        // Setup Drag & Drop
        const dropZone = document.getElementById('nonRegCsvDropZone');
        const fileInput = document.getElementById('nonRegExcelFile');

        if (dropZone && fileInput) {
            dropZone.addEventListener('click', () => fileInput.click());

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-[#1C4D8D]', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-[#1C4D8D]', 'bg-blue-50');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    submitNonRegExcel(); // auto submit upon drop
                }
            });

            fileInput.addEventListener('change', () => {
                if(fileInput.files.length) {
                    submitNonRegExcel(); // auto submit upon selection
                }
            });
        }
    });

    document.getElementById('formNonReg').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn_submit_nonreg');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';
        
        const formData = new FormData(this);
        
        if (tsNonReg) {
            const selectedVal = tsNonReg.getValue();
            if (!selectedVal) {
                Swal.fire('Error', 'Kode Spec belum diisi!', 'warning');
                btn.disabled = false;
                btn.innerHTML = 'Simpan';
                return;
            }

            const option = tsNonReg.options[selectedVal];
            if (option && option.kode_spec) {
                formData.set('kode_spec', option.kode_spec);
            } else {
                formData.set('kode_spec', selectedVal);
            }
        }
        
        fetch('<?= base_url('dashboard/saveNonReg') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.msg,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.msg, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Simpan';
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Simpan';
        });
    });

    function submitNonRegExcel() {
        const fileInput = document.getElementById('nonRegExcelFile');
        if (!fileInput.files.length) return;

        const formData = new FormData(document.getElementById('nonRegExcelForm'));
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        Swal.fire({
            title: 'Uploading...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('<?= base_url('dashboard/uploadNonRegExcel') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message || 'Upload berhasil', 'success').then(() => window.location.reload());
            } else {
                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat upload', 'error');
            }
            fileInput.value = '';
        })
        .catch(err => {
            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            fileInput.value = '';
        });
    }

    function confirmNonRegDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data material akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('dashboard/deleteNonReg') ?>/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire('Terhapus!', data.msg, 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
            }
        });
    }
</script>
