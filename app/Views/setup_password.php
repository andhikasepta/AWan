<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Atur Password - AWan</title>

    <script>
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) return;
            originalWarn.apply(console, args);
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="<?= base_url('images/LogoIcon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-[#F1F1F1] min-h-screen flex flex-col">

    <?php $uri = service('uri'); ?>

    <nav id="mainNav" class="fixed w-full bg-[#1C4D8D] text-white px-4 md:px-6 py-2 flex justify-between items-center shadow-md z-50">
        <img src="<?= base_url('images/awan.webp') ?>" class="w-[140px] md:w-[200px]">

        <!-- Hamburger button (mobile only) -->
        <button id="hamburgerBtn" onclick="toggleMobileMenu()" class="md:hidden text-white focus:outline-none p-2">
            <i id="hamburgerIcon" class="fa-solid fa-bars text-xl"></i>
        </button>

        <!-- Desktop nav -->
        <div class="hidden md:flex gap-8 items-center">
            <a href="<?= base_url('/') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2 border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]">
                <i class="fa-solid fa-table-list text-lg mb-1"></i>
                <span class="text-sm">Form</span>
            </a>

            <a href="<?= base_url('history') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2 border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]">
                <i class="fa-solid fa-clock-rotate-left text-lg mb-1"></i>
                <span class="text-sm">History</span>
            </a>

            <a href="<?= base_url('login') ?>" class="flex flex-col items-center cursor-pointer transition group py-2 border-b-2 border-transparent hover:text-[#B3B3B3] hover:border-[#B3B3B3]">
                <i class="fa-solid fa-arrow-right-to-bracket text-lg mb-1"></i>
                <span class="text-sm">Login</span>
            </a>
        </div>
    </nav>

    <!-- Mobile menu (hidden by default) -->
    <div id="mobileMenu" class="fixed left-0 w-full bg-[#1C4D8D] text-white z-40 shadow-lg hidden md:hidden">
        <div class="flex flex-col divide-y divide-[#2a5fa0]">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3 px-6 py-3 transition hover:bg-[#163d73] border-l-4 border-transparent">
                <i class="fa-solid fa-table-list"></i>
                <span class="text-sm">Form</span>
            </a>
            <a href="<?= base_url('history') ?>" class="flex items-center gap-3 px-6 py-3 transition hover:bg-[#163d73] border-l-4 border-transparent">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span class="text-sm">History</span>
            </a>
            <a href="<?= base_url('login') ?>" class="flex items-center gap-3 px-6 py-3 transition hover:bg-[#163d73] border-l-4 border-transparent">
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

    <main class="flex-1 flex items-center justify-center pt-20 pb-8 px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-lg">

            <div class="flex flex-col items-center mb-6">
                <div class="bg-[#1C4D8D] text-white rounded-full w-14 h-14 flex items-center justify-center mb-3 shadow-md">
                    <i class="fa-solid fa-key text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-[#1C4D8D] tracking-wide">Aktivasi Akun</h2>
            </div>

            <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-4 py-3 text-xs flex items-start gap-2 mb-6">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-yellow-500"></i>
                <span>Anda login sebagai <strong><?= esc(session('admin')['username'] ?? '') ?></strong>. Anda diwajibkan mengatur password baru untuk aktivasi akun.</span>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('setup-password') ?>" method="post" class="flex flex-col gap-4">
                <?= csrf_field() ?>

                <div class="flex flex-col">
                    <label class="font-semibold text-[#1C4D8D] text-sm mb-2" for="new_password">
                        Password Baru
                    </label>
                    <div class="relative">
                        <input name="new_password" id="new_password" type="password" required
                            placeholder="Minimal 12 karakter"
                            class="w-full text-sm border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400">
                        <button type="button" onclick="showHide('new_password', 'eye_new')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i id="eye_new" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="font-semibold text-[#1C4D8D] text-sm mb-2" for="confirm_password">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input name="confirm_password" id="confirm_password" type="password" required
                            placeholder="Ulangi password baru"
                            class="w-full text-sm border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400">
                        <button type="button" onclick="showHide('confirm_password', 'eye_conf')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i id="eye_conf" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="bg-[#1C4D8D] w-full text-sm text-white p-3 rounded-lg font-bold shadow-md hover:bg-[#3E679E] transition mt-2">
                    <i class="fa-solid fa-lock mr-2"></i> Submit & Login Ulang
                </button>
            </form>
        </div>
    </main>

    <footer class="text-xs text-[#1C4D8D] p-2 text-center">
        Unreleased &bull; PT. Aplikanusa Lintasarta &copy; <?= date('Y') ?>
    </footer>

    <script>
        function showHide(idInput, idIcon) {
            const inputan = document.getElementById(idInput);
            const ikon = document.getElementById(idIcon);
            if (inputan.type === "password") {
                inputan.type = "text";
                ikon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                inputan.type = "password";
                ikon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }
    </script>
</body>

</html>
