<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use App\Models\UserModel;
use App\Models\BrpModel;
use Config\Database;
use Config\Services;

class DashboardController extends BaseController
{
    protected $perangkatModel;
    protected $mutasiModel;
    protected $userModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->mutasiModel = new MutasiModel();
        $this->userModel = new UserModel();
        // Load password helper untuk Argon2ID hashing
        helper('password');
    }

    public function dashboard()
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $filters = [
            'keyword' => $this->request->getGet('keyword'),
            'status' => $this->request->getGet('status'),
            'filter_mutasi' => $this->request->getGet('filter_mutasi'),
            'user' => $this->request->getGet('user'),
            'sort_by' => $this->request->getGet('sort_by'),
            'sort_dir' => $this->request->getGet('sort_dir'),
        ];

        $result = $this->perangkatModel->getDataDash($filters, $limit, $offset);

        $data['perangkat'] = $result['data'];
        $totalData = $result['total'];
        $data['currentPage'] = $page;
        $data['limit'] = $limit;
        $data['totalPage'] = ceil($totalData / $limit);

        $configMutasi = new \Config\Mutasi();
        $data['statuses'] = $configMutasi->status;

        $userModel = new \App\Models\UserModel();
        $data['users'] = $userModel->orderBy('nama', 'ASC')->findAll();

        return view('dashboard', $data);
    }

    public function getHistory($id)
    {
        $model = new \App\Models\MutasiModel();

        $page = $this->request->getVar('page') ?? 1;
        $search = $this->request->getVar('searchHistory') ?? '';

        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchHistory' => $search
        ];

        $result = $model->getDataHistory($id, $filters, $limit, $offset);

        $total = $result['total'];
        $totalPage = ceil($total / $limit);

        return $this->response->setJSON([
            'data' => $result['data'],
            'total' => $total,
            'totalPage' => $totalPage,
            'currentPage' => (int) $page
        ]);
    }

    public function checkMutasi($id)
    {
        $mutasi = $this->mutasiModel->find($id);

        if (!$mutasi || !in_array($mutasi['status'], ['Terpasang', 'Terkirim'])) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->mutasiModel->update($id, [
            'is_checked' => 1,
            'checked_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function simpanPerangkat()
    {
        $idSpecInput = $this->request->getPost('id_spec');
        $namaInput = $this->request->getPost('nama');
        $kodeId = $this->request->getPost('kode_id');

        $data = [
            'kode_id' => $kodeId,
            'status' => 'Tersedia',
        ];

        if (is_numeric($idSpecInput)) {
            $data['id_spec'] = (int) $idSpecInput;
            $data['nama'] = $namaInput;
        } else {
            $data['id_spec'] = null;
            $data['nama'] = $idSpecInput;
        }

        if ($this->perangkatModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil ditambahkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }

    public function userList()
    {
        $db = \Config\Database::connect();
        $users = $db->table('users')->orderBy('nama', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($users);
    }

    public function addUser()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        try {
            $nama = trim($this->request->getPost('nama'));

            if (!$nama) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama tidak boleh kosong'
                ]);
            }

            $db = \Config\Database::connect();

            $insert = $db->table('users')->insert([
                'nama' => $nama
            ]);

            if (!$insert) {
                return $this->response->setJSON([
                    'success' => false,
                    'db_error' => $db->error()
                ]);
            }

            $insertID = $db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $insertID,
                    'nama' => $nama
                ]
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteUser($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();

        $deleted = $db->table('users')->delete(['id' => $id]);
        return $this->response->setJSON([
            'success' => $deleted ? true : false
        ]);
    }

    public function updateUser($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nama = trim($this->request->getPost('nama'));

        if (!$nama) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama tidak boleh kosong'
            ]);
        }

        $db = \Config\Database::connect();

        $db->table('users')->where('id', $id)->update([
            'nama' => $nama
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ── Admin Manage ────────────────────────────────────────────────────────

    public function adminList()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        $admins = $db->table('admin')->select('id, nama, username')->get()->getResultArray();
        return $this->response->setJSON($admins);
    }

    public function addAdmin()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db   = \Config\Database::connect();
        $nama     = trim($this->request->getPost('nama'));
        $username = trim($this->request->getPost('username'));

        if (!$nama || !$username) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nama dan Username wajib diisi']);
        }

        // Check duplicate username
        $exist = $db->table('admin')->where('username', $username)->get()->getRowArray();
        if ($exist) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Username sudah digunakan']);
        }

        $db->table('admin')->insert([
            'nama'       => $nama,
            'username'   => $username,
            // [SECURITY] Argon2ID hash untuk password kosong (akan di-setup oleh admin baru)
            'password'   => hash_password(''),
            'is_super'   => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function deleteAdmin($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        // Prevent self-deletion
        if ($adminSession && $adminSession['id'] == $id) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Tidak dapat menghapus akun sendiri']);
        }

        $db = \Config\Database::connect();
        $db->table('admin')->delete(['id' => $id]);

        return $this->response->setJSON(['success' => true]);
    }

    public function updateAdmin($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nama = trim($this->request->getPost('nama'));
        $username = trim($this->request->getPost('username'));

        if (!$nama || !$username) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nama dan Username wajib diisi']);
        }

        $db = \Config\Database::connect();
        
        // Check duplicate username
        $exist = $db->table('admin')->where('username', $username)->where('id !=', $id)->get()->getRowArray();
        if ($exist) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Username sudah digunakan']);
        }

        $db->table('admin')->where('id', $id)->update([
            'nama' => $nama,
            'username' => $username,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function resetAdminPassword($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        
        $db->table('admin')->where('id', $id)->update([
            // [SECURITY] Argon2ID hash untuk password reset (admin baru harus setup saat login)
            'password'   => hash_password(''),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ── Return Requests ──────────────────────────────────────────────────────

    public function getPendingReturns()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $returnRequestModel = new \App\Models\ReturnRequestModel();
        $requests = $returnRequestModel->getPendingRequestsGrouped();

        return $this->response->setJSON(['success' => true, 'data' => $requests]);
    }

    public function approveReturnGroup()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $approvedIds = $this->request->getPost('approved_ids');
        $rejectedIds = $this->request->getPost('rejected_ids');
        
        if (empty($approvedIds) && empty($rejectedIds)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Tidak ada perangkat yang dipilih.']);
        }

        $approvedIds = is_array($approvedIds) ? $approvedIds : [];
        $rejectedIds = is_array($rejectedIds) ? $rejectedIds : [];

        $returnRequestModel = new \App\Models\ReturnRequestModel();
        $mutasiModel = new \App\Models\MutasiModel();
        $perangkatModel = new \App\Models\PerangkatModel();

        $db = \Config\Database::connect();
        $db->transStart();

        // Process Approved Requests
        foreach ($approvedIds as $requestId) {
            $request = $returnRequestModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

            // Update return_requests status
            $returnRequestModel->update($requestId, ['status' => 'Approved']);

            // Insert new mutasi status to preserve history
            $mutasiId = $request['id_mutasi'];
            $mutasi = $mutasiModel->find($mutasiId);
            
            if ($mutasi) {
                $returnedQty = $request['qty'] ?? 1;

                // Insert the new "Kembali" mutasi record for history
                $mutasiModel->insert([
                    'id_perangkat' => $mutasi['id_perangkat'],
                    'id_non_reg'   => $mutasi['id_non_reg'] ?? null,
                    'qty'          => $returnedQty,
                    'id_users'     => $mutasi['id_users'],
                    'status'       => 'Kembali',
                    'keterangan'   => '-',
                    'updated_by'   => $adminSession['username']
                ]);
                
                if (!empty($mutasi['id_perangkat'])) {
                    // Update perangkat status to Tersedia
                    $perangkatId = $mutasi['id_perangkat'];
                    $perangkatModel->update($perangkatId, ['status' => 'Tersedia']);
                } elseif (!empty($mutasi['id_non_reg'])) {
                    // Non registration item partial logic
                    $originalQty = $mutasi['qty'];
                    $remainingQty = $originalQty - $returnedQty;
                    
                    if ($remainingQty > 0) {
                        $mutasiModel->update($mutasiId, ['qty' => $remainingQty]);
                    } else {
                        // We do not delete the original Dibawa record because it represents the checkout event.
                        // However, to stop it from showing up in getDevicesDibawa, getDevicesDibawa currently queries by status='Dibawa'.
                        // Wait! The user should NO LONGER see this item as "Dibawa" if qty reaches 0. 
                        // But changing its status to something else would rewrite history!
                        // Actually, if we change the original status to 'Selesai', it hides it from Dibawa, but keeps it in DB.
                        $mutasiModel->update($mutasiId, ['status' => 'Selesai']);
                    }

                    // Increase quantity in non_registration
                    $nonRegModel = new \App\Models\NonRegistrationModel();
                    $nr = $nonRegModel->find($mutasi['id_non_reg']);
                    if ($nr) {
                        $newQty = $nr['quantity'] + $returnedQty;
                        $nonRegModel->update($nr['id'], ['quantity' => $newQty]);
                    }
                }
            }
        }

        // Process Rejected Requests
        foreach ($rejectedIds as $requestId) {
            $request = $returnRequestModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

            // Reject the return request, device remains 'Dibawa'
            $returnRequestModel->update($requestId, ['status' => 'Rejected']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Gagal memproses request pengembalian.']);
        }

        return $this->response->setJSON(['success' => true, 'msg' => 'Data pengembalian berhasil diproses.']);
    }

    public function markReturnRead()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $requestIds = $this->request->getPost('request_ids');
        if (empty($requestIds) || !is_array($requestIds)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Request ID tidak valid.']);
        }

        $returnRequestModel = new \App\Models\ReturnRequestModel();
        
        foreach ($requestIds as $id) {
            $returnRequestModel->update($id, ['is_read' => 1]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function followUpItems()
    {
        $db = \Config\Database::connect();
        
        // Find latest mutasi for each perangkat
        $subQuery = $db->table('mutasi')
            ->select('id_perangkat, MAX(updated_at) as latest')
            ->groupBy('id_perangkat')
            ->getCompiledSelect();

        $builder = $db->table('mutasi m');
        $builder->select('p.noreg, p.nama as nama_perangkat, u.nama as nama_user, m.status, m.is_checked, m.updated_at');
        $builder->join("($subQuery) latest_data", 'm.id_perangkat = latest_data.id_perangkat AND m.updated_at = latest_data.latest');
        $builder->join('perangkat p', 'p.id = m.id_perangkat');
        $builder->join('users u', 'u.id = m.id_users', 'left');
        
        // Removed 1 day restriction as requested
        // $builder->where('m.updated_at <', date('Y-m-d H:i:s', strtotime('-1 day')));
        
        // Status condition: Dibawa OR (Terpasang/Terkirim AND is_checked = 0)
        $builder->groupStart()
            ->where('m.status', 'Dibawa')
            ->orGroupStart()
                ->whereIn('m.status', ['Terpasang', 'Terkirim'])
                ->where('m.is_checked', 0)
            ->groupEnd()
        ->groupEnd();
        
        $builder->orderBy('m.updated_at', 'ASC');
        
        $items = $builder->get()->getResultArray();
        
        $result = [];
        $now = time();
        foreach ($items as $item) {
            $updatedTime = strtotime($item['updated_at']);
            $diff = $now - $updatedTime;
            $days = floor($diff / (60 * 60 * 24));
            
            $displayStatus = $item['status'];
            if (in_array($item['status'], ['Terpasang', 'Terkirim']) && $item['is_checked'] == 0) {
                $displayStatus = 'Crosscheck Intan';
            }
            
            $result[] = [
                'noreg' => $item['noreg'],
                'nama_perangkat' => $item['nama_perangkat'] ?? '-',
                'user' => $item['nama_user'] ?? '-',
                'status' => $displayStatus,
                'days_ago' => $days
            ];
        }
        
        return $this->response->setJSON($result);
    }

    // ── Installation Requests ───────────────────────────────────────────────

    public function getPendingInstallations()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $installationModel = new \App\Models\InstallationRequestModel();
        $requests = $installationModel->getPendingRequestsGrouped();

        return $this->response->setJSON(['success' => true, 'data' => $requests]);
    }

    public function approveInstallationGroup()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $approvedIds = $this->request->getPost('approved_ids');
        $rejectedIds = $this->request->getPost('rejected_ids');

        if (empty($approvedIds) && empty($rejectedIds)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Tidak ada perangkat yang dipilih.']);
        }

        $approvedIds = is_array($approvedIds) ? $approvedIds : [];
        $rejectedIds = is_array($rejectedIds) ? $rejectedIds : [];

        $installationModel = new \App\Models\InstallationRequestModel();
        $mutasiModel = new \App\Models\MutasiModel();
        $perangkatModel = new \App\Models\PerangkatModel();

        $db = \Config\Database::connect();
        $db->transStart();

        // Process Approved Requests
        foreach ($approvedIds as $requestId) {
            $request = $installationModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

            // Update installation_requests status
            $installationModel->update($requestId, ['status' => 'Approved']);

            // Create a new mutasi record for Terpasang
            $mutasiId = $request['id_mutasi'];
            $mutasi = $mutasiModel->find($mutasiId);

            if ($mutasi) {
                $installedQty = $request['qty'] ?? 1;

                // Insert new "Terpasang" record
                $mutasiModel->insert([
                    'id_perangkat' => $mutasi['id_perangkat'],
                    'id_non_reg'   => $mutasi['id_non_reg'] ?? null,
                    'qty'          => $installedQty,
                    'id_users'     => $mutasi['id_users'],
                    'status'       => 'Terpasang',
                    'keterangan'   => 'Terpasang di Site ' . (trim($request['node_sentral']) !== '-' && trim($request['node_sentral']) !== '' ? trim($request['node_sentral']) : trim($request['site_sentral'])),
                    'updated_by'   => $adminSession['username']
                ]);

                if (!empty($mutasi['id_perangkat'])) {
                    // Update perangkat status
                    $perangkatId = $mutasi['id_perangkat'];
                    $perangkatModel->update($perangkatId, ['status' => 'Tidak Tersedia']);
                    
                    // The old mutasi should probably just be left alone or marked 'Selesai' 
                    // But in original code, it updated the existing mutasi directly!
                    // To keep history, we should leave the "Dibawa" record and maybe mark it "Selesai".
                    $mutasiModel->update($mutasiId, ['status' => 'Selesai']);
                } elseif (!empty($mutasi['id_non_reg'])) {
                    $originalQty = $mutasi['qty'];
                    $remainingQty = $originalQty - $installedQty;
                    
                    if ($remainingQty > 0) {
                        $mutasiModel->update($mutasiId, ['qty' => $remainingQty]);
                    } else {
                        $mutasiModel->update($mutasiId, ['status' => 'Selesai']);
                    }
                }
            }
        }

        // Process Rejected Requests
        foreach ($rejectedIds as $requestId) {
            $request = $installationModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }
            $installationModel->update($requestId, ['status' => 'Rejected']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Gagal memproses request pemasangan.']);
        }

        return $this->response->setJSON(['success' => true, 'msg' => 'Data pemasangan berhasil diproses.']);
    }

    public function markInstallationRead()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $requestIds = $this->request->getPost('request_ids');
        if (empty($requestIds) || !is_array($requestIds)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Request ID tidak valid.']);
        }

        $installationModel = new \App\Models\InstallationRequestModel();

        foreach ($requestIds as $id) {
            $installationModel->update($id, ['is_read' => 1]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function runMigration()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE nodes ADD COLUMN site_sentral VARCHAR(100) NULL");
            echo "Added site_sentral to nodes table.<br>";
        } catch (\Exception $e) { echo $e->getMessage() . "<br>"; }
        try {
            $db->query("ALTER TABLE installation_requests ADD COLUMN site_sentral VARCHAR(100) NULL");
            echo "Added site_sentral to installation_requests table.<br>";
        } catch (\Exception $e) { echo $e->getMessage() . "<br>"; }
        return "Migration complete.";
    }

    // ── Node Management (Admin) ──────────────────────────────────────────────

    public function nodeList()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        $nodes = $nodeModel->orderBy('arep', 'ASC')->orderBy('node_sentral', 'ASC')->findAll();
        return $this->response->setJSON($nodes);
    }

    public function addNode()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $arep = trim($this->request->getPost('arep'));
        $nodeSentral = trim($this->request->getPost('node_sentral'));
        $siteSentral = trim($this->request->getPost('site_sentral'));

        if (!$nodeSentral) {
            $nodeSentral = '-';
        }

        if ($nodeSentral !== '-' && strlen($nodeSentral) >= 6) {
            $siteSentral = substr($nodeSentral, 0, 6);
        }

        if (!$arep || !$siteSentral) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Arep dan Site Sentral wajib diisi.']);
        }

        $nodeModel = new \App\Models\NodeModel();

        // Check for duplicates
        $existing = $nodeModel->where('arep', $arep)->where('site_sentral', $siteSentral)->where('node_sentral', $nodeSentral)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Node sudah terdaftar.']);
        }

        $nodeModel->insert([
            'arep'         => $arep,
            'site_sentral' => $siteSentral,
            'node_sentral' => $nodeSentral
        ]);

        return $this->response->setJSON(['success' => true, 'msg' => 'Node berhasil ditambahkan.']);
    }

    public function importNodes()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $json = $this->request->getJSON();
        $rows = $json->rows ?? [];

        if (empty($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data valid untuk diimport.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        $inserted = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $row = (array)$row;
            $arep = trim($row['arep'] ?? '');
            $nodeSentral = trim($row['node_sentral'] ?? '');

            if (!$nodeSentral) {
                $nodeSentral = '-';
            }

            if (!$arep) {
                $skipped++;
                continue;
            }

            $siteSentral = ($nodeSentral !== '-' && strlen($nodeSentral) >= 6) ? substr($nodeSentral, 0, 6) : '-';

            // Check duplicate
            $exist = $nodeModel->where('arep', $arep)
                               ->where('site_sentral', $siteSentral)
                               ->where('node_sentral', $nodeSentral)
                               ->first();
            if ($exist) {
                $skipped++;
                continue;
            }

            try {
                $nodeModel->insert([
                    'arep' => $arep,
                    'node_sentral' => $nodeSentral
                ]);
                $inserted++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        if ($inserted > 0) {
            return $this->response->setJSON(['success' => true, 'inserted' => $inserted, 'skipped' => $skipped]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Semua data gagal diimport atau duplikat.']);
        }
    }

    public function deleteNode($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        if ($nodeModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'msg' => 'Node gagal dihapus.']);
    }

    public function updateNode($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $arep = trim($this->request->getPost('arep'));
        $nodeSentral = trim($this->request->getPost('node_sentral'));
        $siteSentral = trim($this->request->getPost('site_sentral'));

        if (!$nodeSentral) {
            $nodeSentral = '-';
        }

        if ($nodeSentral !== '-' && strlen($nodeSentral) >= 6) {
            $siteSentral = substr($nodeSentral, 0, 6);
        }

        if (!$arep || !$siteSentral) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Arep dan Site Sentral wajib diisi.']);
        }

        $nodeModel = new \App\Models\NodeModel();

        // Check duplicate
        $existing = $nodeModel->where('arep', $arep)
                              ->where('site_sentral', $siteSentral)
                              ->where('node_sentral', $nodeSentral)
                              ->where('id !=', $id)
                              ->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Node sudah terdaftar.']);
        }

        $nodeModel->update($id, [
            'arep' => $arep,
            'site_sentral' => $siteSentral,
            'node_sentral' => $nodeSentral
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function bulkDeleteNodes()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $json = $this->request->getJSON();
        $ids = $json->ids ?? [];

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada node yang dipilih.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        try {
            $nodeModel->whereIn('id', $ids)->delete();
            return $this->response->setJSON(['success' => true, 'deleted' => count($ids)]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    public function deleteAllNodes()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        try {
            $nodeModel->emptyTable();
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat mengosongkan data.']);
        }
    }

    /**
     * Lightweight endpoint to check for data changes.
     * Returns the latest mutasi updated_at timestamp so the dashboard
     * can detect when to auto-reload.
     */
    public function checkUpdates()
    {
        $db = \Config\Database::connect();
        $row = $db->query("SELECT MAX(updated_at) as latest FROM mutasi")->getRowArray();
        $countRow = $db->query("SELECT COUNT(*) as total FROM mutasi")->getRowArray();

        return $this->response->setJSON([
            'latest' => $row['latest'] ?? '',
            'total'  => (int)($countRow['total'] ?? 0),
        ]);
    }

    // ── BRP (Bukti Request Perangkat) ────────────────────────────────────────

    /**
     * Get list of available months/years that have BRP documents.
     */
    public function brpAvailableMonths()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $brpModel = new BrpModel();
        $months = $brpModel->getAvailableMonths();

        return $this->response->setJSON(['success' => true, 'data' => $months]);
    }

    /**
     * Get BRP documents for a given month/year.
     */
    public function brpList()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $month = (int) $this->request->getGet('month');
        $year  = (int) $this->request->getGet('year');

        if ($month < 1 || $month > 12 || $year < 2020) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Bulan/tahun tidak valid.']);
        }

        $brpModel = new BrpModel();
        $documents = $brpModel->getByMonth($month, $year);

        return $this->response->setJSON(['success' => true, 'data' => $documents]);
    }

    /**
     * Download a BRP PDF by document ID.
     * TODO(security): Verify requesting admin has permission to access this document.
     */
    public function brpDownload($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $brpModel = new BrpModel();
        $doc = $brpModel->find((int) $id);

        if (!$doc) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'Dokumen tidak ditemukan.']);
        }

        $filePath = WRITEPATH . 'brp' . DIRECTORY_SEPARATOR . $doc['filename'];

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'File PDF tidak ditemukan di server.']);
        }

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $doc['filename'] . '"')
            ->setBody(file_get_contents($filePath));
    }

    // ── NON-REGISTRATION MATERIAL ────────────────────────────────────────

    public function nonRegDashboard()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $page = $this->request->getGet('page') ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $search = $this->request->getGet('keyword') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'nama_material';
        $sortDir = $this->request->getGet('sort_dir') ?? 'asc';

        $nonRegModel = new \App\Models\NonRegistrationModel();
        
        $builder = $nonRegModel->builder();
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('kode_spec', $search)
                    ->orLike('nama_material', $search)
                    ->groupEnd();
        }

        $totalData = $builder->countAllResults(false);
        $builder->orderBy($sortBy, $sortDir);
        $data['perangkat'] = $builder->get($limit, $offset)->getResultArray();

        $data['currentPage'] = $page;
        $data['limit'] = $limit;
        $data['totalPage'] = ceil($totalData / $limit);
        $data['keyword'] = $search;
        
        return view('nonreg_dashboard', $data);
    }

    public function getNonRegHistory($id)
    {
        $model = new \App\Models\MutasiModel();

        $page = $this->request->getVar('page') ?? 1;
        $search = $this->request->getVar('searchHistory') ?? '';

        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchHistory' => $search
        ];

        $result = $model->getNonRegHistory($id, $filters, $limit, $offset);

        $total = $result['total'];
        $totalPage = ceil($total / $limit);

        return $this->response->setJSON([
            'data' => $result['data'],
            'total' => $total,
            'totalPage' => $totalPage,
            'currentPage' => (int) $page
        ]);
    }
    public function nonRegList()
    {
        $nonRegModel = new \App\Models\NonRegistrationModel();
        $items = $nonRegModel->orderBy('nama_material', 'ASC')->findAll();
        return $this->response->setJSON($items);
    }

    public function getNonReg()
    {
        $search = $this->request->getGet('search') ?? '';
        $nonRegModel = new \App\Models\NonRegistrationModel();

        $data = $nonRegModel
            ->groupStart()
            ->like('kode_spec', $search)
            ->orLike('nama_material', $search)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function saveNonReg()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $id = $this->request->getPost('id');
        $kode_spec = trim($this->request->getPost('kode_spec'));
        $nama_material = trim($this->request->getPost('nama_material'));
        $quantity = (int) $this->request->getPost('quantity');

        if (empty($nama_material)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nama material wajib diisi']);
        }

        $nonRegModel = new \App\Models\NonRegistrationModel();

        $data = [
            'kode_spec' => $kode_spec,
            'nama_material' => $nama_material,
            'quantity' => $quantity,
        ];

        if ($id) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $nonRegModel->update($id, $data);
            $msg = 'Data berhasil diupdate';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $nonRegModel->insert($data);
            $msg = 'Data berhasil ditambahkan';
        }

        return $this->response->setJSON(['success' => true, 'msg' => $msg]);
    }

    public function deleteNonReg($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nonRegModel = new \App\Models\NonRegistrationModel();
        $deleted = $nonRegModel->delete($id);

        return $this->response->setJSON(['success' => $deleted ? true : false]);
    }

    public function uploadNonRegExcel()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'msg' => 'File tidak valid']);
        }

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $spreadsheet = $reader->load($file->getTempName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $nonRegModel = new \App\Models\NonRegistrationModel();
            $inserted = 0;
            $updated = 0;

            // Skip header (row 0)
            for ($i = 1; $i < count($sheetData); $i++) {
                $row = $sheetData[$i];
                if (empty(trim($row[0])) && empty(trim($row[1]))) continue;

                $kode_spec = trim($row[0]);
                $nama_material = trim($row[1]);
                $quantity = (int) trim($row[2]);

                $existing = $nonRegModel->where('kode_spec', $kode_spec)->first();
                if ($existing) {
                    $nonRegModel->update($existing['id'], [
                        'nama_material' => $nama_material,
                        'quantity' => $existing['quantity'] + $quantity,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $updated++;
                } else {
                    $nonRegModel->insert([
                        'kode_spec' => $kode_spec,
                        'nama_material' => $nama_material,
                        'quantity' => $quantity,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $inserted++;
                }
            }

            return $this->response->setJSON([
                'success' => true, 
                'msg' => "Berhasil memproses. $inserted ditambahkan, $updated diupdate."
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
    public function getUsersWithDibawa()
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('mutasi m');
        
        $subQuery = $db->table('mutasi')
            ->select('MAX(id) as latest_id')
            ->groupBy('COALESCE(id_perangkat, -id_non_reg)')
            ->getCompiledSelect();

        $builder->join("($subQuery) latest_data", 'm.id = latest_data.latest_id');
        
        $builder->select('m.id as mutasi_id, u.id as user_id, u.nama, m.created_at, p.noreg as perangkat_noreg, p.nama as perangkat_nama, nr.kode_spec as nr_noreg, nr.nama_material as nr_nama, m.qty');
        $builder->join('users u', 'u.id = m.id_users');
        $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
        $builder->join('non_registration nr', 'nr.id = m.id_non_reg', 'left');
        $builder->where('m.status', 'Dibawa');
        $builder->where('m.is_read_admin', 'false');
        $builder->orderBy('u.nama', 'ASC');
        $builder->orderBy('m.created_at', 'DESC');
        
        $mutasiRecords = $builder->get()->getResultArray();
        
        $brpDocuments = $db->table('brp_documents')->select('id, mutasi_ids')->get()->getResultArray();
        
        $users = [];
        foreach ($mutasiRecords as $row) {
            $noreg = $row['perangkat_noreg'] ?? $row['nr_noreg'];
            $nama = $row['perangkat_nama'] ?? $row['nr_nama'];
            
            $brpId = null;
            foreach ($brpDocuments as $brp) {
                $ids = json_decode($brp['mutasi_ids'], true);
                if (is_array($ids) && in_array($row['mutasi_id'], $ids)) {
                    $brpId = $brp['id'];
                    break;
                }
            }
            
            $userName = $row['nama'];
            if (!isset($users[$userName])) {
                $users[$userName] = [
                    'nama' => $userName,
                    'total_dibawa' => 0,
                    'last_dibawa' => $row['created_at'],
                    'devices' => []
                ];
            }
            
            if ($row['created_at'] > $users[$userName]['last_dibawa']) {
                $users[$userName]['last_dibawa'] = $row['created_at'];
            }
            
            $users[$userName]['total_dibawa']++;
            $users[$userName]['devices'][] = [
                'noreg' => $noreg,
                'nama' => $nama,
                'created_at' => $row['created_at'],
                'brp_id' => $brpId
            ];
        }
        
        $users = array_values($users);
        usort($users, function($a, $b) {
            return strtotime($b['last_dibawa']) - strtotime($a['last_dibawa']);
        });
        
        return $this->response->setJSON(['success' => true, 'data' => $users]);
    }
    
    public function markDibawaAsRead()
    {
        $db = \Config\Database::connect();
        $db->table('mutasi')
           ->where('status', 'Dibawa')
           ->where('is_read_admin', 'false')
           ->update(['is_read_admin' => 'true']);
           
        return $this->response->setJSON(['success' => true]);
    }
}