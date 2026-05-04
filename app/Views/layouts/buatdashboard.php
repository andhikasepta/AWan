<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" href="<?= base_url('images/LogoIcon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

    <nav class="fixed w-full bg-[#1C4D8D] text-white px-6 pt-1 pb-1 flex justify-between items-center shadow-md z-[49]">
        <img src="<?= base_url('images/awan.png') ?>" width="200px">

        <div x-data="{open:false}" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 cursor-pointer transition group text-white hover:text-[#B3B3B3]">
                <i class="fa-regular fa-circle-user text-xl mb-1"></i>
                <span class="text-sm font-medium">
                    <?= session('nama_admin') ?? 'admin' ?>
                </span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{'rotate-180' : open}"></i>
            </button>

            <div x-show="open" x-transition @click.outside="open = false"
                class="absolute right-0 mt-2 w-48 bg-white text-black rounded-md shadow-lg text-sm">

                <button onclick="bukaModalPassword()" @click="open = false"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                    Ganti Password
                </button>
                <button onclick="openUserManage()" @click="open = false"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                    User Manage
                </button>
                <a href="<?= base_url('admin-manage') ?>" @click="open = false" class="block px-4 py-2 hover:bg-gray-100">
                    Admin Manage
                </a>
                <a href="<?= base_url('logout') ?>"
                    class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 pt-24 pl-6 pr-6">
        <?= $this->renderSection('content') ?>
    </main>
    <div id="overlayPassword" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white border border-gray-200 rounded-md shadow-md w-full max-w-lg p-6 relative">

            <button onclick="tutupModalPassword()"
                class="absolute right-3 top-3 text-black text-lg font-bold focus:outline-none hover:text-gray-400">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-[#1C4D8D]">Ganti Password</h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('update-password') ?>" method="post" class="flex flex-col space-y-4">
                <?= csrf_field() ?>

                <div class="grid grid-cols-[180px,1fr] gap-x-6 gap-y-4 items-center text-left mb-5">

                    <label class="font-semibold text-[#1C4D8D] text-sm" for="current_password">Password Lama</label>
                    <div class="relative">
                        <input name="current_password" id="current_password" type="password"
                            class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]"
                            required>
                        <button type="button" onclick="showHide('current_password', 'eye_old')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i id="eye_old" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>

                    <label class="font-semibold text-[#1C4D8D] text-sm" for="new_password">Password Baru</label>
                    <div class="relative">
                        <input name="new_password" id="new_password" type="password"
                            class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]"
                            required>
                        <button type="button" onclick="showHide('new_password', 'eye_new')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i id="eye_new" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>

                    <label class="font-semibold text-[#1C4D8D] text-sm" for="confirm_password">Konfirmasi
                        Password</label>
                    <div class="relative">
                        <input name="confirm_password" id="confirm_password" type="password"
                            class="w-full border rounded-sm p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#1C4D8D]"
                            required>
                        <button type="button" onclick="showHide('confirm_password', 'eye_conf')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i id="eye_conf" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="submit"
                        class="bg-[#1C4D8D] text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    <footer class="mt-auto text-xs text-[#1C4D8D] p-2 text-center">
        &copy; <?= date('Y') ?> PT. Aplikanusa Lintasarta
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <?= $this->renderSection('scripts') ?>

    <script>
        const overlay = document.getElementById('overlayPassword');

        function bukaModalPassword() {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            localStorage.setItem('showModal', 'true');

        }

        function tutupModalPassword() {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            localStorage.removeItem('showModal');
        }

        window.onclick = function (event) {
            if (event.target == overlay) {
                tutupModalPassword();
            }
        }
        window.addEventListener('load', function () {
            if (localStorage.getItem('showModal') === 'true') {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
        });
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
            document.addEventListener("DOMContentLoaded", function () {
                <?php if (session()->getFlashdata('show_modal')): ?>
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