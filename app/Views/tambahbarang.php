<?= $this->extend('layouts/buatdashboard') ?>

<?= $this->section('content') ?>
<div class="p-6 bg-white rounded-xl shadow max-w-2xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-6 text-gray-800">Form Tambah Perangkat Baru</h2>
    
    <form action="<?= base_url('dashboard/save') ?>" method="POST">
        <?= csrf_field() ?> <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">No Registrasi</label>
            <input type="text" name="noreg" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
            <input type="text" name="nama" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Serial Number</label>
            <input type="text" name="serial_number" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?= base_url('dashboard') ?>" class="px-4 py-2 bg-gray-200 rounded-md">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan Perangkat</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
