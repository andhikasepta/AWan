<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<?php $hideOverlay = session()->getFlashdata('success') || session()->getFlashdata('error') || session()->get('mutasi_pdf_ids'); ?>
<div id="homepageOverlay"
  class="fixed inset-0 z-[100] flex flex-col items-center justify-center text-center bg-cover bg-center bg-no-repeat transition-all duration-[800ms] ease-in-out <?= $hideOverlay ? 'hidden' : '' ?>"
  style="background-image: url('<?= base_url('images/Bg.webp') ?>'); width: 100vw; height: 100vh; background-color: #1C4D8D; <?= $hideOverlay ? 'display: none;' : '' ?>">

  <div class="absolute inset-0 bg-black/70 z-0"
    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 0;">
  </div>

  <div class="relative z-10 flex flex-grow flex-col items-center justify-center px-4 w-full">
    <img src="<?= base_url('images/awan.webp') ?>" alt="AWan Logo" class="w-64 mb-3 mt-64 drop-shadow-lg"
      onerror="this.style.display='none'">

    <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl mb-6 shadow-lg border border-white/20">
      <i class="text-gray-100 text-[16px] font-medium">Tertib Administrasi, Mutasi Terkendali.</i>
    </div>

    <p class="text-white mb-14 mt-20 mx-auto drop-shadow-md text-[21px] max-w-xl leading-relaxed font-medium">
    </p>

    <button type="button" onclick="closeHomepageOverlay()"
      class="bg-[#1C4D8D] hover:bg-[#3E679E] text-white font-bold py-2.5 px-6 rounded-full shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-lg border border-white/20">
      Lanjutkan
    </button>
  </div>

  <footer class="relative z-10 text-xs text-white/50 pb-6 w-full text-center">
    Unreleased &bull; PT. Aplikanusa Lintasarta &copy; <?= date('Y') ?>
  </footer>
</div>

<div id="toast" class="fixed top-20 right-5 z-[70] hidden transform transition-all duration-300 translate-x-full">
  <div id="toastBox" class="flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-white text-sm">
    <i id="toastIcon" class="fa-solid"></i>
    <span id="toastMsg"></span>
  </div>
</div>

