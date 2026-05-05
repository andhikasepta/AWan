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
        $data['users'] = $userModel->findAll();

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
        $users = $db->table('users')->get()->getResultArray();
        return $this->response->setJSON($users);
    }

    public function addUser()
    {
       try {
        $nama = trim($this->request->getPost('nama'));

        if (!$nama){
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Nama tidak boleh kosong'
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
            'data'=>[
                'id'=>$insertID,
                'nama'=>$nama
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
        $db = \Config\Database::connect();

        $deleted = $db->table('users')->delete(['id'=>$id]);
        return $this->response->setJSON([
            'success' => $deleted ? true : false]);
    } 

    public function updateUser($id)
    {
        $nama = trim($this->request->getPost('nama'));

        if(!$nama){
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Nama tidak boleh kosong'
            ]);
        }

        $db = \Config\Database::connect();

        $db->table('users')->where('id', $id)->update([
            'nama'=>$nama
        ]);

        return $this->response->setJSON(['success'=>true]);
    }
}