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

    <nav class="fixed w-full bg-[#1C4D8D] text-white px-8 py-2 flex justify-between items-center shadow-md">
        <h1 class="text-3xl font-extrabold tracking-wider">AWan</h1>

        <div class="flex gap-8 items-center">
            <a href="<?= base_url('logout') ?>" class="flex flex-col items-center cursor-pointer transition group
                <?= $uri->getSegment(1) == 'logout' ? 'text-[#7FB3D5] font-semibold border-b-2 border-[#7FB3D5]' : 'hover:text-[#7FB3D5]' ?>">
                <i class="fa-solid fa-arrow-right-from-bracket text-xl mb-1"></i>
                <span class="text-sm">Logout</span>
            </a>
        </div>
    </nav>

    <main class="flex-1 pt-24">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="mt-auto text-xs text-[#1C4D8D] p-2 text-center">
        &copy; <?= date('Y') ?> anak baik
    </footer>

    <?= $this->renderSection('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
</body>

</html>