<div class="w-full max-w-[1450px] mx-auto rounded-lg overflow-hidden shadow-lg flex flex-col md:flex-row">
  <div class="w-full md:w-1/5 bg-[#3E679E] p-6 md:p-8 text-white flex flex-col justify-between md:min-h-[600px]">
    <div>
      <p class="text-xs mb-6 leading-relaxed">
        Welcome, Laskar Lintasarta <br>
        Central Java & D.I.Y Operation
      </p>

      <div class="bg-[#1C4D8D] p-5 rounded-xl shadow-inner">
        <h2 class="font-bold text-lg mb-2">PENTING!!!</h2>
        <p class="text-xs leading relaxed mb-4 text-white">
          Mohon untuk pengambilan dan peminjaman perangkat dapat mengisi form yang sudah disediakan atau mengirimkan
          foto registrasi perangkat dengan <b>JELAS</b>
        </p>
        <p class="text-[10px] italic text-white">
          Note: <br>
          Pengambilan dan mutasi perangkat <span class="font-bold">WAJIB</span> menginformasikan administrator
        </p>
      </div>

      <p class="text-[11px] font-bold mt-5 mb-1">BUTUH BANTUAN?</p>
      <p class="text-[11px] mb-4">Silakan hubungi admin melalui whatsapp di bawah ini</p>
      <a href="https://wa.me/6282133601435?text=Admin,%20terkendala%20No.Registrasi%20perangkat%20tidak%20terdaftar,%20ingin%20menambahkan%20user%20atau%20ingin%20mendaftarkan%20akun"
        target="_blank"
        class="bg-white text-[#1C4D8D] font-bold px-2 py-3 rounded-lg w-full text-sm shadow hover:bg-gray-300 transition text-center flex items-center justify-center gap-2">
        Hubungi Admin
      </a>
    </div>

    <div class="mt-8">
    </div>
  </div>

  <div class="w-full md:w-4/5 bg-white p-6 md:p-10 min-h-[600px]">
    <!-- TABS -->
    <div class="flex border-b border-gray-200 mb-8">
      <button type="button" onclick="switchTab('request')" id="tab_request" class="w-1/3 py-3 font-extrabold text-[#1C4D8D] text-center border-b-4 border-[#1C4D8D] border-t-0 border-l-0 border-r-0 outline-none focus:outline-none focus:ring-0 transition-all text-[10px] md:text-sm">
        PEMINJAMAN
      </button>
      <button type="button" onclick="switchTab('return')" id="tab_return" class="w-1/3 py-3 font-extrabold text-gray-400 text-center border-b-4 border-transparent hover:text-[#1C4D8D] border-t-0 border-l-0 border-r-0 outline-none focus:outline-none focus:ring-0 transition-all text-[10px] md:text-sm">
        PENGEMBALIAN
      </button>
      <button type="button" onclick="switchTab('install')" id="tab_install" class="w-1/3 py-3 font-extrabold text-gray-400 text-center border-b-4 border-transparent hover:text-[#1C4D8D] border-t-0 border-l-0 border-r-0 outline-none focus:outline-none focus:ring-0 transition-all text-[10px] md:text-sm">
        PEMASANGAN
      </button>
    </div>

    <!-- SLIDER CONTAINER -->
    <div class="overflow-x-hidden overflow-y-auto w-full relative max-h-[470px] pr-2">
      <div id="slider_container" class="flex transition-transform duration-500 ease-in-out w-[300%]">
        
        <!-- TAB 1: PEMINJAMAN -->
        <div class="w-1/3 flex-shrink-0 pr-4 pl-1">
          <form action="<?= base_url('/submit') ?>" method="POST">
            <?= csrf_field() ?>
      <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-6 mb-5">

        <div class="w-full flex flex-col relative">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">Material Registrasi</label>
          <input type="text" id="noreg_input" placeholder="Scan barcode atau ketik no registrasi"
            class="text-xs w-full rounded-md p-2 min-h-[42px] border border-gray-300 focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D]">

          <div id="status_scan" class="text-[10px] mt-1 hidden"></div>
        </div>

        <div class="w-full flex flex-col justify-end">
          <label class="invisible text-sm mb-2">Hidden</label>
          <button type="button" id="btn_tambah"
            class="bg-[#1C4D8D] h-[42px] px-4 py-1 text-xs text-white rounded-md font-semibold shadow hover:bg-[#7AAACE] transition items-end flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
              <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
              <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
              <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
              <line x1="2" y1="12" x2="22" y2="12"></line>
            </svg> Scan Barcode
          </button>
        </div>
      </div>

      <!-- Non-Registration Input -->
      <div class="grid grid-cols-1 md:grid-cols-[1fr_100px_auto] gap-4 mb-5 border-t border-gray-200 pt-4 relative z-20">
        <div class="w-full flex flex-col relative z-20">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">Material Non-Registrasi</label>
          <select id="nonreg_select" class="">
            <option value="">Pilih Material...</option>
          </select>
        </div>
        <div class="w-full flex flex-col relative">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">Quantity</label>
          <input type="number" id="nonreg_qty" min="1" value="" placeholder="-"
            class="text-xs w-full rounded-md p-2 min-h-[42px] border border-gray-300 focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D]">
        </div>
        <div class="w-full flex flex-col justify-end">
          <label class="invisible text-sm mb-2">Hidden</label>
          <button type="button" id="btn_tambah_nonreg" onclick="addNonRegToCart()"
            class="bg-[#1C4D8D] h-[42px] px-4 py-1 text-xs text-white rounded-md font-semibold shadow hover:bg-[#7AAACE] transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah
          </button>
        </div>
      </div>

      <div class="mt-4 mb-6">
        <h3 class="font-semibold text-sm mb-3 text-[#1C4D8D]">Daftar Perangkat</h3>
        <div class="overflow-y-auto rounded-lg border border-gray-200 shadow-sm max-h-[245px]">
          <table class="w-full text-xs text-left">
            <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10 shadow-sm">
              <tr>
                <th class="px-2 py-2 font-semibold text-gray-600 text-center w-12">No</th>
                <th class="px-2 py-2 font-semibold text-gray-600">Registrasi / Kode Spec</th>
                <th class="px-2 py-2 font-semibold text-gray-600">Nama Perangkat / Material</th>
                <th class="px-2 py-2 font-semibold text-gray-600 text-center w-20">Qty</th>
                <th class="px-2 py-2 font-semibold text-gray-600 text-center w-20">Action</th>
              </tr>
            </thead>
            <tbody id="list_perangkat" class="divide-y divide-gray-100 bg-white">
              <tr>
                <td colspan="5" class="px-4 py-2 text-center text-gray-400 italic">
                  Belum ada perangkat atau material yang ditambahkan
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="flex flex-col mb-4">
        <label class="font-semibold text-[#1C4D8D] text-sm mb-2">User</label>
        <select id="user" name="user" class="" required>
          <style>
            .ts-control {
              border-radius: 0.375rem;
              padding: 0.7rem;
              border: 1px solid #d1d5db;
            }

            .ts-control:focus {
              border-color: #1C4D8D;
              box-shadow: 0 0 0 1px #1C4D8D;
            }

            #reader {
              border: none !important;
              position: relative;
            }

            #reader video {
              border-radius: 8px;
              object-fit: cover !important;
            }

            #reader__scan_region {
              border-radius: 8px;
            }
          </style>
          <option value="">Pilih user</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" data-nama="<?= $u['nama'] ?>"> <?= $u['nama'] ?>
            <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-8">
        <div class="flex flex-col">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">Keterangan</label>
          <div class="relative">
            <textarea name="keterangan" rows="2" placeholder="Masukkan keterangan"
              class="border rounded-md p-3 pr-10 text-xs w-full focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D] resize-none"></textarea>
          </div>
        </div>
      </div>

      <div class="flex">
        <button type="submit" id="btn_submit"
          class="bg-[#1C4D8D] text-sm text-white px-8 py-2 rounded-md font-semibold shadow hover:bg-[#7AAACE] transition">
          Submit
        </button>
      </div>
          </form>
        </div>

        <!-- TAB 2: PENGEMBALIAN -->
        <div class="w-1/3 flex-shrink-0 px-4 flex flex-col">
          <div class="flex flex-col mb-4 relative z-30">
            <div class="flex items-center gap-2 mb-2">
              <label class="font-semibold text-[#1C4D8D] text-sm">Pilih User</label>
              <a href="javascript:void(0)" onclick="resetReturnForm()" class="text-gray-500 hover:text-[#1C4D8D] hover:border-[#1C4D8D] transition text-[11px] flex items-center gap-1 border border-gray-300 rounded-full px-2 py-0.5" title="Reset">
                <i class="fa-solid fa-rotate-right text-[10px]"></i> Reset
              </a>
            </div>
            <select id="return_user" class="text-sm w-full">
              <option value="">Pilih User...</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['nama'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mt-4 mb-2 transition-all duration-500 ease-in-out">
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-sm text-[#1C4D8D]">Perangkat yang dibawa (<span id="total_dibawa_count">0</span>)</h3>
              <input type="text" id="search_return_devices" placeholder="Cari perangkat..." class="border border-gray-300 rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D] hidden">
            </div>
            <div class="overflow-auto rounded-lg border border-gray-200 shadow-sm max-h-[40vh]">
              <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                  <tr>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center w-12">
                      <input type="checkbox" id="selectAllReturn" class="w-3 h-3 cursor-pointer accent-[#1C4D8D]">
                    </th>
                    <th class="px-2 py-2 font-semibold text-gray-600">No Registrasi</th>
                    <th class="px-2 py-2 font-semibold text-gray-600">Nama Perangkat</th>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center w-16">Qty</th>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="return_devices_list" class="divide-y divide-gray-100 bg-white">
                  <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-400 italic">
                      Pilih user untuk melihat perangkat.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-200 flex justify-between items-center mt-4">
            <div class="text-xs text-gray-500 font-medium">
              Terpilih: <span id="selected_dibawa_count" class="font-bold text-[#1C4D8D]">0</span> perangkat
            </div>
            <button type="button" onclick="submitReturnRequest()" class="bg-[#1C4D8D] px-6 py-2 text-sm text-white rounded-md shadow hover:bg-[#7AAACE] transition disabled:opacity-50" id="btn_submit_return" disabled>
              Submit Request
            </button>
          </div>
        </div>

        <!-- TAB 3: PEMASANGAN -->
        <div class="w-1/3 flex-shrink-0 pl-4 pr-1 flex flex-col">
          <div class="flex flex-col mb-4 relative z-30">
            <div class="flex items-center gap-2 mb-2">
              <label class="font-semibold text-[#1C4D8D] text-sm">Pilih User</label>
              <a href="javascript:void(0)" onclick="resetInstallForm()" class="text-gray-500 hover:text-[#1C4D8D] hover:border-[#1C4D8D] transition text-[11px] flex items-center gap-1 border border-gray-300 rounded-full px-2 py-0.5" title="Reset">
                <i class="fa-solid fa-rotate-right text-[10px]"></i> Reset
              </a>
            </div>
            <select id="install_user" class="text-sm w-full">
              <option value="">Pilih User...</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>"><?= $u['nama'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mt-2 mb-2 transition-all duration-500 ease-in-out">
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-sm text-[#1C4D8D]">Perangkat yang dibawa (<span id="total_install_count">0</span>)</h3>
              <input type="text" id="search_install_devices" placeholder="Cari perangkat..." class="border border-gray-300 rounded-md px-3 py-1 text-xs focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D] hidden">
            </div>
            <div class="overflow-auto rounded-lg border border-gray-200 shadow-sm max-h-[25vh]">
              <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                  <tr>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center w-12">
                      <input type="checkbox" id="selectAllInstall" class="w-3 h-3 cursor-pointer accent-[#1C4D8D]">
                    </th>
                    <th class="px-2 py-2 font-semibold text-gray-600">No Registrasi</th>
                    <th class="px-2 py-2 font-semibold text-gray-600">Nama Perangkat</th>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center w-16">Qty</th>
                    <th class="px-2 py-2 font-semibold text-gray-600 text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="install_devices_list" class="divide-y divide-gray-100 bg-white">
                  <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-400 italic">
                      Pilih user untuk melihat perangkat.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Arep, Site & Node Sentral Dropdowns -->
          <div class="grid grid-cols-3 gap-3 mt-3 mb-3 relative z-20">
            <div class="flex flex-col">
              <label class="font-semibold text-[#1C4D8D] text-xs mb-1">Arep</label>
              <select id="install_arep" class="">
                <option value="">Pilih Arep</option>
              </select>
            </div>
            <div class="flex flex-col">
              <label class="font-semibold text-[#1C4D8D] text-xs mb-1">Site Sentral</label>
              <select id="install_site" class="" disabled>
                <option value="">Pilih Site</option>
              </select>
            </div>
            <div class="flex flex-col">
              <label class="font-semibold text-[#1C4D8D] text-xs mb-1">Node Sentral</label>
              <select id="install_node" class="" disabled>
                <option value="">Pilih Node</option>
              </select>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-200 flex justify-between items-center mt-2">
            <div class="text-xs text-gray-500 font-medium">
              Terpilih: <span id="selected_install_count" class="font-bold text-[#1C4D8D]">0</span> perangkat
            </div>
            <button type="button" onclick="submitInstallRequest()" class="bg-[#1C4D8D] px-6 py-2 text-sm text-white rounded-md shadow hover:bg-[#7AAACE] transition disabled:opacity-50" id="btn_submit_install" disabled>
              Submit Request
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

<!-- Modal Scanner -->
<div id="scannerModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">
    <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
      <h3 class="font-bold">Scan Barcode / QR Code</h3>
      <div class="flex items-center gap-3">
        <button type="button" id="switchCamera" title="Switch Camera" class="text-white hover:text-gray-300 transition">
          <i class="fa-solid fa-camera-rotate text-lg"></i>
        </button>
        <button type="button" id="closeScanner" class="text-white hover:text-gray-400 transition">
          <i class="fa-solid fa-xmark fa-xl"></i>
        </button>
      </div>
    </div>
    <div class="p-4 flex flex-col items-center">
      <div id="reader" class="w-full"></div>
    </div>
  </div>
</div>

</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
  function showToast(message, type = "error") {
    const toast = document.getElementById("toast");
    const box = document.getElementById("toastBox");
    const msg = document.getElementById("toastMsg");
    const icon = document.getElementById("toastIcon");

    if (!toast || !box || !msg || !icon) return;

    msg.innerText = message;

    box.className = "flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-white text-sm";

    if (type === "error") {
      box.classList.add("bg-red-500");
      icon.className = "fa-solid fa-circle-xmark";
    } else if (type === "success") {
      box.classList.add("bg-green-500");
      icon.className = "fa-solid fa-circle-check";
    } else {
      box.classList.add("bg-yellow-500");
      icon.className = "fa-solid fa-triangle-exclamation";
    }

    toast.classList.remove("hidden", "translate-x-full");
    toast.classList.add("translate-x-0");

    setTimeout(() => {
      toast.classList.remove("translate-x-0");
      toast.classList.add("translate-x-full");

      setTimeout(() => {
        toast.classList.add("hidden");
      }, 300);
    }, 3000);
  }

  function closeHomepageOverlay() {
    const overlay = document.getElementById('homepageOverlay');
    if (!overlay) return;
    overlay.style.transition = 'transform 800ms ease-in-out, opacity 800ms ease-in-out';
    overlay.style.transform = 'translateY(-100%)';
    overlay.style.opacity = '0';

    setTimeout(function() {
      overlay.style.display = 'none';
    }, 800);
  }

  document.addEventListener("DOMContentLoaded", function () {
    <?php if (session()->getFlashData('success')): ?>
      showToast("<?= session()->getFlashdata('success') ?>", "success");

      <?php if (session()->getFlashData('brp_ready')): ?>
        // Show BRP Modal
        const brpModal = document.getElementById('brpDownloadModal');
        if (brpModal) {
            brpModal.classList.remove('hidden');
            brpModal.classList.add('flex');
            
            // Auto download
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = "<?= base_url('submit/pdf') ?>";
                link.setAttribute('download', '');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 500);
        }
      <?php endif; ?>
    <?php endif; ?>

    const inputScan = document.getElementById('noreg_input');
    const nonRegSelect = document.getElementById('nonreg_select');

    let cart = [];
    let cartNonReg = [];
    let allNonRegs = [];

    let tsNonReg = null;

    // Load Non-Registration Materials
    fetch('<?= base_url('form/nonRegList') ?>', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.json())
      .then(data => {
        allNonRegs = data;
        if(data && data.length) {
          data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = `${item.kode_spec || '-'} - ${item.nama_material} (Stock: ${item.quantity})`;
            nonRegSelect.appendChild(opt);
          });

          tsNonReg = new TomSelect("#nonreg_select", {
            create: false,
          });

          tsNonReg.on('change', function(value) {
            if (value) {
              const selectedItem = allNonRegs.find(item => item.id == value);
              const stock = selectedItem ? parseInt(selectedItem.quantity) : 0;
              if (stock === 0) {
                document.getElementById('nonreg_qty').disabled = true;
                document.getElementById('btn_tambah_nonreg').disabled = true;
                document.getElementById('btn_tambah_nonreg').classList.add('opacity-50', 'cursor-not-allowed');
              } else {
                document.getElementById('nonreg_qty').disabled = false;
                document.getElementById('btn_tambah_nonreg').disabled = false;
                document.getElementById('btn_tambah_nonreg').classList.remove('opacity-50', 'cursor-not-allowed');
              }
            } else {
              document.getElementById('nonreg_qty').disabled = false;
              document.getElementById('btn_tambah_nonreg').disabled = false;
              document.getElementById('btn_tambah_nonreg').classList.remove('opacity-50', 'cursor-not-allowed');
            }
          });
        }
      });
      
    window.addNonRegToCart = function() {
      const selectedId = tsNonReg ? tsNonReg.getValue() : nonRegSelect.value;
      const qty = parseInt(document.getElementById('nonreg_qty').value);

      if(!selectedId) {
        showToast("Pilih material terlebih dahulu", "warning");
        return;
      }
      if(!qty || qty < 1) {
        showToast("Quantity minimal 1", "warning");
        return;
      }

      const selectedItem = allNonRegs.find(item => item.id == selectedId);
      const stock = selectedItem ? parseInt(selectedItem.quantity) : 0;

      if(qty > stock) {
        showToast(`Stock tidak cukup! Tersisa ${stock}`, "error");
        return;
      }

      // Check if already in cart
      const existing = cartNonReg.find(item => item.id == selectedId);
      if(existing) {
        if(existing.qty + qty > stock) {
          showToast(`Total quantity melebihi stock! Tersisa ${stock}`, "error");
          return;
        }
        existing.qty += qty;
      } else {
        cartNonReg.push({
          id: selectedId,
          kode_spec: selectedItem.kode_spec || '-',
          nama: selectedItem.nama_material,
          qty: qty
        });
      }

      showToast("Material ditambahkan ke daftar!", "success");
      if (tsNonReg) tsNonReg.clear();
      document.getElementById('nonreg_qty').value = "";
      document.getElementById('nonreg_qty').disabled = false;
      document.getElementById('btn_tambah_nonreg').disabled = false;
      document.getElementById('btn_tambah_nonreg').classList.remove('opacity-50', 'cursor-not-allowed');
      renderTable();
    };

    function multiAdd(noregInput) {
      const noreg = noregInput.trim();

      if (!noreg) {
        showToast("Masukkan No Registrasi terlebih dahulu", "warning");
        return;
      }

      fetch(`<?= base_url('form/cek-noreg') ?>?noreg=${encodeURIComponent(noreg)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(res => {
          if (!res.exists) {
            showToast(res.message || "No registrasi tidak tersedia", res.toast_type || "error");
            return;
          }

          const hasil = res.data;

          if (cart.some(item => item.noreg.toLowerCase() === hasil.noreg.toLowerCase())) {
            showToast("Perangkat sudah ditambahkan!", "warning");
            return;
          }

          cart.push({
            id: hasil.id,
            noreg: hasil.noreg,
            nama: hasil.nama
          });

          renderTable();
          inputScan.value = "";
        })
        .catch(err => {
          showToast("Gagal memeriksa perangkat", "error");
        });
    }

    inputScan.addEventListener('keydown', function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        multiAdd(this.value);
      }
    });

    let html5QrCode;
    let isScanning = false;
    let currentFacingMode = 'environment';

    async function stopScanner() {
      if (html5QrCode && isScanning) {
        try {
          await html5QrCode.stop();
          isScanning = false;
          const guide = document.getElementById('scanner-guide');
          if (guide) guide.remove();
          document.getElementById('scannerModal').classList.add('hidden');
        } catch (err) {
          console.error("Failed to stop scanner", err);
          document.getElementById('scannerModal').classList.add('hidden');
        }
      } else {
        document.getElementById('scannerModal').classList.add('hidden');
      }
    }

    function getFormatsToSupport() {
      return [
        Html5QrcodeSupportedFormats.QR_CODE,
        Html5QrcodeSupportedFormats.AZTEC,
        Html5QrcodeSupportedFormats.CODABAR,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.CODE_93,
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.DATA_MATRIX,
        Html5QrcodeSupportedFormats.MAXICODE,
        Html5QrcodeSupportedFormats.ITF,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.PDF_417,
        Html5QrcodeSupportedFormats.RSS_14,
        Html5QrcodeSupportedFormats.RSS_EXPANDED,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
        Html5QrcodeSupportedFormats.UPC_EAN_EXTENSION,
      ];
    }

    function getScanConfig() {
      return {
        fps: 25,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
          const width = Math.floor(viewfinderWidth * 0.8);
          const height = Math.floor(viewfinderHeight * 0.6);
          return { width, height };
        },
        aspectRatio: 1.0,
        experimentalFeatures: {
          useBarCodeDetectorIfSupported: true
        }
      };
    }

    async function startScanner(facingMode) {
      currentFacingMode = facingMode || 'environment';

      if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader", { formatsToSupport: getFormatsToSupport() });
      }

      const config = getScanConfig();

      // if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
      //   showToast("Error: Kamera membutuhkan koneksi HTTPS (Secure Context)", "error");
      //   scannerModal.classList.add('hidden');
      //   return;
      // }

      try {
        await html5QrCode.start(
          { facingMode: currentFacingMode },
          config,
          onScanSuccess,
          onScanFailure
        );
        isScanning = true;

        const reader = document.getElementById('reader');
        if (!document.getElementById('scanner-guide')) {
          const guide = document.createElement('div');
          guide.id = 'scanner-guide';
          guide.style.position = 'absolute';
          guide.style.top = '50%';
          guide.style.left = '50%';
          guide.style.transform = 'translate(-50%, -50%)';
          guide.style.border = '2px solid #1C4D8D';
          guide.style.borderRadius = '8px';
          guide.style.zIndex = '10';
          guide.style.pointerEvents = 'none';

          const rect = reader.getBoundingClientRect();
          const width = Math.floor(rect.width * 0.8);
          const height = Math.floor(rect.height * 0.6);
          guide.style.width = width + 'px';
          guide.style.height = height + 'px';

          const line = document.createElement('div');
          line.style.position = 'absolute';
          line.style.top = '0';
          line.style.left = '0';
          line.style.width = '100%';
          line.style.height = '2px';
          line.style.background = '#1C4D8D';
          line.style.boxShadow = '0 0 10px #1C4D8D';
          line.style.animation = 'scan-line-anim 2s linear infinite';

          if (!document.getElementById('scan-anim-style')) {
            const style = document.createElement('style');
            style.id = 'scan-anim-style';
            style.innerHTML = `@keyframes scan-line-anim { 0% { top: 0%; } 100% { top: 100%; } }`;
            document.head.appendChild(style);
          }

          guide.appendChild(line);
          reader.appendChild(guide);
        }
      } catch (err) {
        console.error("Unable to start scanning.", err);
        let errorMsg = "Gagal mengakses kamera!";
        if (err.name === 'NotAllowedError') errorMsg = "Permission kamera ditolak oleh user.";
        if (err.name === 'NotFoundError') errorMsg = "Kamera tidak ditemukan.";
        if (err.name === 'NotReadableError') errorMsg = "Kamera sedang digunakan aplikasi lain.";

        showToast(errorMsg, "error");
        scannerModal.classList.add('hidden');
      }
    }

    document.getElementById('btn_tambah').addEventListener('click', async function () {
      const scannerModal = document.getElementById('scannerModal');
      scannerModal.classList.remove('hidden');
      await startScanner('environment');
    });

    document.getElementById('switchCamera').addEventListener('click', async function () {
      if (!isScanning) return;
      try {
        await html5QrCode.stop();
        isScanning = false;
        const guide = document.getElementById('scanner-guide');
        if (guide) guide.remove();
      } catch (err) {
        console.error('Failed to stop before switch', err);
      }
      const newMode = currentFacingMode === 'environment' ? 'user' : 'environment';
      await startScanner(newMode);
    });

    function onScanSuccess(decodedText, decodedResult) {
      if (!isScanning) return;

      showToast("Barcode added!", "success");

      inputScan.value = decodedText;
      multiAdd(decodedText);

      stopScanner();
    }

    function onScanFailure(error) {
      // Keep scanning
    }

    document.getElementById('closeScanner').addEventListener('click', stopScanner);

    function renderTable() {
      const tbody = document.getElementById('list_perangkat');
      tbody.innerHTML = "";

      if (cart.length === 0 && cartNonReg.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
              Belum ada perangkat atau material yang ditambahkan
            </td>
          </tr>
        `;
        return;
      }

      let rowIdx = 1;

      // Render Devices
      cart.forEach((item, index) => {
        tbody.innerHTML += `
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-4 py-3 text-center text-gray-700">${rowIdx++}</td>
          <td class="px-4 py-3 text-gray-700 font-medium">${item.noreg}</td>
          <td class="px-4 py-3 text-gray-700">${item.nama}</td>
          <td class="px-4 py-3 text-center text-gray-700">1</td>
          <td class="px-4 py-3 text-center">
            <button type="button" onclick="hapusItem(${index}, 'perangkat')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-md transition-colors" title="Hapus">
              <i class="fa-solid fa-trash"></i>
            </button>
            <input type="hidden" name="perangkat[${index}][id]" value="${item.id}">
            <input type="hidden" name="perangkat[${index}][noreg]" value="${item.noreg}">
          </td>
        </tr>
        `;
      });

      // Render Non-Registration Materials
      cartNonReg.forEach((item, index) => {
        tbody.innerHTML += `
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-4 py-3 text-center text-gray-700">${rowIdx++}</td>
          <td class="px-4 py-3 text-gray-700 font-medium">${item.kode_spec}</td>
          <td class="px-4 py-3 text-gray-700">${item.nama} <span class="text-[10px] bg-blue-100 text-blue-800 px-1 py-0.5 rounded ml-1">Non-Reg</span></td>
          <td class="px-4 py-3 text-center text-gray-700 font-bold">${item.qty}</td>
          <td class="px-4 py-3 text-center">
            <button type="button" onclick="hapusItem(${index}, 'non_reg')" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-md transition-colors" title="Hapus">
              <i class="fa-solid fa-trash"></i>
            </button>
            <input type="hidden" name="non_reg[${index}][id]" value="${item.id}">
            <input type="hidden" name="non_reg[${index}][qty]" value="${item.qty}">
          </td>
        </tr>
        `;
      });
    }

    window.hapusItem = function (index, type = 'perangkat') {
      if(type === 'perangkat') {
        cart.splice(index, 1);
      } else {
        cartNonReg.splice(index, 1);
      }
      renderTable();
    }

    const form = document.querySelector("form");
    const btnSubmit = document.getElementById("btn_submit");

    form.addEventListener("submit", function (e) {
      if (cart.length === 0 && cartNonReg.length === 0) {
        e.preventDefault();
        showToast("Tambahkan minimal 1 perangkat atau material!", "warning");
        return;
      }

      btnSubmit.disabled = true;
    });

    new TomSelect("#user", {
      create: false,
    });
    
    // Return Request Logic
    let tsReturnUser = new TomSelect("#return_user", {
      create: false,
    });

    tsReturnUser.on('change', function(userId) {
      if (!userId) {
        document.getElementById('return_devices_list').innerHTML = `
          <tr>
            <td colspan="4" class="px-4 py-4 text-center text-gray-400 italic">
              Pilih user untuk melihat perangkat.
            </td>
          </tr>
        `;
        document.getElementById('btn_submit_return').disabled = true;
        document.getElementById('total_dibawa_count').innerText = '0';
        document.getElementById('selected_dibawa_count').innerText = '0';
        document.getElementById('search_return_devices').classList.add('hidden');
        document.getElementById('search_return_devices').value = '';
        return;
      }

      document.getElementById('return_devices_list').innerHTML = `
        <tr>
          <td colspan="4" class="px-4 py-4 text-center text-gray-400 italic">
            <i class="fa-solid fa-spinner fa-spin"></i> Memuat perangkat...
          </td>
        </tr>
      `;

      fetch(`<?= base_url('form/devices') ?>/${userId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('return_devices_list');
          tbody.innerHTML = '';
          
          if (data.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="5" class="px-4 py-4 text-center text-gray-400 italic">
                  Tidak ada perangkat yang dibawa oleh user ini.
                </td>
              </tr>
            `;
            document.getElementById('btn_submit_return').disabled = true;
            document.getElementById('total_dibawa_count').innerText = '0';
            document.getElementById('selected_dibawa_count').innerText = '0';
            document.getElementById('search_return_devices').classList.add('hidden');
            document.getElementById('search_return_devices').value = '';
            return;
          }

          document.getElementById('total_dibawa_count').innerText = data.length;
          document.getElementById('selected_dibawa_count').innerText = '0';
          document.getElementById('search_return_devices').classList.remove('hidden');

          data.forEach(device => {
            const pendingType = device.pending_type;
            const isPending = pendingType !== '';
            const statusHtml = isPending 
                ? '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-semibold">Dibawa</span> <span class="px-2 py-1 bg-[#1C4D8D] text-white rounded-full text-[10px] font-semibold ml-1">Pengajuan ' + (pendingType === 'return' ? 'Pengembalian' : 'Pemasangan') + '</span>'
                : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-semibold">Dibawa</span>';
            const cbHtml = isPending 
                ? `<input type="checkbox" class="return-device-cb w-3 h-3 cursor-not-allowed opacity-50" disabled value="${device.mutasi_id}" title="Sedang dalam pengajuan">`
                : `<input type="checkbox" class="return-device-cb w-3 h-3 cursor-pointer accent-[#1C4D8D]" value="${device.mutasi_id}">`;

            const qtyHtml = device.is_nonreg == 1
                ? `<input type="number" min="1" max="${device.qty}" value="${device.qty}" class="return-device-qty w-12 border border-gray-300 rounded px-1 py-0.5 text-center text-xs focus:ring-[#1C4D8D]" ${isPending ? 'disabled' : ''}>`
                : `<span class="text-gray-500">1</span>`;

            tbody.innerHTML += `
              <tr class="device-row hover:bg-gray-50 transition-colors ${isPending ? 'opacity-75' : ''}">
                <td class="px-2 py-2 text-center border-b">
                  ${cbHtml}
                </td>
                <td class="px-2 py-2 border-b text-gray-700">${device.noreg}</td>
                <td class="px-2 py-2 border-b text-gray-700 max-w-[200px] truncate" title="${device.nama}">${device.nama}</td>
                <td class="px-2 py-2 border-b text-center">${qtyHtml}</td>
                <td class="px-2 py-2 border-b text-center">${statusHtml}</td>
              </tr>
            `;
          });

           document.getElementById('btn_submit_return').disabled = false;

          // Reset select all checkbox
          var selectAll = document.getElementById('selectAllReturn');
          if (selectAll) selectAll.checked = false;
        });
    });

    function updateSelectedCount() {
      var selectedCount = document.querySelectorAll('.return-device-cb:checked').length;
      document.getElementById('selected_dibawa_count').innerText = selectedCount;
    }

    const searchReturnInput = document.getElementById('search_return_devices');
    if (searchReturnInput) {
      searchReturnInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('#return_devices_list tr.device-row');
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(keyword) ? '' : 'none';
        });
      });
    }

    const searchInstallInput = document.getElementById('search_install_devices');
    if (searchInstallInput) {
      searchInstallInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('#install_devices_list tr.install-device-row');
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(keyword) ? '' : 'none';
        });
      });
    }

    window.resetReturnForm = function() {
      if (typeof tsReturnUser !== 'undefined') {
        tsReturnUser.clear();
      }
    };

    // Use event delegation for dynamically created checkboxes
    var returnList = document.getElementById('return_devices_list');
    if (returnList) {
      returnList.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('return-device-cb')) {
          updateSelectedCount();
          
          // Update "Select All" checkbox state
          var allCbs = document.querySelectorAll('.return-device-cb');
          var checkedCbs = document.querySelectorAll('.return-device-cb:checked');
          var selectAll = document.getElementById('selectAllReturn');
          if (selectAll) {
            selectAll.checked = (allCbs.length > 0 && allCbs.length === checkedCbs.length);
          }
        }
      });
    }

    var selectAllEl = document.getElementById('selectAllReturn');
    if (selectAllEl) {
      selectAllEl.addEventListener('change', function() {
        var isChecked = this.checked;
        var checkboxes = document.querySelectorAll('.return-device-cb');
        checkboxes.forEach(function(cb) { cb.checked = isChecked; });
        updateSelectedCount();
      });
    }
    
    window.switchTab = function(tab) {
      const slider = document.getElementById('slider_container');
      const tabReq = document.getElementById('tab_request');
      const tabRet = document.getElementById('tab_return');
      const tabInst = document.getElementById('tab_install');

      const activeClass = 'w-1/3 py-3 font-extrabold text-[#1C4D8D] text-center border-b-4 border-[#1C4D8D] border-t-0 border-l-0 border-r-0 outline-none focus:outline-none focus:ring-0 transition-all text-[10px] md:text-sm';
      const inactiveClass = 'w-1/3 py-3 font-extrabold text-gray-400 text-center border-b-4 border-transparent hover:text-[#1C4D8D] border-t-0 border-l-0 border-r-0 outline-none focus:outline-none focus:ring-0 transition-all text-[10px] md:text-sm';

      tabReq.className = inactiveClass;
      tabRet.className = inactiveClass;
      tabInst.className = inactiveClass;

      if (tab === 'request') {
        slider.style.transform = 'translateX(0%)';
        tabReq.className = activeClass;
      } else if (tab === 'return') {
        slider.style.transform = 'translateX(-33.333%)';
        tabRet.className = activeClass;
      } else if (tab === 'install') {
        slider.style.transform = 'translateX(-66.666%)';
        tabInst.className = activeClass;
      }
    }

    window.submitReturnRequest = function() {
      const checkboxes = document.querySelectorAll('.return-device-cb:checked');
      if (checkboxes.length === 0) {
        showToast('Pilih setidaknya satu perangkat untuk dikembalikan', 'warning');
        return;
      }

      const selectedData = Array.from(checkboxes).map(cb => {
        const row = cb.closest('tr');
        const qtyInput = row.querySelector('.return-device-qty');
        return {
          id: cb.value,
          qty: qtyInput ? qtyInput.value : 1
        };
      });

      const btn = document.getElementById('btn_submit_return');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

      var params = new URLSearchParams();
      selectedData.forEach(function(item) {
        params.append('mutasi_ids[]', item.id);
        params.append('qtys[]', item.qty);
      });

      const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
      const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';
      params.append('csrf_test_name', csrfToken);

      fetch(`<?= base_url('form/return') ?>`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: params
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          showToast(res.message, 'success');
          switchTab('request');
          tsReturnUser.clear();
        } else {
          showToast(res.message, 'error');
        }
      })
      .catch(err => {
        showToast('Terjadi kesalahan', 'error');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Submit Request';
      });
    }

    // Installation Request Logic
    let tsInstallUser = new TomSelect("#install_user", {
      create: false,
    });

    let nodeData = {};

    // Fetch Arep, Site, and Node Data
    let tsInstallArep;
    let tsInstallSite;
    let tsInstallNode;

    fetch(`<?= base_url('form/nodes') ?>`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.json())
      .then(data => {
        nodeData = data;
        const arepSelect = document.getElementById('install_arep');
        Object.keys(data).forEach(arep => {
          const option = document.createElement('option');
          option.value = arep;
          option.textContent = arep;
          arepSelect.appendChild(option);
        });

        tsInstallArep = new TomSelect("#install_arep", {
          create: false,
        });

        tsInstallSite = new TomSelect("#install_site", {
          create: false,
        });

        tsInstallNode = new TomSelect("#install_node", {
          create: false,
        });

        tsInstallArep.on('change', function(selectedArep) {
          tsInstallSite.clearOptions();
          tsInstallSite.clear();
          tsInstallNode.clearOptions();
          tsInstallNode.clear();
          
          if (selectedArep && nodeData[selectedArep]) {
            tsInstallSite.enable();
            const sites = Object.keys(nodeData[selectedArep]).sort((a, b) => a.localeCompare(b, undefined, {numeric: true, sensitivity: 'base'}));
            sites.forEach(site => {
              tsInstallSite.addOption({value: site, text: site});
            });
          } else {
            tsInstallSite.disable();
            tsInstallSite.addOption({value: "", text: "Pilih Site"});
            tsInstallNode.disable();
            tsInstallNode.addOption({value: "", text: "Pilih Node"});
          }
        });

        tsInstallSite.on('change', function(selectedSite) {
          tsInstallNode.clearOptions();
          tsInstallNode.clear();
          
          const selectedArep = tsInstallArep.getValue();
          if (selectedArep && selectedSite && nodeData[selectedArep] && nodeData[selectedArep][selectedSite]) {
            tsInstallNode.enable();
            const nodes = [...nodeData[selectedArep][selectedSite]].sort((a, b) => a.localeCompare(b, undefined, {numeric: true, sensitivity: 'base'}));
            nodes.forEach(node => {
              tsInstallNode.addOption({value: node, text: node});
            });
          } else {
            tsInstallNode.disable();
            tsInstallNode.addOption({value: "", text: "Pilih Node"});
          }
        });
      });

    const installArepSelect = document.getElementById('install_arep');
    const installSiteSelect = document.getElementById('install_site');
    const installNodeSelect = document.getElementById('install_node');

    tsInstallUser.on('change', function(userId) {
      if (!userId) {
        document.getElementById('install_devices_list').innerHTML = `
          <tr>
            <td colspan="5" class="px-4 py-4 text-center text-gray-400 italic">
              Pilih user untuk melihat perangkat.
            </td>
          </tr>
        `;
        document.getElementById('btn_submit_install').disabled = true;
        document.getElementById('total_install_count').innerText = '0';
        document.getElementById('selected_install_count').innerText = '0';
        document.getElementById('search_install_devices').classList.add('hidden');
        document.getElementById('search_install_devices').value = '';
        return;
      }

      document.getElementById('install_devices_list').innerHTML = `
        <tr>
          <td colspan="5" class="px-4 py-4 text-center text-gray-400 italic">
            <i class="fa-solid fa-spinner fa-spin"></i> Memuat perangkat...
          </td>
        </tr>
      `;

      // Reusing the same endpoint, but we should ideally filter out already pending installations. 
      // For now we use the same endpoint as return requests.
      fetch(`<?= base_url('form/devices') ?>/${userId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('install_devices_list');
          tbody.innerHTML = '';
          
          if (data.length === 0) {
            tbody.innerHTML = `
              <tr>
                <td colspan="5" class="px-4 py-4 text-center text-gray-400 italic">
                  Tidak ada perangkat yang dibawa oleh user ini.
                </td>
              </tr>
            `;
            document.getElementById('btn_submit_install').disabled = true;
            document.getElementById('total_install_count').innerText = '0';
            document.getElementById('selected_install_count').innerText = '0';
            document.getElementById('search_install_devices').classList.add('hidden');
            document.getElementById('search_install_devices').value = '';
            return;
          }

          document.getElementById('total_install_count').innerText = data.length;
          document.getElementById('selected_install_count').innerText = '0';
          document.getElementById('search_install_devices').classList.remove('hidden');

          data.forEach(device => {
            const pendingType = device.pending_type;
            const isPending = pendingType !== '';
            const statusHtml = isPending 
                ? '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-semibold">Dibawa</span> <span class="px-2 py-1 bg-[#1C4D8D] text-white rounded-full text-[10px] font-semibold ml-1">Pengajuan ' + (pendingType === 'return' ? 'Pengembalian' : 'Pemasangan') + '</span>'
                : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-semibold">Dibawa</span>';
            const cbHtml = isPending 
                ? `<input type="checkbox" class="install-device-cb w-3 h-3 cursor-not-allowed opacity-50" disabled value="${device.mutasi_id}" title="Sedang dalam pengajuan">`
                : `<input type="checkbox" class="install-device-cb w-3 h-3 cursor-pointer accent-[#1C4D8D]" value="${device.mutasi_id}">`;

            const qtyHtml = device.is_nonreg == 1
                ? `<input type="number" min="1" max="${device.qty}" value="${device.qty}" class="install-device-qty w-12 border border-gray-300 rounded px-1 py-0.5 text-center text-xs focus:ring-[#1C4D8D]" ${isPending ? 'disabled' : ''}>`
                : `<span class="text-gray-500">1</span>`;

            tbody.innerHTML += `
              <tr class="install-device-row hover:bg-gray-50 transition-colors ${isPending ? 'opacity-75' : ''}">
                <td class="px-2 py-2 text-center border-b">
                  ${cbHtml}
                </td>
                <td class="px-2 py-2 border-b text-gray-700">${device.noreg}</td>
                <td class="px-2 py-2 border-b text-gray-700 max-w-[200px] truncate" title="${device.nama}">${device.nama}</td>
                <td class="px-2 py-2 border-b text-center">${qtyHtml}</td>
                <td class="px-2 py-2 border-b text-center">${statusHtml}</td>
              </tr>
            `;
          });

           document.getElementById('btn_submit_install').disabled = false;

          var selectAll = document.getElementById('selectAllInstall');
          if (selectAll) selectAll.checked = false;
        });
    });

    function updateInstallSelectedCount() {
      var selectedCount = document.querySelectorAll('.install-device-cb:checked').length;
      document.getElementById('selected_install_count').innerText = selectedCount;
    }

    window.resetInstallForm = function() {
      if (typeof tsInstallUser !== 'undefined') {
        tsInstallUser.clear();
      }
      if (typeof tsInstallArep !== 'undefined') {
        tsInstallArep.clear();
      }
    };

    var installList = document.getElementById('install_devices_list');
    if (installList) {
      installList.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('install-device-cb')) {
          updateInstallSelectedCount();
          
          var allCbs = document.querySelectorAll('.install-device-cb:not(:disabled)');
          var checkedCbs = document.querySelectorAll('.install-device-cb:checked');
          var selectAll = document.getElementById('selectAllInstall');
          if (selectAll) {
            selectAll.checked = (allCbs.length > 0 && allCbs.length === checkedCbs.length);
          }
        }
      });
    }

    var selectAllInstallEl = document.getElementById('selectAllInstall');
    if (selectAllInstallEl) {
      selectAllInstallEl.addEventListener('change', function() {
        var isChecked = this.checked;
        var checkboxes = document.querySelectorAll('.install-device-cb:not(:disabled)');
        checkboxes.forEach(function(cb) { cb.checked = isChecked; });
        updateInstallSelectedCount();
      });
    }

    window.submitInstallRequest = function() {
      const checkboxes = document.querySelectorAll('.install-device-cb:checked');
      if (checkboxes.length === 0) {
        showToast('Pilih setidaknya satu perangkat untuk dipasang', 'warning');
        return;
      }
      
      const arep = installArepSelect.value;
      const siteSentral = installSiteSelect.value;
      const nodeSentral = installNodeSelect.value;
      if (!arep || !siteSentral) {
        showToast('Pilih Arep dan Site Sentral', 'warning');
        return;
      }

      const selectedData = Array.from(checkboxes).map(cb => {
        const row = cb.closest('tr');
        const qtyInput = row.querySelector('.install-device-qty');
        return {
          id: cb.value,
          qty: qtyInput ? qtyInput.value : 1
        };
      });

      const btn = document.getElementById('btn_submit_install');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

      var params = new URLSearchParams();
      selectedData.forEach(function(item) {
        params.append('mutasi_ids[]', item.id);
        params.append('qtys[]', item.qty);
      });
      params.append('arep', arep);
      params.append('site_sentral', siteSentral);
      params.append('node_sentral', nodeSentral);

      const csrfTokenElement = document.querySelector('input[name="csrf_test_name"]');
      const csrfToken = csrfTokenElement ? csrfTokenElement.value : '<?= csrf_hash() ?>';
      params.append('csrf_test_name', csrfToken);

      fetch(`<?= base_url('form/installation') ?>`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: params
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          showToast(res.message, 'success');
          switchTab('request');
          resetInstallForm();
        } else {
          showToast(res.message, 'error');
        }
      })
      .catch(err => {
        showToast('Terjadi kesalahan', 'error');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Submit Request';
      });
    }
  });
</script>

<!-- BRP Download Modal -->
<div id="brpDownloadModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] max-w-sm p-6 text-center transform transition-all">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <i class="fa-solid fa-check-circle text-3xl text-green-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Peminjaman Berhasil!</h3>
        <p class="text-sm text-gray-500 mb-6">Silakan download Bukti Request Perangkat (BRP) Anda di bawah ini</p>
        
        <div class="flex flex-col gap-3">
            <a href="<?= base_url('submit/pdf') ?>" target="_blank" onclick="document.getElementById('brpDownloadModal').classList.add('hidden'); document.getElementById('brpDownloadModal').classList.remove('flex');"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#1C4D8D] text-base font-medium text-white hover:bg-[#2A62AA] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1C4D8D] sm:text-sm">
                <i class="fa-solid fa-download mr-2 mt-1"></i> Download PDF
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
