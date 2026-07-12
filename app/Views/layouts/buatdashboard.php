<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Dashboard</title>

    <script>
        const originalWarn = console.warn;
        console.warn = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) return;
            originalWarn.apply(console, args);
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" href="<?= base_url('images/LogoIcon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.css">
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

        /* TomSelect: hide dropdown arrow */
        .ts-control.no-arrow::after,
        .ts-wrapper.no-arrow .ts-control::after,
        .ts-wrapper .ts-control.no-arrow::after {
            display: none !important;
            content: none !important;
        }
        .ts-wrapper.no-arrow .dropdown-input::before,
        .ts-wrapper .no-arrow .dropdown-input::before {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#F1F1F1] min-h-screen flex flex-col">
    <?php $uri = service('uri'); ?>

    <nav id="dashNav"
        class="fixed top-0 left-0 w-full bg-[#1C4D8D] text-white px-4 md:px-6 py-2 flex justify-between items-center shadow-md z-[49]">
        <img src="<?= base_url('images/awan.webp') ?>" class="w-[150px] md:w-[200px] -my-4">

        <!-- Hamburger button (mobile only) -->
        <button id="dashHamburgerBtn" onclick="toggleDashMobileMenu()" class="md:hidden text-white focus:outline-none p-2">
            <i id="dashHamburgerIcon" class="fa-solid fa-bars text-xl"></i>
        </button>

        <!-- Desktop nav -->
        <div class="hidden md:flex items-center gap-6">
            <!-- Notification Bell -->
            <div x-data="notificationComponent()" x-init="init()" class="relative">
                <button @click="open = !open; if(open) fetchPendingReturns()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex flex-col items-center justify-center py-1">
                    <i class="fa-solid fa-bell text-lg mb-1"></i>
                    <span class="text-[11px] leading-tight">Notifikasi</span>
                    <span x-show="count > 0" x-text="count" x-cloak
                        class="absolute -top-1 -right-3 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md"></span>
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
                                            x-text="(req.devices[0]?.node_sentral && req.devices[0]?.node_sentral.trim() !== '' && req.devices[0]?.node_sentral.trim() !== '-') ? req.devices[0]?.node_sentral : (req.devices[0]?.site_sentral || '-')"></div>
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
                        class="bg-white rounded-lg shadow-xl w-[90%] md:w-[700px] overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                            <h3 class="font-bold text-sm"
                                x-text="selectedType === 'return' ? 'Request Pengembalian' : 'Request Pemasangan'"></h3>
                            <button @click="closeReviewModal()" class="text-white hover:text-gray-300 transition">
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                        </div>
                        <div class="p-6" x-show="selectedReq">
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-4">
                                <div class="grid grid-cols-2 gap-4">
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
                            </div>

                            <div class="bg-blue-50 border border-blue-100 rounded-md p-4 mb-6" x-show="selectedType === 'install'">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Arep</label>
                                        <div class="font-semibold text-gray-800"
                                            x-text="selectedReq?.devices[0]?.arep || '-'">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Site
                                            Sentral</label>
                                        <div class="font-semibold text-gray-800"
                                            x-text="selectedReq?.devices[0]?.site_sentral || '-'"></div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Node
                                            Sentral</label>
                                        <div class="font-semibold text-gray-800"
                                            x-text="selectedReq?.devices[0]?.node_sentral || '-'"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="max-h-64 overflow-y-auto mb-6 border border-gray-100 rounded shadow-inner">
                                <table class="w-full text-left text-sm border-collapse">
                                    <thead class="sticky top-0 bg-white shadow-sm z-10">
                                        <tr>
                                            <th class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2 pt-2 bg-white">
                                                Action</th>
                                            <th class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2 pt-2 bg-white">No
                                                Registrasi</th>
                                            <th
                                                class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2 pt-2 pl-4 bg-white">
                                                Nama Perangkat</th>
                                            <th
                                                class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pb-2 pt-2 text-center w-12 bg-white">
                                                Qty</th>
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
                                                <td class="align-top py-2 px-2 border-b text-center">
                                                    <div class="font-medium text-gray-700 leading-tight"
                                                        x-text="dev.qty || 1"></div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

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
                    function getCookie(name) {
                        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                        if (match) return decodeURIComponent(match[2]);
                        return '';
                    }

                    window.csrfToken = function () {
                        return getCookie('am_csrf') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    }

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
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': csrfToken()
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
                                fetch('<?= base_url("dashboard/returns") ?>', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
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
                                fetch('<?= base_url("dashboard/installations") ?>', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
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
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': csrfToken()
                                    },
                                    body: params
                                })
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            let msg = res.msg;
                                            if (approvedIds.length === 0 && rejectedIds.length > 0) {
                                                msg = "Pengajuan di tolak";
                                            }
                                            if (typeof showToast === 'function') showToast(msg, 'success');
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
            <div x-data="followUpComponent()" x-init="init()" class="relative">
                <button @click="openModal()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex flex-col items-center justify-center py-1"
                    title="Follow Up">
                    <i class="fa-solid fa-clipboard-list text-lg mb-1"></i>
                    <span class="text-[11px] leading-tight">Follow Up</span>
                    <span x-show="count > 0" x-text="count" x-cloak
                        class="absolute -top-1 -right-3 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md"></span>
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
                                    <!-- Header Actions (Search & Filter) -->
                                    <div class="mb-4 flex gap-2">
                                        <input type="text" x-model="searchQuery"
                                            placeholder="Cari Nama Perangkat, User, Status, No Registrasi..."
                                            class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-xs text-[#1C4D8D] focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                                        <select x-model="durationFilter" class="border border-gray-300 rounded-md px-3 py-2 text-xs text-[#1C4D8D] focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                                            <option value="all">Semua Durasi</option>
                                            <option value="<5">&lt; 5 Days ago</option>
                                            <option value="5-10">5 - 10 Days ago</option>
                                            <option value=">10">&gt; 10 Days ago</option>
                                        </select>
                                    </div>

                                    <!-- View: User Cards -->
                                    <div x-show="!selectedUserView" x-transition>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <template x-for="user in userGroups" :key="user.name">
                                                <div @click="openUserDetail(user.name)" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md cursor-pointer transition flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="bg-blue-100 text-blue-700 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
                                                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-gray-800 text-sm truncate max-w-[140px]" x-text="user.name" :title="user.name"></h4>
                                                            <p class="text-xs text-gray-500"><span x-text="user.devices.length"></span> Perangkat</p>
                                                        </div>
                                                    </div>
                                                    <button @click.stop="showFollowUpAlert({user: user.name})" title="Follow Up User" class="bg-[#1C4D8D] text-white hover:bg-[#2A62AA] transition w-8 h-8 flex items-center justify-center rounded-full text-xs shadow-sm flex-shrink-0">
                                                         <i class="fa-brands fa-whatsapp"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- View: Selected User Devices -->
                                    <div x-show="selectedUserView" x-cloak x-transition>
                                        <div class="mb-4 flex items-center gap-3 bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                            <button @click="closeUserDetail()" class="text-gray-500 hover:text-gray-800 transition flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">
                                                <i class="fa-solid fa-arrow-left"></i> Kembali
                                            </button>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 text-sm">
                                                    Detail: <span x-text="selectedUser" class="text-[#1C4D8D]"></span>
                                                </h4>
                                            </div>
                                            <button @click="showFollowUpAlert({user: selectedUser})" class="bg-[#1C4D8D] text-white hover:bg-[#2A62AA] transition px-3 py-1.5 rounded text-xs font-semibold flex items-center gap-2 shadow-sm">
                                                 <i class="fa-brands fa-whatsapp"></i> Follow Up Semua
                                            </button>
                                        </div>

                                        <table class="w-full text-left text-xs border-collapse bg-white shadow-sm rounded-md overflow-hidden border border-gray-200">
                                            <thead class="bg-gray-100 border-b border-gray-200">
                                                <tr>
                                                    <th class="p-2 font-semibold text-gray-700">No Registrasi</th>
                                                    <th class="p-2 font-semibold text-gray-700">Nama Perangkat</th>
                                                    <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Status</th>
                                                    <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Durasi</th>
                                                    <th class="p-2 font-semibold text-gray-700 text-center whitespace-nowrap">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <template x-for="item in selectedUserDevices" :key="item.noreg">
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="p-2 text-gray-800 font-medium" x-text="item.noreg"></td>
                                                        <td class="p-2 text-gray-800 font-medium" x-text="item.nama_perangkat"></td>
                                                        <td class="p-2 whitespace-nowrap">
                                                            <span class="px-2 py-1 rounded-full text-[10px] font-semibold select-none"
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
                                                                    <i class="fa-regular fa-clock"></i> <span x-text="item.days_ago + ' days ago'"></span>
                                                                </span>
                                                                <span x-show="item.days_ago >= 2" class="relative flex h-2 w-2">
                                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
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
                            durationFilter: 'all',
                            selectedUserView: false,
                            selectedUser: '',
                            get filteredItems() {
                                let result = this.items;

                                if (this.durationFilter !== 'all') {
                                    result = result.filter(item => {
                                        if (this.durationFilter === '<5') return item.days_ago < 5;
                                        if (this.durationFilter === '5-10') return item.days_ago >= 5 && item.days_ago <= 10;
                                        if (this.durationFilter === '>10') return item.days_ago > 10;
                                        return true;
                                    });
                                }

                                if (this.searchQuery !== '') {
                                    const lowerCaseQuery = this.searchQuery.toLowerCase();
                                    result = result.filter(item => {
                                        return (item.user && item.user.toLowerCase().includes(lowerCaseQuery)) ||
                                            (item.nama_perangkat && item.nama_perangkat.toLowerCase().includes(lowerCaseQuery)) ||
                                            (item.status && item.status.toLowerCase().includes(lowerCaseQuery)) ||
                                            (item.noreg && item.noreg.toLowerCase().includes(lowerCaseQuery)) ||
                                            ((item.days_ago + ' days ago').includes(lowerCaseQuery));
                                    });
                                }

                                return result;
                            },
                            get userGroups() {
                                const groups = {};
                                this.filteredItems.forEach(item => {
                                    const userName = item.user && item.user !== '-' ? item.user : 'Unknown User';
                                    if (!groups[userName]) {
                                        groups[userName] = {
                                            name: userName,
                                            devices: []
                                        };
                                    }
                                    groups[userName].devices.push(item);
                                });
                                return Object.values(groups);
                            },
                            get selectedUserDevices() {
                                const group = this.userGroups.find(g => g.name === this.selectedUser);
                                return group ? group.devices : [];
                            },
                            openUserDetail(userName) {
                                this.selectedUser = userName;
                                this.selectedUserView = true;
                            },
                            closeUserDetail() {
                                this.selectedUserView = false;
                                this.selectedUser = '';
                            },
                            init() {
                                this.fetchItems();
                                setInterval(() => {
                                    this.fetchItems();
                                }, 15000);
                            },
                            fetchItems() {
                                fetch('<?= base_url("dashboard/followUpItems") ?>', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
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
                                this.closeUserDetail();
                            },
                            closeModal() {
                                this.modalOpen = false;
                                setTimeout(() => this.closeUserDetail(), 300);
                            },
                            showFollowUpAlert(item) {
                                let userItems = [];
                                if (item.noreg) {
                                    // Single device follow-up
                                    userItems = [item];
                                } else if (item.user && item.user !== '-') {
                                    // Follow Up Semua (only {user: ...} was passed)
                                    userItems = this.items.filter(i => i.user === item.user);
                                } else {
                                    userItems = [item];
                                }

                                // Generate HTML with checkboxes
                                let checkboxHtml = '';
                                if (userItems.length > 1) {
                                    checkboxHtml = `
                                        <div class="mb-3">
                                            <p class="text-xs font-semibold text-gray-500 mb-1">Pilih Perangkat untuk di-follow up:</p>
                                            <div class="space-y-1.5 max-h-36 overflow-y-auto border border-gray-200 rounded p-2 bg-white text-left custom-scrollbar">
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
                                    title: 'Follow Up Perangkat',
                                    html: `
                                        <div class="text-left text-xs space-y-3 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
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
                                                selectedDevices = [{ noreg: (item.noreg || userItems[0].noreg), nama: (item.nama_perangkat || userItems[0].nama_perangkat) }];
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
                                            selectedDevices = [{ noreg: (item.noreg || userItems[0].noreg), nama: (item.nama_perangkat || userItems[0].nama_perangkat) }];
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
                                            selectedDevices = [{ noreg: (item.noreg || userItems[0].noreg), nama: (item.nama_perangkat || userItems[0].nama_perangkat) }];
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

            <!-- Users with Dibawa Component -->
            <div x-data="dibawaComponent()" class="relative">
                <button @click="openModal()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex flex-col items-center justify-center py-1"
                    title="User dengan Perangkat Dibawa">
                    <i class="fa-solid fa-users text-lg mb-1"></i>
                    <span class="text-[11px] leading-tight">Peminjaman</span>
                    <template x-if="users.length > 0">
                        <span class="absolute -top-1 -right-3 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-md"
                            x-text="users.length">
                        </span>
                    </template>
                </button>

                <!-- Users Dibawa Modal -->
                <div x-show="modalOpen" x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.outside="closeModal()"
                        class="bg-white rounded-lg shadow-xl w-[95%] md:w-[700px] overflow-hidden flex flex-col max-h-[80vh]">
                        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                            <h3 class="font-bold text-sm flex items-center gap-2">
                                <i class="fa-solid fa-users"></i> Peminjaman Perangkat
                            </h3>
                            <button @click="closeModal()" class="text-white hover:text-gray-100 transition">
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                        </div>

                        <div class="p-4 flex-1 overflow-y-auto bg-[#F9FBFF]">
                            <template x-if="loading">
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-3 text-[#1C4D8D]"></i>
                                    <p>Memuat data user...</p>
                                </div>
                            </template>
                            <template x-if="!loading && users.length === 0">
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <i class="fa-solid fa-check-circle text-3xl mb-3 text-green-500"></i>
                                    <p>Saat ini tidak ada perangkat yang dibawa user.</p>
                                </div>
                            </template>
                            <template x-if="!loading && users.length > 0">
                                <div>
                                    <!-- View: User Cards -->
                                    <div x-show="!selectedUserView" x-transition>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <template x-for="(user, index) in users" :key="index">
                                                <div @click="openUserDetail(user)" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md cursor-pointer transition flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="bg-blue-100 text-blue-700 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
                                                            <span x-text="user.nama.charAt(0).toUpperCase()"></span>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-gray-800 text-sm truncate max-w-[140px]" x-text="user.nama" :title="user.nama"></h4>
                                                            <p class="text-xs text-gray-500"><span x-text="user.total_dibawa"></span> Perangkat</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- View: Selected User Devices -->
                                    <div x-show="selectedUserView" x-cloak x-transition>
                                        <div class="mb-4 flex items-center gap-3 bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                            <button @click="closeUserDetail()" class="text-gray-500 hover:text-gray-800 transition flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">
                                                <i class="fa-solid fa-arrow-left"></i> Kembali
                                            </button>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 text-sm">
                                                    Detail: <span x-text="selectedUser.nama" class="text-[#1C4D8D]"></span>
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="max-h-64 overflow-y-auto mb-4 border border-gray-100 rounded shadow-inner">
                                            <table class="w-full text-left text-xs border-collapse bg-white shadow-sm rounded-md overflow-hidden border border-gray-200">
                                                <thead class="sticky top-0 bg-gray-100 border-b border-gray-200 z-10">
                                                    <tr>
                                                        <th class="p-2 font-semibold text-gray-700 text-center w-8">
                                                            #
                                                        </th>
                                                        <th class="p-2 font-semibold text-gray-700">No Registrasi</th>
                                                        <th class="p-2 font-semibold text-gray-700">Nama Perangkat</th>
                                                        <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Status</th>
                                                        <th class="p-2 font-semibold text-gray-700 whitespace-nowrap">Tanggal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    <template x-for="(dev, index) in selectedUserDevices" :key="dev.mutasi_id">
                                                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="hover:bg-blue-50 transition">
                                                            <td class="p-2 text-center text-gray-500 font-medium" x-text="index + 1"></td>
                                                            <td class="p-2 text-gray-800 font-medium" x-text="dev.noreg"></td>
                                                            <td class="p-2 text-gray-800 font-medium" x-text="dev.nama"></td>
                                                            <td class="p-2 whitespace-nowrap">
                                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-semibold select-none">Dibawa</span>
                                                            </td>
                                                            <td class="p-2 whitespace-nowrap">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="font-semibold flex items-center gap-1 text-gray-600">
                                                                        <i class="fa-regular fa-clock"></i> <span x-text="dev.created_at"></span>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Actions removed as requested -->
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <script>
                    function dibawaComponent() {
                        return {
                            modalOpen: false,
                            users: [],
                            loading: false,
                            selectedUserView: false,
                            selectedUser: {},
                            selectedUserDevices: [],
                            openUserDetail(user) {
                                this.selectedUser = user;
                                this.selectedUserDevices = user.devices;
                                this.selectedUserView = true;
                            },
                            closeUserDetail() {
                                this.selectedUserView = false;
                                this.selectedUser = {};
                                this.selectedUserDevices = [];
                            },
                            init() {
                                this.fetchUsers();
                                // Poll every 3 minutes
                                setInterval(() => {
                                    if(!this.modalOpen) this.fetchUsers();
                                }, 180000);
                            },
                            openModal() {
                                this.modalOpen = true;
                                this.fetchUsers();
                            },
                            closeModal() {
                                this.modalOpen = false;
                            },
                            fetchUsers() {
                                this.loading = true;
                                fetch('<?= base_url('dashboard/usersDibawa') ?>', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.users = res.data.map(u => ({ ...u, expanded: false }));
                                        }
                                        this.loading = false;
                                    })
                                    .catch(err => {
                                        console.error('Error fetching users:', err);
                                        this.loading = false;
                                    });
                            }
                        }
                    }
                </script>
            </div>

            <!-- BRP (Bukti Request Perangkat) Component -->
            <div x-data="brpComponent()" class="relative">
                <button @click="openModal()"
                    class="text-white hover:text-[#B3B3B3] transition relative flex flex-col items-center justify-center py-1"
                    title="BRP - Bukti Request Perangkat">
                    <i class="fa-solid fa-file-pdf text-lg mb-1"></i>
                    <span class="text-[11px] leading-tight">BRP</span>
                </button>

                <!-- BRP Modal -->
                <div x-show="modalOpen" x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.outside="closeModal()"
                        class="bg-white rounded-lg shadow-xl w-[95%] md:w-[850px] overflow-hidden flex flex-col max-h-[80vh]">
                        <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                            <h3 class="font-bold text-sm">
                                <i class="fa-solid fa-file-pdf mr-1"></i> BRP - Bukti Request Perangkat
                            </h3>
                            <button @click="closeModal()" class="text-white hover:text-gray-300 transition">
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                        </div>

                        <!-- Month/Year Selector -->
                        <div class="px-4 pt-4 pb-2 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <label class="text-xs font-semibold text-gray-600">Periode:</label>
                                <select x-model="selectedMonth" @change="fetchDocuments()"
                                    class="border border-gray-300 rounded-md px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-gray-100 text-gray-700 font-medium cursor-pointer">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <select x-model="selectedYear" @change="fetchDocuments()"
                                    class="border border-gray-300 rounded-md px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#1C4D8D] bg-gray-100 text-gray-700 font-medium cursor-pointer">
                                    <template x-for="y in yearOptions" :key="y">
                                        <option :value="y" x-text="y"></option>
                                    </template>
                                </select>
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="text-xs text-gray-500 font-medium"
                                        x-text="documents.length + ' dokumen'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Documents List -->
                        <div class="p-4 flex-1 overflow-y-auto bg-[#F9FBFF]">
                            <template x-if="loading">
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <i class="fa-solid fa-spinner fa-spin text-2xl mb-3 text-[#1C4D8D]"></i>
                                    <p>Memuat data...</p>
                                </div>
                            </template>
                            <template x-if="!loading && documents.length === 0">
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <i class="fa-regular fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                    <p>Belum ada dokumen BRP untuk periode ini.</p>
                                </div>
                            </template>
                            <template x-if="!loading && documents.length > 0">
                                <div>
                                    <!-- Search -->
                                    <div class="mb-3">
                                        <input type="text" x-model="searchQuery"
                                            placeholder="Cari nama file, user..."
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-xs text-[#1C4D8D] focus:outline-none focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                                    </div>
                                    <table
                                        class="w-full text-left text-xs border-collapse bg-white shadow-sm rounded-md overflow-hidden border border-gray-200">
                                        <thead class="bg-gray-100 border-b border-gray-200">
                                            <tr>
                                                <th class="p-2 font-semibold text-gray-700 text-center w-12">No</th>
                                                <th class="p-2 font-semibold text-gray-700">Nama File</th>
                                                <th class="p-2 font-semibold text-gray-700">User</th>
                                                <th class="p-2 font-semibold text-gray-700 text-center">Nomor</th>
                                                <th class="p-2 font-semibold text-gray-700 text-center">Tanggal</th>
                                                <th class="p-2 font-semibold text-gray-700 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="(doc, index) in filteredDocuments" :key="doc.id">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="p-2 text-center text-gray-600" x-text="index + 1"></td>
                                                    <td class="p-2 text-gray-800 font-medium">
                                                        <div class="flex items-center gap-1.5">
                                                            <i class="fa-solid fa-file-pdf text-red-500"></i>
                                                            <span x-text="doc.filename" class="truncate max-w-[250px]" :title="doc.filename"></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-2 text-gray-600" x-text="doc.user_name"></td>
                                                    <td class="p-2 text-center">
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] font-bold"
                                                            x-text="String(doc.generated_number).padStart(5, '0')"></span>
                                                    </td>
                                                    <td class="p-2 text-center text-gray-500 text-[10px]" x-text="formatDate(doc.created_at)"></td>
                                                    <td class="p-2 text-center">
                                                        <a :href="'<?= base_url('dashboard/brpDownload') ?>/' + doc.id"
                                                            target="_blank" title="Download"
                                                            class="text-[#1C4D8D] hover:text-[#2A62AA] transition text-sm mr-2 inline-flex items-center">
                                                            <i class="fa-solid fa-download"></i>
                                                        </a>
                                                        <?php if ((isset(session('admin')['is_super']) && session('admin')['is_super'] == 1) || session('admin')['username'] === 'admin'): ?>
                                                        <button @click="deleteDoc(doc.id, doc.filename)" title="Delete"
                                                            class="text-red-600 hover:text-red-400 transition text-sm inline-flex items-center">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                        <?php endif; ?>
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
                    function brpComponent() {
                        const now = new Date();
                        return {
                            modalOpen: false,
                            loading: false,
                            documents: [],
                            searchQuery: '',
                            selectedMonth: String(now.getMonth() + 1),
                            selectedYear: String(now.getFullYear()),
                            get yearOptions() {
                                const startYear = 2026;
                                const endYear = startYear + 5;
                                const years = [];
                                for (let y = startYear; y <= endYear; y++) {
                                    years.push(String(y));
                                }
                                return years;
                            },
                            get filteredDocuments() {
                                if (!this.searchQuery) return this.documents;
                                const q = this.searchQuery.toLowerCase();
                                return this.documents.filter(d =>
                                    (d.filename && d.filename.toLowerCase().includes(q)) ||
                                    (d.user_name && d.user_name.toLowerCase().includes(q))
                                );
                            },
                            openModal() {
                                this.modalOpen = true;
                                this.fetchDocuments();
                            },
                            closeModal() {
                                this.modalOpen = false;
                            },
                            fetchDocuments() {
                                this.loading = true;
                                fetch(`<?= base_url('dashboard/brpList') ?>?month=${this.selectedMonth}&year=${this.selectedYear}`)
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.success) {
                                            this.documents = res.data;
                                        } else {
                                            this.documents = [];
                                        }
                                        this.loading = false;
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        this.documents = [];
                                        this.loading = false;
                                    });
                            },
                            deleteDoc(id, filename) {
                                Swal.fire({
                                    title: 'Hapus BRP?',
                                    text: `Yakin ingin menghapus BRP ${filename}?`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Hapus',
                                    cancelButtonText: 'Batal',
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch('<?= base_url('dashboard/brpDelete') ?>/' + id, {
                                            method: 'POST',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(res => {
                                            if (res.success) {
                                                if (typeof showToast === 'function') showToast('BRP berhasil dihapus', 'success');
                                                this.fetchDocuments();
                                            } else {
                                                if (typeof showToast === 'function') showToast(res.msg || res.message || 'Gagal menghapus BRP', 'error');
                                            }
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            if (typeof showToast === 'function') showToast('Terjadi kesalahan saat menghapus', 'error');
                                        });
                                    }
                                });
                            },
                            formatDate(dateStr) {
                                if (!dateStr) return '-';
                                const d = new Date(dateStr);
                                const day = String(d.getDate()).padStart(2, '0');
                                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                const month = monthNames[d.getMonth()];
                                const year = d.getFullYear();
                                const hours = String(d.getHours()).padStart(2, '0');
                                const mins = String(d.getMinutes()).padStart(2, '0');
                                return `${day} ${month} ${year}, ${hours}:${mins}`;
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
                    <button onclick="bukaModalSignature()" @click="open = false"
                        class="w-full text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                        <i class="fa-solid fa-signature mr-2" style="color: #1C4D8D;"></i>
                        Signature
                    </button>
                    <!-- <hr class="mx-3 border-t-1 border-gray-300 my-1"/> -->
                    <?php
                    $adminSess = session()->get('admin');
                    $isSuperAdmin = $adminSess && ((isset($adminSess['is_super']) && $adminSess['is_super'] == 1) || $adminSess['username'] === 'admin');
                    if ($isSuperAdmin):
                        ?>
                        <button onclick="openRegionalManage()" @click="open = false"
                            class="w-full text-left px-4 py-3 text-[#1C4D8D] border-b border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-map-location-dot mr-2" style="color: #1C4D8D;"></i>
                            Regional Manage
                        </button>
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

    <!-- Mobile menu (hidden by default) -->
    <div id="dashMobileMenu" class="fixed left-0 w-full bg-[#1C4D8D] text-white z-[48] shadow-lg hidden md:hidden">
        <div class="flex flex-col divide-y divide-[#2a5fa0]">
            <button type="button" onclick="document.querySelector('[x-data=&quot;notificationComponent()&quot;] button').click(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition text-left">
                <i class="fa-solid fa-bell"></i>
                <span class="text-sm">Notifikasi</span>
            </button>
            <button type="button" onclick="document.querySelector('[x-data=&quot;followUpComponent()&quot;] button').click(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition text-left">
                <i class="fa-solid fa-clipboard-list"></i>
                <span class="text-sm">Follow Up</span>
            </button>
            <button type="button" onclick="document.querySelector('[x-data=&quot;dibawaComponent()&quot;] button').click(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition text-left">
                <i class="fa-solid fa-users"></i>
                <span class="text-sm">Peminjaman</span>
            </button>
            <button type="button" onclick="document.querySelector('[x-data=&quot;brpComponent()&quot;] button').click(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition text-left">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm">BRP</span>
            </button>
            <div class="border-t border-[#2a5fa0]">
                <button type="button" onclick="bukaModalPassword(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-key"></i>
                    <span class="text-sm">Ganti Password</span>
                </button>
                <button type="button" onclick="bukaModalSignature(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-signature"></i>
                    <span class="text-sm">Signature</span>
                </button>
                <?php
                $adminSessMobile = session()->get('admin');
                $isSuperAdminMobile = $adminSessMobile && ((isset($adminSessMobile['is_super']) && $adminSessMobile['is_super'] == 1) || $adminSessMobile['username'] === 'admin');
                if ($isSuperAdminMobile):
                ?>
                <button type="button" onclick="openRegionalManage(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span class="text-sm">Regional Manage</span>
                </button>
                <button type="button" onclick="openUserManage(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-user-gear"></i>
                    <span class="text-sm">User Manage</span>
                </button>
                <button type="button" onclick="openAdminManage(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-user-shield"></i>
                    <span class="text-sm">Admin Manage</span>
                </button>
                <button type="button" onclick="openNodeManage(); toggleDashMobileMenu();" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition w-full text-left">
                    <i class="fa-solid fa-network-wired"></i>
                    <span class="text-sm">Input Node</span>
                </button>
                <?php endif; ?>
                <a href="<?= base_url('logout') ?>" class="flex items-center gap-3 px-6 py-3 hover:bg-[#163d73] border-l-4 border-transparent transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="text-sm">Logout</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleDashMobileMenu() {
            const menu = document.getElementById('dashMobileMenu');
            const icon = document.getElementById('dashHamburgerIcon');
            const nav = document.getElementById('dashNav');
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

    <main class="flex-1 flex flex-col pt-28 pl-6 pr-6">
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

    <!-- SIGNATURE MODAL -->
    <div id="overlaySignature"
        class="fixed inset-0 z-[60] hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-[90%] md:w-[500px] overflow-hidden">
            <div class="flex justify-between items-center bg-[#1C4D8D] text-white px-4 py-3">
                <h3 class="font-bold text-sm">
                    <i class="fa-solid fa-signature mr-1"></i> Tanda Tangan (Signature)
                </h3>
                <button onclick="tutupModalSignature()" class="text-white hover:text-gray-400 transition">
                    <i class="fa-solid fa-xmark fa-xl"></i>
                </button>
            </div>

            <div class="p-5">
                <!-- Current Signature Preview -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanda Tangan Saat Ini</label>
                    <div id="signaturePreviewBox" class="border-2 border-dashed border-gray-300 rounded-lg p-4 min-h-[150px] flex items-center justify-center bg-gray-50 transition-all">
                        <!-- Empty state -->
                        <div id="signatureEmpty" class="text-center">
                            <i class="fa-regular fa-image text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-400">Belum ada tanda tangan</p>
                        </div>
                        <!-- Signature image -->
                        <img id="signatureImg" src="" alt="Tanda Tangan" class="max-h-[140px] max-w-full object-contain hidden">
                    </div>
                </div>

                <!-- Upload Form -->
                <form id="formUploadSignature" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Upload Tanda Tangan Baru</label>
                    <div class="flex items-center gap-3">
                        <label for="signatureFileInput" class="flex-1 cursor-pointer">
                            <div id="signatureDropZone" class="border-2 border-dashed border-[#1C4D8D] rounded-lg p-3 text-center hover:bg-blue-50 transition-all">
                                <i class="fa-solid fa-cloud-arrow-up text-[#1C4D8D] text-lg mb-1"></i>
                                <p id="signatureFileName" class="text-xs text-gray-600">Klik untuk pilih file PNG (maks 2MB)</p>
                            </div>
                            <input type="file" id="signatureFileInput" name="ttd_file" accept="image/png" class="hidden" onchange="previewSignatureFile(this)">
                        </label>
                    </div>
                    <!-- Preview of selected file -->
                    <div id="signatureNewPreview" class="mt-3 hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Preview</label>
                        <div class="border border-gray-200 rounded-lg p-2 bg-white flex items-center justify-center">
                            <img id="signatureNewImg" src="" alt="Preview" class="max-h-[100px] max-w-full object-contain">
                        </div>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center gap-2 mt-4 pt-3 border-t border-gray-200">
                    <button type="button" id="btnDeleteSignature" onclick="hapusSignature()"
                        class="bg-red-500 text-white text-sm px-3 py-2 rounded-md font-semibold shadow hover:bg-red-600 transition hidden">
                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                    </button>
                    <div class="ml-auto">
                        <button type="button" onclick="uploadSignature()"
                            class="bg-[#1C4D8D] text-white text-sm px-4 py-2 rounded-md font-semibold shadow hover:bg-[#3E679E] transition">
                            <i class="fa-solid fa-upload mr-1"></i> Upload
                        </button>
                    </div>
                </div>
            </div>
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
        <?= view('components/usermanage') ?>
        <?= view('components/adminmanage') ?>
        <?= view('components/regionalmanage') ?>
        <?= view('components/nodemanage') ?>
    <?php endif; ?>
    <?= view('components/non_registration_manage') ?>

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

    <!-- SIGNATURE MODAL SCRIPTS -->
    <script>
        const overlaySignature = document.getElementById('overlaySignature');

        function bukaModalSignature() {
            overlaySignature.classList.remove('hidden');
            overlaySignature.classList.add('flex');
            loadCurrentSignature();
        }

        function tutupModalSignature() {
            overlaySignature.classList.add('hidden');
            overlaySignature.classList.remove('flex');
            // Reset file input
            document.getElementById('formUploadSignature').reset();
            document.getElementById('signatureNewPreview').classList.add('hidden');
            document.getElementById('signatureFileName').textContent = 'Klik untuk pilih file PNG (maks 2MB)';
        }

        function loadCurrentSignature() {
            const img = document.getElementById('signatureImg');
            const empty = document.getElementById('signatureEmpty');
            const btnDelete = document.getElementById('btnDeleteSignature');

            fetch('<?= base_url('dashboard/getMySignature') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (res.ok) return res.blob();
                throw new Error('No signature');
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                img.src = url;
                img.classList.remove('hidden');
                empty.classList.add('hidden');
                btnDelete.classList.remove('hidden');
            })
            .catch(() => {
                img.classList.add('hidden');
                img.src = '';
                empty.classList.remove('hidden');
                btnDelete.classList.add('hidden');
            });
        }

        function previewSignatureFile(input) {
            const preview = document.getElementById('signatureNewPreview');
            const previewImg = document.getElementById('signatureNewImg');
            const fileName = document.getElementById('signatureFileName');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.type !== 'image/png') {
                    Swal.fire({ icon: 'error', title: 'Format Salah', text: 'File harus berformat PNG.', confirmButtonColor: '#1C4D8D' });
                    input.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: 'Ukuran file maksimal 2MB.', confirmButtonColor: '#1C4D8D' });
                    input.value = '';
                    return;
                }

                fileName.textContent = file.name;

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
                fileName.textContent = 'Klik untuk pilih file PNG (maks 2MB)';
            }
        }

        function uploadSignature() {
            const fileInput = document.getElementById('signatureFileInput');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({ icon: 'warning', title: 'Pilih File', text: 'Pilih file PNG terlebih dahulu.', confirmButtonColor: '#1C4D8D' });
                return;
            }

            const formData = new FormData();
            formData.append('ttd_file', fileInput.files[0]);

            fetch('<?= base_url('dashboard/uploadMySignature') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, timer: 1500, showConfirmButton: false });
                    // Reset form and reload preview
                    document.getElementById('formUploadSignature').reset();
                    document.getElementById('signatureNewPreview').classList.add('hidden');
                    document.getElementById('signatureFileName').textContent = 'Klik untuk pilih file PNG (maks 2MB)';
                    loadCurrentSignature();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg, confirmButtonColor: '#1C4D8D' });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengupload.', confirmButtonColor: '#1C4D8D' });
            });
        }

        function hapusSignature() {
            Swal.fire({
                title: 'Hapus Tanda Tangan?',
                text: 'Tanda tangan akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?= base_url('dashboard/deleteMySignature') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken()
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, timer: 1500, showConfirmButton: false });
                            loadCurrentSignature();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg, confirmButtonColor: '#1C4D8D' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.', confirmButtonColor: '#1C4D8D' });
                    });
                }
            });
        }

        // Close signature modal when clicking outside
        overlaySignature.addEventListener('click', function(e) {
            if (e.target === overlaySignature) {
                tutupModalSignature();
            }
        });
    </script>
    <?= view('components/global_scripts') ?>
</body>

</html>
