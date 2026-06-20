<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use App\Models\UserModel;
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
            'password'   => password_hash('', PASSWORD_DEFAULT),
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
                $this->mutasiModel->insert([
                    'id_perangkat' => $mutasi['id_perangkat'],
                    'id_users'     => $mutasi['id_users'],
                    'status'       => 'Kembali',
                    'keterangan'   => '-',
                    'updated_by'   => $adminSession['username']
                ]);
                
                // Update perangkat status to Tersedia
                $perangkatId = $mutasi['id_perangkat'];
                $perangkatModel->update($perangkatId, ['status' => 'Tersedia']);
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
            $returnRequestModel->update($id, ['is_read' => true]);
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
        
        // Older than 1 day
        $builder->where('m.updated_at <', date('Y-m-d H:i:s', strtotime('-1 day')));
        
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

            // Update the mutasi record
            $mutasiId = $request['id_mutasi'];
            $mutasi = $mutasiModel->find($mutasiId);

            if ($mutasi) {
                // Update existing mutasi with Terpasang status and keterangan
                $mutasiModel->update($mutasiId, [
                    'status'     => 'Terpasang',
                    'keterangan' => 'Terpasang di ' . $request['node_sentral'],
                    'updated_by' => $adminSession['username']
                ]);

                // Update perangkat status
                $perangkatId = $mutasi['id_perangkat'];
                $perangkatModel->update($perangkatId, ['status' => 'Tidak Tersedia']);
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
            $installationModel->update($id, ['is_read' => true]);
        }

        return $this->response->setJSON(['success' => true]);
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

        if (!$arep || !$nodeSentral) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Arep dan Node Sentral wajib diisi.']);
        }

        $nodeModel = new \App\Models\NodeModel();

        // Check for duplicates
        $existing = $nodeModel->where('arep', $arep)->where('node_sentral', $nodeSentral)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Node sudah terdaftar.']);
        }

        $nodeModel->insert([
            'arep'         => $arep,
            'node_sentral' => $nodeSentral
        ]);

        return $this->response->setJSON(['success' => true, 'msg' => 'Node berhasil ditambahkan.']);
    }

    public function deleteNode($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $nodeModel = new \App\Models\NodeModel();
        $nodeModel->delete($id);

        return $this->response->setJSON(['success' => true, 'msg' => 'Node berhasil dihapus.']);
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
}