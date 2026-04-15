<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.all.min.js"></script>
    <script src="sweetalert2.min.js"></script>

    <link rel="icon" href="<?= base_url('images/LogoLintas.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
    <link rel="stylesheet" href="sweetalert2.min.css">
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

<body class="bg-[#F1F1F1] h-screen flex flex-col overflow-hidden">
    <?php $uri = service('uri'); ?>

    <nav class="fixed w-full bg-[#1C4D8D] text-white px-6 flex justify-between items-center shadow-md">
        <img src="<?= base_url('images/awan.png') ?>" width="250px">

        <div class="flex gap-8 items-center">
            <a href="javascript:void(0)" onclick="bukaModalPassword()" class="flex flex-col items-center cursor-pointer transition group text-white hover:text-gray-300">
                <i class="fa-solid fa-key text-2xl mb-1 rotate-[135deg]"></i>
                <span class="text-sm font-medium">Password</span>
            </a>
            <a href="<?= base_url('logout') ?>" class="flex flex-col items-center cursor-pointer transition group
                <?= $uri->getSegment(1) == 'logout' ? 'text-[#7FB3D5] font-semibold border-b-2 border-[#7FB3D5]' : 'hover:text-[#7FB3D5]' ?>">
                <i class="fa-solid fa-arrow-right-from-bracket text-xl mb-1"></i>
                <span class="text-sm">Logout</span>
            </a>
        </div>
    </nav>

    <main class="flex-1 pt-24 pl-6 pr-6">
        <?= $this->renderSection('content') ?>
    </main>
        <div id="overlayPassword" class="fixed inset-0 left-0 top-0 z-[9999] hidden bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl p-12 relative">
                <button onclick="tutupModalPassword()" class="absolute top-8 right-10 text-3xl font-light hover:text-red-500">&times;</button>
                <h2 class="text-3xl font-bold mb-12 text-left text-[#1C4D8D]">Ganti Password</h2>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('update-password') ?>" method="post" class="flex flex-col">
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-[180px,1fr] gap-x-6 gap-y-4 items-center text-left">
                        <label class="font-semibold text-[#1C4D8D] text-sm" for="current_password">Password Lama</label>
                        <div class="relative">
                            <input name="current_password" id="current_password" type="password" required 
                                class="w-full border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                            <button type="button" onclick="showHide('current_password', 'eye_old')" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <i id="eye_old" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>

                        <label class="font-semibold text-[#1C4D8D] text-sm" for="new_password">Password Baru</label>
                        <div class="relative">
                            <input name="new_password" id="new_password" type="password" required 
                                class="w-full border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                            <button type="button" onclick="showHide('new_password', 'eye_new')" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <i id="eye_new" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>

                        <label class="font-semibold text-[#1C4D8D] text-sm" for="confirm_password">Konfirmasi Password</label>
                        <div class="relative">
                            <input name="confirm_password" id="confirm_password" type="password" required 
                                class="w-full border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]">
                            <button type="button" onclick="showHide('confirm_password', 'eye_conf')" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <i id="eye_conf" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button class="bg-[#1C4D8D] w-full text-white p-3 rounded-lg font-bold shadow-md hover:bg-[#3E679E] transition mt-8" type="submit">
                        Ganti Password
                    </button>
                </form>
            </div>
        </div> 
    <footer class="mt-auto text-xs text-[#1C4D8D] p-2 text-center">
        &copy; <?= date('Y') ?> PT. Aplikanusa Lintasarta
    </footer>

    <?= $this->renderSection('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
    function bukaModalPassword() {
        const overlay = document.getElementById('overlayPassword');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
    }

    function tutupModalPassword() {
        const overlay = document.getElementById('overlayPassword');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }

    window.onclick = function(event) {
        const overlay = document.getElementById('overlayPassword');
        if (event.target == overlay) {
            tutupModalPassword();
        }
    }
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
    document.addEventListener("DOMContentLoaded", function() {
        <?php if (session()->getFlashdata('show_modal')) : ?>
            const modal = document.getElementById('modalUpdatePassword');
            if (modal) {
                modal.classList.remove('hidden'); 
                modal.classList.add('flex'); 
            }
        <?php endif; ?>
    });
}
</script>
</body>

</html>