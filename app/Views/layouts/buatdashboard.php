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

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#F1F1F1] h-screen flex flex-col overflow-hidden">
    <?php $uri = service('uri'); ?>

    <nav
        class="fixed top-0 left-0 w-full bg-[#1C4D8D] text-white px-6 py-3 flex justify-between items-center shadow-md z-[49]">
        <img src="<?= base_url('images/awan.png') ?>" width="150px">
        <div class="flex items-center gap-6">
            <!-- Notification Bell -->
            <div x-data="notificationComponent()" x-init="init()" class="relative mt-1">
                <button @click="open = !open; if(open) fetchPendingReturns()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex items-center justify-center">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <span x-show="count > 0" x-text="count" x-cloak
                        class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md"></span>
                </button>

                <div x-show="open" x-cloak @click.outside="open = false"
                    class="absolute right-0 mt-6 w-80 bg-white text-black rounded-lg shadow-2xl overflow-hidden z-50 border border-gray-200">
                    <div
                        class="bg-[#1C4D8D] text-white px-4 py-3 font-bold text-sm flex justify-between items-center shadow-inner">
                        <span>Notifikasi</span>
                        <span x-show="count > 0" x-text="count + ' Baru'"
                            class="bg-red-500 px-2 py-0.5 rounded-full text-[10px]"></span>
                    </div>

                    <!-- Notification Tabs -->
                    <div class="flex text-xs font-semibold text-center border-b border-gray-200 bg-gray-50">
                        <button @click="activeTab = 'return'"
                            :class="{'text-[#1C4D8D] border-b-2 border-[#1C4D8D]': activeTab === 'return', 'text-gray-500 hover:text-[#1C4D8D]': activeTab !== 'return'}"
                            class="flex-1 py-2 transition-all">
                            Pengembalian <span x-show="returnCount > 0" x-text="returnCount"
                                class="ml-1 bg-red-500 text-white px-1.5 py-0.5 rounded-full text-[9px]"></span>
                        </button>
                        <button @click="activeTab = 'install'"
                            :class="{'text-[#1C4D8D] border-b-2 border-[#1C4D8D]': activeTab === 'install', 'text-gray-500 hover:text-[#1C4D8D]': activeTab !== 'install'}"
                            class="flex-1 py-2 transition-all">
                            Pemasangan <span x-show="installCount > 0" x-text="installCount"
                                class="ml-1 bg-red-500 text-white px-1.5 py-0.5 rounded-full text-[9px]"></span>
                        </button>
                    </div>

                    <!-- Slider Container -->
                    <div class="overflow-hidden w-full relative">
                        <div class="flex transition-transform duration-300 ease-in-out w-[200%]"
                            :style="activeTab === 'return' ? 'transform: translateX(0%)' : 'transform: translateX(-50%)'">

                            <!-- Pengembalian List -->
                            <div class="w-1/2 flex-shrink-0 max-h-[290px] overflow-y-auto divide-y divide-gray-100">
                                <template x-if="returnRequests.length === 0">
                                    <div class="p-6 text-center text-gray-500 text-sm">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                                        <p>Tidak ada request pengembalian.</p>
                                    </div>
                                </template>
                                <template x-for="req in returnRequests" :key="req.group_id">
                                    <div @click="openReviewModal(req, 'return')"
                                        class="p-4 hover:bg-[#F9FBFF] cursor-pointer transition-colors border-l-4 border-transparent hover:border-[#1C4D8D]">
                                        <div class="flex justify-between items-start mb-1">
                                            <div class="font-bold text-sm text-[#1C4D8D]">
                                                <i class="fa-solid fa-user-circle mr-1 opacity-70"></i> <span
                                                    x-text="req.nama_user || '-'"></span>
                                            </div>
                                            <span x-show="!req.is_read"
                                                class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm">NEW</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 font-medium" x-text="req.created_at">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Pemasangan List -->
                            <div class="w-1/2 flex-shrink-0 max-h-[290px] overflow-y-auto divide-y divide-gray-100">
                                <template x-if="installRequests.length === 0">
                                    <div class="p-6 text-center text-gray-500 text-sm">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                                        <p>Tidak ada request pemasangan.</p>
                                    </div>
                                </template>
                                <template x-for="req in installRequests" :key="req.group_id">
                                    <div @click="openReviewModal(req, 'install')"
                                        class="p-4 hover:bg-[#F9FBFF] cursor-pointer transition-colors border-l-4 border-transparent hover:border-[#1C4D8D]">
                                        <div class="flex justify-between items-start mb-1">
                                            <div class="font-bold text-sm text-[#1C4D8D]">
                                                <i class="fa-solid fa-user-circle mr-1 opacity-70"></i> <span
                                                    x-text="req.nama_user || '-'"></span>
                                            </div>
                                            <span x-show="!req.is_read"
                                                class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm">NEW</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 font-medium mb-1" x-text="req.created_at">
                                        </div>
                                        <div class="text-[10px] text-[#1C4D8D] font-semibold bg-blue-50 inline-block px-1.5 py-0.5 rounded"
                                            x-text="req.devices[0]?.node_sentral || '-'"></div>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Review Modal -->
                <div x-show="reviewOpen" x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.outside="closeReviewModal()"
                        class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                            <h3 class="font-bold text-sm"
                                x-text="selectedType === 'return' ? 'Request Pengembalian' : 'Request Pemasangan'"></h3>
                            <button @click="closeReviewModal()" class="text-white hover:text-gray-300 transition">
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                        </div>
                        <div class="p-6" x-show="selectedReq">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label
                                        class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">User</label>
                                    <div class="font-semibold text-gray-800" x-text="selectedReq?.nama_user || '-'">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Tanggal
                                        Request</label>
                                    <div class="font-semibold text-gray-700" x-text="selectedReq?.created_at"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-6" x-show="selectedType === 'install'">
                                <div>
                                    <label
                                        class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Arep</label>
                                    <div class="font-semibold text-gray-800"
                                        x-text="selectedReq?.devices[0]?.arep || '-'">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Node
                                        Sentral</label>
                                    <div class="font-semibold text-gray-800"
                                        x-text="selectedReq?.devices[0]?.node_sentral || '-'"></div>
                                </div>
                            </div>
                            <table class="w-full text-left text-sm mb-6 border-collapse">
                                <thead>
                                    <tr>
                                        <th class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2">
                                            Action</th>
                                        <th class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2">No
                                            Registrasi</th>
                                        <th
                                            class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2 pl-4">
                                            Nama Perangkat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(dev, index) in selectedReq?.devices" :key="index">
                                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                                            <td class="align-top py-2 px-2 border-b text-center">
                                                <input type="checkbox" class="w-3 h-3 cursor-pointer accent-[#1C4D8D]"
                                                    :value="dev.request_id" x-model="checkedDevices">
                                            </td>
                                            <td class="align-top py-2 px-2 border-b">
                                                <div class="font-medium text-gray-700" x-text="dev.noreg"></div>
                                            </td>
                                            <td class="align-top py-2 px-2 border-b pl-4">
                                                <div class="font-medium text-gray-700 leading-tight"
                                                    x-text="dev.nama_perangkat"></div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            <div class="border-t border-gray-200 pt-4 flex justify-end gap-2 mt-2">
                                <button @click="refuseAll()"
                                    class="bg-red-500 text-white px-4 py-2 rounded-md shadow hover:bg-red-600 transition font-semibold text-sm">
                                    <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                </button>
                                <button @click="approveSelected()"
                                    class="bg-green-500 text-white px-6 py-2 rounded-md shadow hover:bg-green-600 transition font-semibold text-sm">
                                    <i class="fa-solid fa-check mr-1"></i> Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function notificationComponent() {
                        return {
                            open: false,
                            reviewOpen: false,
                            activeTab: 'return',
                            selectedReq: null,
                            selectedType: null, // 'return' or 'install'
                            checkedDevices: [],
                            returnRequests: [],
                            installRequests: [],
                            returnCount: 0,
                            installCount: 0,
                            count: 0,
                            init() {
                                this.fetchPendingReturns();
                                this.fetchPendingInstalls();
                                setInterval(() => {
                                    this.fetchPendingReturns();
                                    this.fetchPendingInstalls();
                                }, 15000);
                            },
                            openReviewModal(req, type) {
                                this.selectedReq = req;
                                this.selectedType = type;
                                this.checkedDevices = req.devices.map(d => d.request_id.toString());
                                this.reviewOpen = true;
                                this.open = false;

                                if (!req.is_read) {
                                    this.markAsRead(req, type);
                                }
                            },
                            markAsRead(req, type) {
                                req.is_read = true;
                                if (type === 'return') {
                                    this.returnCount = this.returnRequests.filter(r => !r.is_read).length;
                                } else {
                                    this.installCount = this.installRequests.filter(r => !r.is_read).length;
                                }
                                this.count = this.returnCount + this.installCount;

                                let params = new URLSearchParams();
                                req.devices.forEach(d => {
                                    params.append('request_ids[]', d.request_id);
                                });

                                const url = type === 'return' ? 'dashboard/returns/mark-read' : 'dashboard/installations/mark-read';

                                fetch(`<?= base_url() ?>${url}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: params
                                }).catch(err => console.error(err));
                            },
                            closeReviewModal() {
                                this.reviewOpen = false;
                                this.selectedReq = null;
                                this.checkedDevices = [];
                                this.selectedType = null;
                            },
                            approveSelected() {
                                if (this.selectedReq && this.selectedReq.devices) {
                                    const approvedIds = this.checkedDevices;
                                    const rejectedIds = this.selectedReq.devices
                                        .filter(d => !this.checkedDevices.includes(d.request_id.toString()))
                                        .map(d => d.request_id.toString());

                                    this.approveReq(approvedIds, rejectedIds, this.selectedType);
                                }
                            },
                            refuseAll() {
                                if (this.selectedReq && this.selectedReq.devices) {
                                    const approvedIds = [];
                                    const rejectedIds = this.selectedReq.devices.map(d => d.request_id.toString());

                                    this.approveReq(approvedIds, rejectedIds, this.selectedType);
                                }
                            },
                            fetchPendingReturns() {
                                fetch('<?= base_url("dashboard/returns") ?>')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.returnRequests = res.data;
                                            this.returnCount = res.data.filter(r => !r.is_read).length;
                                            this.count = this.returnCount + this.installCount;
                                        }
                                    }).catch(err => console.error(err));
                            },
                            fetchPendingInstalls() {
                                fetch('<?= base_url("dashboard/installations") ?>')
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.installRequests = res.data;
                                            this.installCount = res.data.filter(r => !r.is_read).length;
                                            this.count = this.returnCount + this.installCount;
                                        }
                                    }).catch(err => console.error(err));
                            },
                            approveReq(approvedIds, rejectedIds, type) {
                                let params = new URLSearchParams();
                                approvedIds.forEach(id => {
                                    params.append('approved_ids[]', id);
                                });
                                rejectedIds.forEach(id => {
                                    params.append('rejected_ids[]', id);
                                });

                                const url = type === 'return' ? 'dashboard/returns/approve' : 'dashboard/installations/approve';

                                fetch(`<?= base_url() ?>${url}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: params
                                })
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            if (typeof showToast === 'function') showToast(res.msg, 'success');
                                            this.closeReviewModal();
                                            if (type === 'return') this.fetchPendingReturns();
                                            else this.fetchPendingInstalls();
                                            setTimeout(() => window.location.reload(), 1500);
                                        } else {
                                            if (typeof showToast === 'function') showToast(res.msg, 'error');
                                            else alert(res.msg);
                                        }
                                    });
                            }
                        }
                    }
                </script>
            </div>

            <!-- Follow Up Component -->
            <div x-data="followUpComponent()" x-init="init()" class="relative mt-1">
                <button @click="openModal()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex items-center justify-center mr-1"
                    title="Follow Up">
                    <i class="fa-solid fa-clipboard-list text-xl"></i>
                    <span x-show="count > 0" x-text="count" x-cloak
                        class="absolute -top-1.5 -right-2 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md"></span>
                </button>

                <!-- Follow Up Modal -->
                <div x-show="modalOpen" x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
                    <div
                        class="bg-white rounded-lg shadow-xl w-[95%] md:w-[850px] overflow-hidden flex flex-col max-h-[80vh]">
                        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                            <h3 class="font-bold text-sm">Follow Up Perangkat</h3>
                            <button @click="closeModal()" class="text-white hover:text-gray-300 transition">
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                        </div>
                        <div class="p-4 flex-1 overflow-y-auto bg-[#F9FBFF]">
                            <template x-if="items.length === 0">
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <i class="fa-solid fa-check-circle text-3xl mb-3 text-green-500"></i>
                                    <p>Semua perangkat aman. Tidak ada yang perlu di-follow up.</p>
                                </div>
                            </template>
                            <template x-if="items.length > 0">
                                <div>
                                    <div class="mb-3">
                                        <input type="text" x-model="searchQuery"
                                            placeholder="Cari Nama Perangkat, User, Status, No Registrasi..."
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-xs text-[#1C4D8D] focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                                    </div>
                                    <table
                                        class="w-full text-left text-xs border-collapse bg-white shadow-sm rounded-md overflow-hidden border border-gray-200">
                                        <thead class="bg-gray-100 border-b border-gray-200">
                                            <tr>
                                                <th class="p-2 font-semibold text-gray-700">No Registrasi</th>
                                                <th class="p-2 font-semibold text-gray-700">Nama Perangkat</th>
                                                <th class="p-2 font-semibold text-gray-700">User</th>
                                                <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Status
                                                </th>
                                                <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Durasi
                                                </th>
                                                <th
                                                    class="p-2 font-semibold text-gray-700 text-center whitespace-nowrap">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="item in filteredItems" :key="item.noreg">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="p-2 text-gray-800 font-medium" x-text="item.noreg"></td>
                                                    <td class="p-2 text-gray-800 font-medium"
                                                        x-text="item.nama_perangkat">
                                                    </td>
                                                    <td class="p-2 text-gray-600" x-text="item.user"></td>
                                                    <td class="p-2 whitespace-nowrap">
                                                        <span
                                                            class="px-2 py-1 rounded-full text-[10px] font-semibold select-none"
                                                            :class="{
                                                            'bg-yellow-100 text-yellow-800': item.status === 'Dibawa',
                                                            'bg-orange-100 text-orange-800': item.status === 'Crosscheck Intan'
                                                        }" x-text="item.status"></span>
                                                    </td>
                                                    <td class="p-2 whitespace-nowrap">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-semibold flex items-center gap-1" :class="{
                                                                'text-yellow-600': item.days_ago >= 2 && item.days_ago <= 3,
                                                                'text-red-600': item.days_ago > 3,
                                                                'text-gray-600': item.days_ago < 2
                                                            }">
                                                                <i class="fa-regular fa-clock"></i> <span
                                                                    x-text="item.days_ago + ' days ago'"></span>
                                                            </span>
                                                            <span x-show="item.days_ago >= 2"
                                                                class="relative flex h-2 w-2">
                                                                <span
                                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                                                    :class="item.days_ago > 3 ? 'bg-red-500' : 'bg-yellow-500'"></span>
                                                                <span class="relative inline-flex rounded-full h-2 w-2"
                                                                    :class="item.days_ago > 3 ? 'bg-red-500' : 'bg-yellow-500'"></span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="p-2 text-center whitespace-nowrap">
                                                        <button @click="showFollowUpAlert(item)"
                                                            class="bg-[#1C4D8D] text-white hover:bg-[#2A62AA] transition px-2.5 py-1 rounded text-[10px] font-semibold flex items-center gap-1 mx-auto shadow-sm">
                                                            <i class="fa-solid fa-paper-plane"></i> Follow Up
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <script>
                    function followUpComponent() {
                        return {
                            modalOpen: false,
                            count: 0,
                            items: [],
                            searchQuery: '',
                            get filteredItems() {
                                if (this.searchQuery === '') {
                                    return this.items;
                                }
                                const lowerCaseQuery = this.searchQuery.toLowerCase();
                                return this.items.filter(item => {
                                    return (item.user && item.user.toLowerCase().includes(lowerCaseQuery)) ||
                                        (item.nama_perangkat && item.nama_perangkat.toLowerCase().includes(lowerCaseQuery)) ||
                                        (item.status && item.status.toLowerCase().includes(lowerCaseQuery)) ||
                                        (item.noreg && item.noreg.toLowerCase().includes(lowerCaseQuery)) ||
                                        ((item.days_ago + ' days ago').includes(lowerCaseQuery));
                                });
                            },
                            init() {
                                this.fetchItems();
                                setInterval(() => {
                                    this.fetchItems();
                                }, 15000);
                            },
                            fetchItems() {
                                fetch('<?= base_url("dashboard/followUpItems") ?>')
                                    .then(res => res.json())
                                    .then(data => {
                                        this.items = data;
                                        this.count = data.length;
                                    })
                                    .catch(err => console.error(err));
                            },
                            openModal() {
                                this.fetchItems();
                                this.modalOpen = true;
                            },
                            closeModal() {
                                this.modalOpen = false;
                            },
                            showFollowUpAlert(item) {
                                // Find all pending items belonging to the same user
                                const userItems = (item.user && item.user !== '-')
                                    ? this.items.filter(i => i.user === item.user)
                                    : [item];

                                // Generate HTML with checkboxes
                                let checkboxHtml = '';
                                if (userItems.length > 1) {
                                    checkboxHtml = `
                                        <div class="mb-3">
                                            <p class="text-xs font-semibold text-gray-500 mb-1">Pilih Perangkat untuk di-follow up:</p>
                                            <div class="space-y-1.5 max-h-36 overflow-y-auto border border-gray-200 rounded p-2 bg-white text-left">
                                                ${userItems.map(ui => `
                                                    <label class="flex items-start gap-2 cursor-pointer p-1 text-xs hover:bg-gray-50 rounded">
                                                        <input type="checkbox" class="mt-0.5 swal-device-checkbox accent-[#1C4D8D]" data-noreg="${ui.noreg}" data-nama="${ui.nama_perangkat}" value="${ui.noreg}" checked>
                                                        <span class="text-gray-700 font-medium"><strong>${ui.noreg}</strong> - ${ui.nama_perangkat}</span>
                                                    </label>
                                                `).join('')}
                                            </div>
                                        </div>
                                    `;
                                }

                                Swal.fire({
                                    title: 'Kirim Pengingat Follow Up',
                                    html: `
                                        <div class="text-left text-xs space-y-3">
                                            ${checkboxHtml}
                                            
                                            <div id="swal-message-preview" class="bg-gray-50 border border-gray-200 rounded p-3 font-mono text-gray-700 select-all relative group break-words leading-relaxed whitespace-pre-wrap"></div>
                                        </div>
                                    `,
                                    icon: 'info',
                                    showCancelButton: true,
                                    confirmButtonText: '<i class="fa-brands fa-whatsapp mr-1"></i> Hubungi via WA',
                                    cancelButtonText: '<i class="fa-regular fa-copy mr-1"></i> Salin Pesan',
                                    confirmButtonColor: '#25D366',
                                    cancelButtonColor: '#4F46E5',
                                    reverseButtons: true,
                                    didOpen: () => {
                                        const updatePreviewText = () => {
                                            let selectedDevices = [];
                                            if (userItems.length > 1) {
                                                const checkedBoxes = document.querySelectorAll('.swal-device-checkbox:checked');
                                                selectedDevices = Array.from(checkedBoxes).map(cb => ({
                                                    noreg: cb.getAttribute('data-noreg'),
                                                    nama: cb.getAttribute('data-nama')
                                                }));
                                            } else {
                                                selectedDevices = [{ noreg: item.noreg, nama: item.nama_perangkat }];
                                            }

                                            let message = '';
                                            if (selectedDevices.length === 0) {
                                                message = `Halo ${item.user}, silakan pilih perangkat yang ingin di-follow up.`;
                                            } else {
                                                const listStr = selectedDevices.map(d => `• ${d.noreg} - ${d.nama}`).join('\n');
                                                message = `Rekan ${item.user}, mohon update untuk status perangkat berikut:\n\n${listStr}\n\nTotal Perangkat: ${selectedDevices.length} Unit\n\nTerima kasih.`;
                                            }

                                            const previewDiv = document.getElementById('swal-message-preview');
                                            if (previewDiv) {
                                                previewDiv.innerText = message;
                                            }
                                            return message;
                                        };

                                        // Initial call
                                        updatePreviewText();

                                        // Bind changes
                                        const checkboxes = document.querySelectorAll('.swal-device-checkbox');
                                        checkboxes.forEach(cb => {
                                            cb.addEventListener('change', updatePreviewText);
                                        });
                                    },
                                    preConfirm: () => {
                                        let selectedDevices = [];
                                        if (userItems.length > 1) {
                                            const checkedBoxes = document.querySelectorAll('.swal-device-checkbox:checked');
                                            selectedDevices = Array.from(checkedBoxes).map(cb => ({
                                                noreg: cb.getAttribute('data-noreg'),
                                                nama: cb.getAttribute('data-nama')
                                            }));
                                        } else {
                                            selectedDevices = [{ noreg: item.noreg, nama: item.nama_perangkat }];
                                        }

                                        let message = '';
                                        if (selectedDevices.length === 0) {
                                            message = `Halo ${item.user}, silakan pilih perangkat yang ingin di-follow up.`;
                                        } else {
                                            const listStr = selectedDevices.map(d => `• ${d.noreg} - ${d.nama}`).join('\n');
                                            message = `Rekan ${item.user}, mohon update untuk status perangkat berikut:\n\n${listStr}\n\nTotal Perangkat: ${selectedDevices.length} Unit\n\nTerima kasih.`;
                                        }
                                        return message;
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        const finalMsg = result.value;
                                        const finalWaUrl = `https://wa.me/?text=${encodeURIComponent(finalMsg)}`;
                                        window.open(finalWaUrl, '_blank');
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        let selectedDevices = [];
                                        if (userItems.length > 1) {
                                            const checkedBoxes = document.querySelectorAll('.swal-device-checkbox:checked');
                                            selectedDevices = Array.from(checkedBoxes).map(cb => ({
                                                noreg: cb.getAttribute('data-noreg'),
                                                nama: cb.getAttribute('data-nama')
                                            }));
                                        } else {
                                            selectedDevices = [{ noreg: item.noreg, nama: item.nama_perangkat }];
                                        }

                                        let finalMsg = '';
                                        if (selectedDevices.length === 0) {
                                            finalMsg = `Halo ${item.user}, silakan pilih perangkat yang ingin di-follow up.`;
                                        } else {
                                            const listStr = selectedDevices.map(d => `• ${d.noreg} - ${d.nama}`).join('\n');
                                            finalMsg = `Rekan ${item.user}, mohon update untuk status perangkat berikut:\n\n${listStr}\n\nTotal Perangkat: ${selectedDevices.length} Unit\n\nTerima kasih.`;
                                        }

                                        navigator.clipboard.writeText(finalMsg).then(() => {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Disalin!',
                                                text: 'Pesan pengingat berhasil disalin ke clipboard.',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        }).catch(err => {
                                            console.error('Gagal menyalin text: ', err);
                                        });
                                    }
                                });
                            }
                        }
                    }
                </script>
            </div>

            <div x-data="{open:false}" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 cursor-pointer transition group text-white hover:text-[#B3B3B3]">
                    <i class="fa-regular fa-circle-user text-xl"></i>
                    <span class="text-sm font-medium">
                        <?= session('admin')['username'] ?? 'admin' ?>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{'rotate-180' : open}"></i>
                </button>

                <div x-show="open" x-cloak x-transition @click.outside="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white text-black rounded-md shadow-2xl text-sm">

                    <button onclick="bukaModalPassword()" @click="open = false"
                        class="w-full rounded-t-md text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                        <i class="fa-solid fa-key mr-2" style="color: #1C4D8D;"></i>
                        Ganti Password
                    </button>
                    <!-- <hr class="mx-3 border-t-1 border-gray-300 my-1"/> -->
                    <?php
                    $adminSess = session()->get('admin');
                    $isSuperAdmin = $adminSess && ((isset($adminSess['is_super']) && $adminSess['is_super'] == 1) || $adminSess['username'] === 'admin');
                    if ($isSuperAdmin):
                        ?>
                        <button onclick="openUserManage()" @click="open = false"
                            class="w-full text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-user-gear mr-2" style="color: #1C4D8D;"></i>
                            User Manage
                        </button>
                        <button onclick="openAdminManage()" @click="open = false"
                            class="w-full text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-user-shield mr-2" style="color: #1C4D8D;"></i>
                            Admin Manage
                        </button>
                        <button onclick="openNodeManage()" @click="open = false"
                            class="w-full text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-network-wired mr-2" style="color: #1C4D8D;"></i>
                            Input Node
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('logout') ?>"
                        class="rounded-b-md block px-4 py-3 text-[#1C4D8D] hover:bg-gray-200">
                        <i class="fa-solid fa-right-from-bracket mr-2" style="color: #1C4D8D;"></i>
                        Logout
                    </a>
                    <!-- <a href="<?= base_url('admin-manage') ?>" @click="open = false" class="block px-4 py-2 hover:bg-gray-100">
                    Admin Manage
                </a> -->
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 pt-28 pl-6 pr-6">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- PASSWORD MODAL -->
    <div id="overlayPassword"
        class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">
            <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                <h3 class="font-bold">Ganti Password</h3>

                <button onclick="tutupModalPassword()" class="text-white hover:text-gray-400 transition">
                    <i class="fa-solid fa-xmark fa-xl"></i>
                </button>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 text-red-700 p-2 mb-4 text-sm rounded">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form id="gantiPassword" action="<?= base_url('update-password') ?>" method="post"
                class="p-4 flex flex-col justify-between">
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
        Unreleased &bull; PT. Aplikanusa Lintasarta &copy; <?= date('Y') ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <?= $this->renderSection('scripts') ?>
    <?php
    $adminSess = session()->get('admin');
    $isSuperAdmin = $adminSess && ((isset($adminSess['is_super']) && $adminSess['is_super'] == 1) || $adminSess['username'] === 'admin');
    if ($isSuperAdmin):
        ?>
        <?= view('components/adminmanage') ?>
        <?= view('components/nodemanage') ?>
    <?php endif; ?>

    <script>
        const overlay = document.getElementById('overlayPassword');

        function bukaModalPassword() {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            localStorage.setItem('showModal', 'true');
        }

        function tutupModalPassword() {
            document.getElementById('overlayPassword').classList.add('hidden');

            document.getElementById('gantiPassword').reset();

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
        }

        document.addEventListener("DOMContentLoaded", function () {
            <?php if (session()->getFlashdata('openModal') || session()->getFlashdata('show_modal')): ?>
                bukaModalPassword();
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '<?= esc(session()->getFlashdata('success')) ?>',
                    confirmButtonColor: '#1C4D8D'
                });
            <?php endif; ?>

            <?php if (session()->getFlashdata('error') && !session()->getFlashdata('openModal') && !session()->getFlashdata('show_modal')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: '<?= esc(session()->getFlashdata('error')) ?>',
                    confirmButtonColor: '#1C4D8D'
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>