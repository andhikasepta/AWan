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
        helper('password');
    }

    public function dashboard()
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');

        $filters = [
            'keyword' => $this->request->getGet('keyword'),
            'status' => $this->request->getGet('status'),
            'filter_mutasi' => $this->request->getGet('filter_mutasi'),
            'user' => $this->request->getGet('user'),
            'sort_by' => $this->request->getGet('sort_by'),
            'sort_dir' => $this->request->getGet('sort_dir'),
            'admin_region' => $isSuper ? null : ($adminSession['region'] ?? null),
            'admin_area' => $isSuper ? null : ($adminSession['area'] ?? null),
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
        if (!$isSuper && !empty($adminSession['region']) && !empty($adminSession['area'])) {
            $userModel->groupStart();
            $adminRegions = explode(',', $adminSession['region']);
            foreach ($adminRegions as $r) {
                $userModel->orLike('region', trim($r), 'both');
            }
            $userModel->groupEnd();

            $userModel->groupStart();
            $adminAreas = explode(',', $adminSession['area']);
            foreach ($adminAreas as $a) {
                $userModel->orLike('area', trim($a), 'both');
            }
            $userModel->groupEnd();
        }
        $data['users'] = $userModel->orderBy('nama', 'ASC')->findAll();

        return view('dashboard', $data);
    }

    public function getHistory($id)
    {
        $model = $this->mutasiModel;

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
            $region = trim($this->request->getPost('region'));
            $area = trim($this->request->getPost('area'));

            if (!$nama) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nama tidak boleh kosong'
                ]);
            }

            $db = \Config\Database::connect();

            $insert = $db->table('users')->insert([
                'nama' => $nama,
                'region' => $region,
                'area' => $area
            ]);

            if (!$insert) {
                log_message('error', 'addUser DB error: ' . json_encode($db->error()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan data user.'
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
            log_message('error', 'addUser exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan user.'
            ]);
        }
    }

    public function importUsers()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $json = $this->request->getJSON();
        $rows = $json->rows ?? [];

        if (empty($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data valid untuk diimport.']);
        }

        $db = \Config\Database::connect();
        $inserted = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $row = (array) $row;
            $nama = trim($row['nama'] ?? '');
            $region = trim($row['region'] ?? '');
            $area = trim($row['area'] ?? '');

            if (!$nama) {
                $skipped++;
                continue;
            }

            // Check duplicate by exact nama match
            $exist = $db->table('users')->where('nama', $nama)->get()->getRow();
            if ($exist) {
                $skipped++;
                continue;
            }

            try {
                $db->table('users')->insert([
                    'nama'   => $nama,
                    'region' => $region,
                    'area'   => $area,
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
        $region = trim($this->request->getPost('region'));
        $area = trim($this->request->getPost('area'));

        if (!$nama) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama tidak boleh kosong'
            ]);
        }

        $db = \Config\Database::connect();

        $db->table('users')->where('id', $id)->update([
            'nama' => $nama,
            'region' => $region,
            'area' => $area
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ── Regional Manage ────────────────────────────────────────────────────────

    public function regionalList()
    {
        $db = \Config\Database::connect();
        $data = $db->table('regional')->orderBy('region', 'ASC')->orderBy('area', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function addRegional()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $region = trim($this->request->getPost('region'));
        $area = trim($this->request->getPost('area'));

        if (!$region || !$area) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Region dan Area tidak boleh kosong']);
        }

        $db = \Config\Database::connect();
        $db->table('regional')->insert([
            'region' => $region,
            'area' => $area,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function deleteRegional($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        $deleted = $db->table('regional')->delete(['id' => $id]);
        return $this->response->setJSON(['success' => $deleted ? true : false]);
    }

    public function adminList()
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        $admins = $db->table('admin')->select('id, nama, username, region, area, is_super, ttd_path')->get()->getResultArray();
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
        $region   = trim($this->request->getPost('region'));
        $area     = trim($this->request->getPost('area'));
        $isSuper  = (int) $this->request->getPost('is_super');

        if (!$nama || !$username) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nama dan Username wajib diisi']);
        }

        $exist = $db->table('admin')->where('username', $username)->get()->getRowArray();
        if ($exist) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Username sudah digunakan']);
        }

        $db->table('admin')->insert([
            'nama'       => $nama,
            'username'   => $username,
            'region'     => $region,
            'area'       => $area,
            // [SECURITY] Argon2ID hash untuk password kosong (akan di-setup oleh admin baru)
            'password'   => hash_password(''),
            'is_super'   => $isSuper,
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
        $region = trim($this->request->getPost('region'));
        $area = trim($this->request->getPost('area'));
        $isSuper = (int) $this->request->getPost('is_super');

        if (!$nama || !$username) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nama dan Username wajib diisi']);
        }

        $db = \Config\Database::connect();
        
        $exist = $db->table('admin')->where('username', $username)->where('id !=', $id)->get()->getRowArray();
        if ($exist) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Username sudah digunakan']);
        }

        $db->table('admin')->where('id', $id)->update([
            'nama' => $nama,
            'username' => $username,
            'region' => $region,
            'area' => $area,
            'is_super' => $isSuper,
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
            'password'   => hash_password(''),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function uploadAdminTtd($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $file = $this->request->getFile('ttd_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'msg' => 'File tidak valid.']);
        }

        $mime = $file->getMimeType();
        if ($mime !== 'image/png') {
            return $this->response->setJSON(['success' => false, 'msg' => 'Format file harus PNG.']);
        }

        if ($file->getSizeByUnit('mb') > 2) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Ukuran file maksimal 2MB.']);
        }

        $ttdDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd';
        if (!is_dir($ttdDir)) {
            mkdir($ttdDir, 0755, true);
        }

        $filename = 'admin_' . $id . '.png';
        $file->move($ttdDir, $filename, true);

        $db = \Config\Database::connect();
        $db->table('admin')->where('id', $id)->update([
            'ttd_path' => $filename,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'msg' => 'TTD berhasil diupload.']);
    }

    public function deleteAdminTtd($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        $admin = $db->table('admin')->where('id', $id)->get()->getRowArray();

        if ($admin && !empty($admin['ttd_path'])) {
            $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd' . DIRECTORY_SEPARATOR . $admin['ttd_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $db->table('admin')->where('id', $id)->update([
                'ttd_path' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true, 'msg' => 'TTD berhasil dihapus.']);
    }

    public function getAdminTtd($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $db = \Config\Database::connect();
        $admin = $db->table('admin')->where('id', $id)->get()->getRowArray();

        if (!$admin || empty($admin['ttd_path'])) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'TTD tidak ditemukan.']);
        }

        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd' . DIRECTORY_SEPARATOR . $admin['ttd_path'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'File TTD tidak ditemukan.']);
        }

        return $this->response->setHeader('Content-Type', 'image/png')
            ->setBody(file_get_contents($filePath));
    }

    // ── Self-service Signature Management ─────────────────────────────────────

    public function uploadMySignature()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $id = $adminSession['id'];

        $file = $this->request->getFile('ttd_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'msg' => 'File tidak valid.']);
        }

        $mime = $file->getMimeType();
        if ($mime !== 'image/png') {
            return $this->response->setJSON(['success' => false, 'msg' => 'Format file harus PNG.']);
        }

        if ($file->getSizeByUnit('mb') > 2) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Ukuran file maksimal 2MB.']);
        }

        $ttdDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd';
        if (!is_dir($ttdDir)) {
            mkdir($ttdDir, 0755, true);
        }

        $filename = 'admin_' . $id . '.png';
        $file->move($ttdDir, $filename, true);

        $db = \Config\Database::connect();
        $db->table('admin')->where('id', $id)->update([
            'ttd_path' => $filename,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'msg' => 'Tanda tangan berhasil diupload.']);
    }

    public function getMySignature()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $id = $adminSession['id'];
        $db = \Config\Database::connect();
        $admin = $db->table('admin')->where('id', $id)->get()->getRowArray();

        if (!$admin || empty($admin['ttd_path'])) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'Tanda tangan belum diupload.']);
        }

        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd' . DIRECTORY_SEPARATOR . $admin['ttd_path'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'File tanda tangan tidak ditemukan.']);
        }

        return $this->response->setHeader('Content-Type', 'image/png')
            ->setBody(file_get_contents($filePath));
    }

    public function deleteMySignature()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $id = $adminSession['id'];
        $db = \Config\Database::connect();
        $admin = $db->table('admin')->where('id', $id)->get()->getRowArray();

        if ($admin && !empty($admin['ttd_path'])) {
            $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'ttd' . DIRECTORY_SEPARATOR . $admin['ttd_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $db->table('admin')->where('id', $id)->update([
                'ttd_path' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true, 'msg' => 'Tanda tangan berhasil dihapus.']);
    }

    public function getPendingReturns()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $returnRequestModel = new \App\Models\ReturnRequestModel();

        $isSuperAdmin = (isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin';
        $adminRegion = !$isSuperAdmin ? ($adminSession['region'] ?? null) : null;
        $adminArea = !$isSuperAdmin ? ($adminSession['area'] ?? null) : null;

        $requests = $returnRequestModel->getPendingRequestsGrouped($adminRegion, $adminArea);

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

        foreach ($approvedIds as $requestId) {
            $request = $returnRequestModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

            $returnRequestModel->update($requestId, ['status' => 'Approved']);

            $mutasiId = $request['id_mutasi'];
            $mutasi = $mutasiModel->find($mutasiId);
            
            if ($mutasi) {
                $returnedQty = $request['qty'] ?? 1;

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
                    $perangkatId = $mutasi['id_perangkat'];
                    $perangkat = $perangkatModel->find($perangkatId);
                    if ($perangkat && $perangkat['status'] !== 'Tersedia') {
                        $perangkatModel->update($perangkatId, ['status' => 'Tersedia']);
                    }
                } elseif (!empty($mutasi['id_non_reg'])) {
                    $originalQty = $mutasi['qty'];
                    $remainingQty = $originalQty - $returnedQty;
                    
                    if ($remainingQty > 0) {
                        $mutasiModel->update($mutasiId, ['qty' => $remainingQty]);
                    } else {
                        $mutasiModel->update($mutasiId, ['status' => 'Selesai']);
                    }

                    $nonRegModel = new \App\Models\NonRegistrationModel();
                    $nr = $nonRegModel->find($mutasi['id_non_reg']);
                    if ($nr) {
                        $newQty = $nr['quantity'] + $returnedQty;
                        $nonRegModel->update($nr['id'], ['quantity' => $newQty]);
                    }
                }
            }
        }

        foreach ($rejectedIds as $requestId) {
            $request = $returnRequestModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

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
        $adminSession = session()->get('admin');
        $db = \Config\Database::connect();
        
        $subQuery = $db->table('mutasi')
            ->select('id_perangkat, MAX(updated_at) as latest')
            ->groupBy('id_perangkat')
            ->getCompiledSelect();

        $builder = $db->table('mutasi m');
        $builder->select('p.noreg, p.nama as nama_perangkat, u.nama as nama_user, m.status, m.is_checked, m.updated_at');
        $builder->join("($subQuery) latest_data", 'm.id_perangkat = latest_data.id_perangkat AND m.updated_at = latest_data.latest');
        $builder->join('perangkat p', 'p.id = m.id_perangkat');
        $builder->join('users u', 'u.id = m.id_users', 'left');
        
        $builder->groupStart()
            ->where('m.status', 'Dibawa')
            ->orGroupStart()
                ->whereIn('m.status', ['Terpasang', 'Terkirim'])
                ->where('m.is_checked', 0)
            ->groupEnd()
        ->groupEnd();

        // Filter by admin region/area for non-super admins (same pattern as getUsersWithDibawa)
        $isSuperAdmin = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuperAdmin && !empty($adminSession['region']) && !empty($adminSession['area'])) {
            $builder->groupStart();
            $adminRegions = explode(',', $adminSession['region']);
            foreach ($adminRegions as $r) {
                $builder->orLike('u.region', trim($r), 'both');
            }
            $builder->groupEnd();

            $builder->groupStart();
            $adminAreas = explode(',', $adminSession['area']);
            foreach ($adminAreas as $a) {
                $builder->orLike('u.area', trim($a), 'both');
            }
            $builder->groupEnd();
        }
        
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

    public function getPendingInstallations()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $installationModel = new \App\Models\InstallationRequestModel();

        $isSuperAdmin = (isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin';
        $adminRegion = !$isSuperAdmin ? ($adminSession['region'] ?? null) : null;
        $adminArea = !$isSuperAdmin ? ($adminSession['area'] ?? null) : null;

        $requests = $installationModel->getPendingRequestsGrouped($adminRegion, $adminArea);

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

        foreach ($approvedIds as $requestId) {
            $request = $installationModel->find($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                continue;
            }

            $installationModel->update($requestId, ['status' => 'Approved']);

            $mutasiId = $request['id_mutasi'];
            $mutasi = $mutasiModel->find($mutasiId);

            if ($mutasi) {
                $installedQty = $request['qty'] ?? 1;

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
                    $perangkatId = $mutasi['id_perangkat'];
                    $perangkat = $perangkatModel->find($perangkatId);
                    if ($perangkat && $perangkat['status'] !== 'Tidak Tersedia') {
                        $perangkatModel->update($perangkatId, ['status' => 'Tidak Tersedia']);
                    }
                    
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
        // [SECURITY] Disabled in production — use `php spark migrate` instead
        if (ENVIRONMENT === 'production') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Disabled in production.']);
        }

        $db = \Config\Database::connect();
        $results = [];
        try {
            $db->query("ALTER TABLE nodes ADD COLUMN site_sentral VARCHAR(100) NULL");
            $results[] = 'Added site_sentral to nodes table.';
        } catch (\Exception $e) { $results[] = $e->getMessage(); }
        try {
            $db->query("ALTER TABLE installation_requests ADD COLUMN site_sentral VARCHAR(100) NULL");
            $results[] = 'Added site_sentral to installation_requests table.';
        } catch (\Exception $e) { $results[] = $e->getMessage(); }
        try {
            $db->query("ALTER TABLE admin ADD COLUMN IF NOT EXISTS ttd_path VARCHAR(255) NULL");
            $results[] = 'Added ttd_path to admin table.';
        } catch (\Exception $e) { $results[] = $e->getMessage(); }
        return $this->response->setJSON(['success' => true, 'results' => $results]);
    }

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
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
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
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
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
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
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

    public function brpList()
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $month  = (int) $this->request->getGet('month');
        $year   = (int) $this->request->getGet('year');
        $region = trim($this->request->getGet('region') ?? '');
        $area   = trim($this->request->getGet('area') ?? '');

        if ($month < 1 || $month > 12 || $year < 2020) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Bulan/tahun tidak valid.']);
        }

        if ($region !== '' || $area !== '') {
            // Filter BRP documents by user region/area via users table
            $db = \Config\Database::connect();
            $builder = $db->table('brp_documents bd');
            $builder->select('bd.*');
            $builder->join('users u', 'u.nama = bd.user_name', 'inner');
            $builder->where('bd.period_month', $month);
            $builder->where('bd.period_year', $year);

            if ($region !== '') {
                $builder->like('u.region', $region, 'both');
            }
            if ($area !== '') {
                $builder->like('u.area', $area, 'both');
            }

            $builder->orderBy('bd.generated_number', 'ASC');
            $builder->groupBy('bd.id');
            $documents = $builder->get()->getResultArray();
        } else {
            $brpModel = new BrpModel();
            $documents = $brpModel->getByMonth($month, $year);
        }

        return $this->response->setJSON(['success' => true, 'data' => $documents]);
    }

    public function brpDownload($id)
    {
        $adminSession = session()->get('admin');
        if (!$adminSession) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $brpModel = new BrpModel();
        $doc = $brpModel->find((int) $id);

        if (!$doc) {
            log_message('error', "BRP Download: Dokumen dengan ID {$id} tidak ditemukan di database.");
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'Dokumen tidak ditemukan.']);
        }

        $filePath = WRITEPATH . 'brp' . DIRECTORY_SEPARATOR . $doc['filename'];

        if (!file_exists($filePath)) {
            log_message('error', "BRP Download: File tidak ditemukan di path: {$filePath} (ID: {$id}, filename: {$doc['filename']})");
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'File PDF tidak ditemukan di server. Path: ' . $doc['filename']]);
        }

        // Use CI4's download method for proper binary file serving
        return $this->response->download($filePath, null)->setFileName($doc['filename']);
    }

    public function brpDelete($id)
    {
        $adminSession = session()->get('admin');
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'msg' => 'Akses ditolak.']);
        }

        $brpModel = new BrpModel();
        $doc = $brpModel->find((int) $id);

        if (!$doc) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'msg' => 'Dokumen tidak ditemukan.']);
        }

        $filePath = WRITEPATH . 'brp' . DIRECTORY_SEPARATOR . $doc['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $brpModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

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

        $allowedSorts = ['nama_material', 'kode_spec', 'quantity', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'nama_material';
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

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
        $model = $this->mutasiModel;

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
        $isSuper = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuper) {
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
            log_message('error', 'uploadNonRegExcel error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'msg' => 'Gagal memproses file. Pastikan format CSV benar.']);
        }
    }

    public function approvePeminjaman()
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

        $mutasiModel = new \App\Models\MutasiModel();
        $perangkatModel = new \App\Models\PerangkatModel();
        $nonRegModel = new \App\Models\NonRegistrationModel();

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($approvedIds as $mutasiId) {
            $mutasi = $mutasiModel->find((int) $mutasiId);
            if (!$mutasi || $mutasi['status'] !== 'Dibawa') {
                continue;
            }
            $mutasiModel->update($mutasiId, ['is_read_admin' => 'true']);
        }

        foreach ($rejectedIds as $mutasiId) {
            $mutasi = $mutasiModel->find((int) $mutasiId);
            if (!$mutasi || $mutasi['status'] !== 'Dibawa') {
                continue;
            }

            if (!empty($mutasi['id_perangkat'])) {
                $previousMutasi = $db->table('mutasi')
                    ->where('id_perangkat', $mutasi['id_perangkat'])
                    ->where('id <', $mutasiId)
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                $previousStatus = $previousMutasi ? $previousMutasi['status'] : 'Tersedia';
                
                $perangkat = $perangkatModel->find($mutasi['id_perangkat']);
                if ($perangkat && $perangkat['status'] !== $previousStatus) {
                    $perangkatModel->update($mutasi['id_perangkat'], ['status' => $previousStatus]);
                }
            }

            if (!empty($mutasi['id_non_reg'])) {
                $nr = $nonRegModel->find($mutasi['id_non_reg']);
                if ($nr) {
                    $restoredQty = $nr['quantity'] + ($mutasi['qty'] ?? 1);
                    $nonRegModel->update($nr['id'], ['quantity' => $restoredQty]);
                }
            }

            $mutasiModel->delete($mutasiId);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Gagal memproses peminjaman.']);
        }

        $msg = 'Peminjaman berhasil diproses.';
        if (empty($approvedIds) && !empty($rejectedIds)) {
            $msg = 'Peminjaman ditolak.';
        }

        return $this->response->setJSON(['success' => true, 'msg' => $msg]);
    }

    public function getUsersWithDibawa()
    {
        $adminSession = session()->get('admin');
        $db = \Config\Database::connect();
        
        $builder = $db->table('mutasi m');
        
        $subQuery = $db->table('mutasi')
            ->select('MAX(id) as latest_id')
            ->groupBy('COALESCE(id_perangkat, -id_non_reg)')
            ->getCompiledSelect();

        $builder->join("($subQuery) latest_data", 'm.id = latest_data.latest_id');
        
        $builder->select('m.id as mutasi_id, u.id as user_id, u.nama, m.created_at, p.noreg as perangkat_noreg, p.nama as perangkat_nama, nr.kode_spec as nr_noreg, nr.nama_material as nr_nama, m.qty, m.is_read_admin');
        $builder->join('users u', 'u.id = m.id_users');
        $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
        $builder->join('non_registration nr', 'nr.id = m.id_non_reg', 'left');
        $builder->where('m.status', 'Dibawa');

        $isSuperAdmin = $adminSession && ((isset($adminSession['is_super']) && $adminSession['is_super'] == 1) || $adminSession['username'] === 'admin');
        if (!$isSuperAdmin && !empty($adminSession['region']) && !empty($adminSession['area'])) {
            $builder->groupStart();
            $adminRegions = explode(',', $adminSession['region']);
            foreach ($adminRegions as $r) {
                $builder->orLike('u.region', trim($r), 'both');
            }
            $builder->groupEnd();

            $builder->groupStart();
            $adminAreas = explode(',', $adminSession['area']);
            foreach ($adminAreas as $a) {
                $builder->orLike('u.area', trim($a), 'both');
            }
            $builder->groupEnd();
        }

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
                'mutasi_id' => $row['mutasi_id'],
                'noreg' => $noreg,
                'nama' => $nama,
                'created_at' => $row['created_at'],
                'qty' => $row['qty'],
                'brp_id' => $brpId,
                'is_read_admin' => $row['is_read_admin']
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