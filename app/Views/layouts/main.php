<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://cdn.tailwindcss.com"></script>

  <title>AWan - Asset Warehouse Management</title>

  <link rel="icon" href="<?= base_url('images/LogoIcon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Poppins';
      background-color: #ffffff;
    }

    .bg-custom-blue {
      background-color: #1e4b8f;
    }

    .text-custom-blue {
      color: #1e4b8f;
    }
  </style>
</head>

<body class="bg-[#F1F1F1] h-screen flex flex-col">
  <?php $uri = service('uri'); ?>

  <nav id="mainNav" class="fixed top-0 left-0 w-full bg-[#1C4D8D] text-white px-4 md:px-6 py-2 flex justify-between items-center shadow-md z-50">
    <img src="<?= base_url('images/awan.png') ?>" class="w-[120px] md:w-[150px]">

    <!-- Hamburger button (mobile only) -->
    <button id="hamburgerBtn" onclick="toggleMobileMenu()" class="md:hidden text-white focus:outline-none p-2">
      <i id="hamburgerIcon" class="fa-solid fa-bars text-xl"></i>
    </button>

    <!-- Desktop nav -->
    <div class="hidden md:flex gap-8 items-center">
      <a href="<?= base_url('/') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2
        <?= $uri->getSegment(1) == '' ? 'border-white text-white font-semibold' : 'border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]' ?>">
        <i class="fa-solid fa-table-list text-lg mb-1"></i>
        <span class="text-sm">Form</span>
      </a>

      <a href="<?= base_url('history') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2
        <?= $uri->getSegment(1) == 'history' ? 'border-white text-white font-semibold' : 'border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]' ?>">
        <i class="fa-solid fa-clock-rotate-left text-lg mb-1"></i>
        <span class="text-sm">History</span>
      </a>

      <a href="<?= base_url('login') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2
        <?= $uri->getSegment(1) == 'login' ? 'border-white text-white font-semibold' : 'border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]' ?>">
        <i class="fa-solid fa-arrow-right-to-bracket text-lg mb-1"></i>
        <span class="text-sm">Login</span>
      </a>
    </div>
  </nav>

  <!-- Mobile menu (hidden by default) -->
  <div id="mobileMenu" class="fixed left-0 w-full bg-[#1C4D8D] text-white z-40 shadow-lg hidden md:hidden">
    <div class="flex flex-col divide-y divide-[#2a5fa0]">
      <a href="<?= base_url('/') ?>" class="flex items-center gap-3 px-6 py-3 transition
        <?= $uri->getSegment(1) == '' ? 'bg-[#163d73] font-semibold border-l-4 border-white' : 'hover:bg-[#163d73] border-l-4 border-transparent' ?>">
        <i class="fa-solid fa-table-list"></i>
        <span class="text-sm">Form</span>
      </a>
      <a href="<?= base_url('history') ?>" class="flex items-center gap-3 px-6 py-3 transition
        <?= $uri->getSegment(1) == 'history' ? 'bg-[#163d73] font-semibold border-l-4 border-white' : 'hover:bg-[#163d73] border-l-4 border-transparent' ?>">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span class="text-sm">History</span>
      </a>
      <a href="<?= base_url('login') ?>" class="flex items-center gap-3 px-6 py-3 transition
        <?= $uri->getSegment(1) == 'login' ? 'bg-[#163d73] font-semibold border-l-4 border-white' : 'hover:bg-[#163d73] border-l-4 border-transparent' ?>">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>
        <span class="text-sm">Login</span>
      </a>
    </div>
  </div>

  <script>
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      const icon = document.getElementById('hamburgerIcon');
      const nav = document.getElementById('mainNav');
      menu.style.top = nav.offsetHeight + 'px';
      menu.classList.toggle('hidden');
      if (menu.classList.contains('hidden')) {
        icon.classList.remove('fa-xmark');
        icon.classList.add('fa-bars');
      } else {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-xmark');
      }
    }
  </script>

  <main class="flex-1 pt-28 pl-4 pr-4">
    <?= $this->renderSection('content') ?>
  </main>

  <footer class="mt-auto text-xs text-[#1C4D8D] p-2 text-center">
    Unreleased &bull; PT. Aplikanusa Lintasarta &copy; <?= date('Y') ?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script>
    function showHide(idInput, idIcon) {
      const inputan = document.getElementById(idInput);
      const ikon = document.getElementById(idIcon);

      if (inputan.type === "password") {
        inputan.type = "text";
        ikon.classList.remove('fa-eye-slash');
        ikon.classList.add('fa-eye');
      } else {
        inputan.type = "password";
        ikon.classList.remove('fa-eye');
        ikon.classList.add('fa-eye-slash');
      }
    }
    document.addEventListener("DOMContentLoaded", function () {
      localStorage.removeItem('showModal');
    });
  </script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>