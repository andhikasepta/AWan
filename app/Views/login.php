<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-lg">
        <h2 class="text-center text-2xl font-bold text-[#1C4D8D] mb-8 tracking-wide">LOGIN</h2>
        <p class="text-xs text-blue-800 mb-2">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Akses Terbatas, Administrator Auth Required
        </p>
        <form action="<?= base_url('login') ?>" method="post" class="flex-flex-col gap-6">
            <div class="flex flex-col">
                <label class="font-semibold text-[#1C4D8D] text-sm mb-2" for="username">
                    Username
                </label>
                <input name="username"
                    class="text-sm border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400 mb-3"
                    id="username" placeholder="Masukkan username" required type="text" />
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-sm text-[#1C4D8D] text-sm mb-2" for="password">
                    Password
                </label>

                <div class="relative">
                    <input name="password" id="password" type="password" required placeholder="Masukkan password"
                        class="w-full text-sm border border-gray-200 rounded-lg p-3 pr-10 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400">

                    <button type="button" onclick="showHide('password', 'eye_password')"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                        <i id="eye_password" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-2 flex items-center gap-3"
                    role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="block sm:inline text-sm"><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>
            <button
                class="bg-[#1C4D8D] w-full text-sm text-white p-3 rounded-lg font-bold shadow-md hover:bg-[#7AAACE] transition mt-4"
                type="submit">
                Login
            </button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>