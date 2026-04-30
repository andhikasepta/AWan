<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div id="toast" class="fixed top-20 right-5 z-50 hidden transform transition-all duration-300 translate-x-full">
  <div id="toastBox" class="flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-white text-sm">
    <i id="toastIcon" class="fa-solid"></i>
    <span id="toastMsg"></span>
  </div>
</div>

<div class="w-full max-w-[1450px] mx-auto rounded-lg overflow-hidden shadow-lg flex flex-col md:flex-row">
  <div class="w-full md:w-1/5 bg-[#3E679E] p-6 md:p-8 text-white flex flex-col justify-between md:min-h-[510px]">
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
      <a href="https://wa.me/6282133601435?text=Admin,%20terkendala%20No.Registrasi%20perangkat%20tidak%20terdaftar"
        target="_blank"
        class="bg-white text-[#1C4D8D] font-bold px-2 py-3 rounded-lg w-full text-sm shadow hover:bg-gray-100 transition text-center flex items-center justify-center gap-2">
        Hubungi Admin
      </a>
    </div>

    <div class="mt-8">

    </div>
  </div>

  <div class="w-full md:w-4/5 bg-white p-6 md:p-10 min-h-[510px]">
    <h2 class="text-center text-xl font-extrabold text-[#1C4D8D] mb-8">FORM REQUEST PERANGKAT</h2>

    <form action="<?= base_url('/submit') ?>" method="POST">
      <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-6 mb-5">

        <div class="w-full flex flex-col relative">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">No Registrasi</label>
          <input type="text" id="noreg_input" placeholder="Scan barcode atau ketik noreg"
            class="text-xs w-full rounded-md p-2 min-h-[42px] border border-gray-300 focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D]">

          <div id="status_scan" class="text-[10px] mt-1 hidden"></div>
        </div>

        <div class="w-full flex flex-col justify-end">
          <label class="invisible text-sm mb-2">Hidden</label>
          <button type="button" id="btn_tambah"
            class="bg-[#1C4D8D] h-[42px] px-4 py-1 text-xs text-white rounded-md font-semibold shadow hover:bg-[#7FB3D5] transition items-end flex items-center justify-center gap-2">
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

      <div class="mt-4 mb-4">
        <h3 class="font-semibold text-sm mb-2 text-[#1C4D8D]">Daftar Perangkat</h3>
        <table class="w-full text-xs border">
          <thead class="bg-gray-100 border border-gray-300">
            <tr>
              <th class="p-2 border border-gray-300">No</th>
              <th class="p-2 border border-gray-300">Nomor Registrasi</th>
              <th class="p-2 border border-gray-300">Nama Perangkat</th>
              <th class="p-2 border border-gray-300">Action</th>
            </tr>
          </thead>
          <tbody id="list_perangkat"></tbody>
        </table>
      </div>

      <div class="flex flex-col mb-4">
        <label class="font-semibold text-[#1C4D8D] text-sm mb-2">User</label>
        <select id="user" name="user" class="" required>
          <style>
            .ts-control {
              border-radius: 0.375rem;
              /* rounded-md */
              padding: 0.7rem;
              /* p-2 */
              border: 1px solid #d1d5db;
              /* border gray */
            }

            .ts-control:focus {
              border-color: #1C4D8D;
              box-shadow: 0 0 0 1px #1C4D8D;
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
          class="bg-[#1C4D8D] text-sm text-white px-8 py-2 rounded-md font-semibold shadow hover:bg-[#7FB3D5] transition">
          Submit
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Scanner -->
<div id="scannerModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">
    <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
      <h3 class="font-bold">Scan Barcode / QR Code</h3>
      <button type="button" id="closeScanner" class="text-white hover:text-red-300 transition">
        <i class="fa-solid fa-xmark text-xl"></i>
      </button>
    </div>
    <div class="p-4 flex flex-col items-center">
      <div id="reader" class="w-full"></div>
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

  document.addEventListener("DOMContentLoaded", function () {
    <?php if (session()->getFlashData('success')): ?>
      showToast("<?= session()->getFlashdata('success') ?>", "success");

      <?php if (session()->get('mutasi_pdf_ids')): ?>
        Swal.fire({
          title: "Data berhasil disimpan!",
          text: "Download Bukti Request Perangkat",
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#1C4D8D",
          cancelButtonColor: "#858585",
          confirmButtonText: '<i class="fa-solid fa-file-pdf"></i> Download PDF',
          cancelButtonText: "Lewati",
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then((result) => {
          if (result.isConfirmed) {
            window.open("<?= base_url('submit/pdf') ?>");
          } else {
            // Clear session data if user skips
            fetch("<?= base_url('submit/pdf/clear') ?>");
          }
        });
      <?php endif; ?>
    <?php endif; ?>

    const daftarPerangkat = <?= json_encode($perangkat) ?>;
    const inputScan = document.getElementById('noreg_input');

    let cart = [];

    function multiAdd(noregInput) {
      const noreg = noregInput.trim();

      if (!noreg) {
        showToast("Masukkan noreg terlebih dahulu", "warning");
        return;
      }

      const hasil = daftarPerangkat.find(p => p.noreg.toLowerCase() === noreg.toLowerCase());

      if (!hasil) {
        showToast("No registrasi tidak tersedia", "error");
        return;
      }

      if (cart.some(item => item.noreg === hasil.noreg)) {
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
    }

    inputScan.addEventListener('keydown', function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        multiAdd(this.value);
      }
    });

    let html5QrcodeScanner;

    let isScanning = false;

    document.getElementById('btn_tambah').addEventListener('click', function () {
      document.getElementById('scannerModal').classList.remove('hidden');
      isScanning = true;

      html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
          fps: 30,
          qrbox: { width: 300, height: 300 }
        },
        /* verbose= */ false);

      html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });

    function onScanSuccess(decodedText, decodedResult) {
      if (!isScanning) return;
      isScanning = false;

      showToast("Barcode terdeteksi!", "success");

      document.getElementById('scannerModal').classList.add('hidden');
      inputScan.value = decodedText;
      multiAdd(decodedText);

      html5QrcodeScanner.clear().catch(err => console.error(err));
    }

    function onScanFailure(error) {
      // ignore failures to keep scanning
    }

    document.getElementById('closeScanner').addEventListener('click', function () {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().then(() => {
          document.getElementById('scannerModal').classList.add('hidden');
        });
      } else {
        document.getElementById('scannerModal').classList.add('hidden');
      }
    });

    function renderTable() {
      const tbody = document.getElementById('list_perangkat');
      tbody.innerHTML = "";

      cart.forEach((item, index) => {
        tbody.innerHTML += `
        <tr>
          <td class="p-2 text-center border border-gray-300">${index + 1}</td>
          <td class="p-2 border border-gray-300">${item.noreg}</td>
          <td class="p-2 border border-gray-300">${item.nama}</td>
          <td class="p-2 text-center border border-gray-300">
            <button onclick="hapusItem(${index})" class="text-red-500">
            <span>
            <i class="fa-solid fa-trash"></i>
            </span>
            </button>
          </td>
        </tr>

        <input type="hidden" name="perangkat[${index}][id]" value="${item.id}">
        <input type="hidden" name="perangkat[${index}][noreg]" value="${item.noreg}">
        `;
      });
    }

    window.hapusItem = function (index) {
      cart.splice(index, 1);
      renderTable();
    }

    const form = document.querySelector("form");
    const btnSubmit = document.getElementById("btn_submit");

    form.addEventListener("submit", function (e) {
      if (cart.length === 0) {
        e.preventDefault();
        showToast("Tambahkan minimal 1 perangkat!", "warning");
        return;
      }

      btnSubmit.disabled = true;
    });

    new TomSelect("#user", {
      create: false,
    });
  });
</script>
<?= $this->endSection() ?>