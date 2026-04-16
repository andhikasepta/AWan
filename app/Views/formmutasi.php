<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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
          Mohon untuk pengambilan dan peminjaman perangkat dapat mengisi form yang sudah disediakan
        </p>
        <p class="text-[10px] italic text-white">
          Note: <br>
          Pengambilan dan mutasi perangkat <span class="font-bold">WAJIB</span> menginformasikan administrator
        </p>
      </div>

      <p class="text-[11px] font-bold mt-5 mb-1">BUTUH BANTUAN?</p>
      <p class="text-[11px] mb-4">Silakan hubungi admin melalui whatsapp di bawah ini</p>
      <a href="https://wa.me/6282133601435?text=Woi%20admin,%20mau%20konfirmasi%20tentang%20mutasi%20perangkat"
        target="_blank"
        class="bg-white text-[#1C4D8D] font-bold px-2 py-3 rounded-lg w-full text-sm shadow hover:bg-gray-100 transition text-center flex items-center justify-center gap-2">
        Hubungi Admin
      </a>
    </div>

    <div class="mt-8">

    </div>
  </div>

  <div class="w-full md:w-4/5 bg-white p-6 md:p-10 min-h-[510px]">
    <h2 class="text-center text-xl font-extrabold text-[#1C4D8D] mb-8">FORM MUTASI</h2>

    <form action="<?= base_url('/submit') ?>" method="POST">
      <div class="grid grid-cols-1 md:grid-cols-[1fr_3fr] gap-6 mb-5">

        <div class="w-full flex flex-col relative">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">No Registrasi</label>
          <input type="text" id="noreg_input" placeholder="Scan barcode atau ketik noreg"
            class="w-full rounded-md p-2 text-sm min-h-[42px] border border-gray-300 focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D]">

          <!-- Daftar tersembunyi untuk pencarian (Opsional untuk validasi JS) -->
          <div id="status_scan" class="text-[10px] mt-1 hidden"></div>
        </div>

        <!-- Hidden input untuk ID Perangkat (tetap ada) -->
        <input type="hidden" id="id_perangkat" name="id_perangkat">
        <input type="hidden" id="noreg_hidden" name="noreg"> <!-- Ini yang dikirim ke server -->


        <div class="flex flex-col ">
          <label class="font-semibold text-[#1C4D8D] text-sm mb-2">Nama Perangkat</label>
          <div class="bg-[#EFEFEF] border rounded-md p-2 min-h-[42px] overflow-x-auto focus:outline-none" readonly>
            <span id="nama_perangkat_text"></span>
          </div>
        </div>
      </div>

      <input type="hidden" id="id_perangkat" name="id_perangkat">

      <div class="flex flex-col mb-4">
        <label class="font-semibold text-[#1C4D8D] text-sm mb-2">User</label>
        <select id="user" name="user" class="">
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
            <textarea name="keterangan" rows="2" placeholder="Masukkan Keterangan" class="border rounded-md p-3 pr-10 text-sm w-full focus:outline-none focus:border-[#1C4D8D] focus:ring-1 focus:ring-[#1C4D8D] resize-none"></textarea>
          </div>
        </div>
      </div>

      <div class="flex">
        <button type="submit" class="bg-[#1C4D8D] text-sm text-white px-8 py-2 rounded-md font-semibold shadow hover:bg-[#7FB3D5] transition">
          Submit
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Data perangkat dari PHP ke JSON Javascript
    const daftarPerangkat = <?= json_encode($perangkat) ?>;

    const inputScan = document.getElementById('noreg_input');
    const textNama = document.getElementById('nama_perangkat_text');
    const inputId = document.getElementById('id_perangkat');
    const inputNoregHidden = document.getElementById('noreg_hidden');

    inputScan.addEventListener('keydown', function(e) {
      if (e.keyCode === 13) { // Jika Enter dari Scanner
        e.preventDefault(); // Stop form submit otomatis

        const noregDiScan = this.value.trim();

        // Cari yang SAMA PERSIS (Exact Match)
        const hasil = daftarPerangkat.find(p => p.noreg.toLowerCase() === noregDiScan.toLowerCase());

        if (hasil) {
          // JIKA KETEMU
          textNama.innerText = hasil.nama;
          inputId.value = hasil.id;
          inputNoregHidden.value = hasil.noreg;

          // Beri tanda sukses
          this.classList.remove('border-red-500');
          this.classList.add('border-green-500');
        } else {
          // JIKA TIDAK KETEMU
          alert("No Registrasi '" + noregDiScan + "' tidak terdaftar di sistem!");
          this.value = "";
          textNama.innerText = "";
          inputId.value = "";
          inputNoregHidden.value = "";

          this.classList.add('border-red-500');
        }
      }
    });

    // Inisialisasi TomSelect hanya untuk USER (karena user tidak di-scan)
    new TomSelect("#user", {
      create: false
    });
  });
</script>
<?= $this->endSection() ?>