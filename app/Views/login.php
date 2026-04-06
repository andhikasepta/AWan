<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-lg">
        <h2 class="text-center text-2xl font-bold text-[#1C4D8D] mb-8 tracking-wide">LOGIN</h2>

        <form action="<?= base_url('login') ?>" method="post" class="flex-flex-col gap-6">
            <div class="flex flex-col">
                <label class="font-semibold text-[#1C4D8D] text-sm mb-2" for="username">
                    Username
                </label>
                <input name="username" class="border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400 mb-3" id="username" placeholder="Masukkan username" required type="text" />
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-[#1C4D8D] text-sm mb-2" for="password">
                    Password
                </label>
                <input name="password" class="border border-gray-200 rounded-lg p-3 focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-white shadow-sm placeholder-gray-400 mb-3" id="password" placeholder="Masukkan password" required type="password" />
            </div>

            <button class="bg-[#1C4D8D] w-full text-white p-3 rounded-lg font-bold shadow-md hover:bg-[#3E679E] transition mt-4" type="submit">
                Login
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>