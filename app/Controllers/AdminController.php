<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use Config\Database;
use Config\Services;

class AdminController extends BaseController
{
    protected $perangkatModel;
    protected $mutasiModel;
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->mutasiModel = new MutasiModel();
        $this->db = Database::connect();

        $this->session = Services::session();
        helper('form');
    }

    public function index()
    {
        return view('login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $admin = $this->db->table('admin')->where('username', $username)->get()->getRowArray();
        if ($admin && password_verify($password, $admin['password'])){
            $this->session->set('admin', $admin);
            return redirect()->to('/dashboard');
        }
        else{
            $this->session->setFlashdata('error', 'username atau password salah');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }

    public function dashboard()
    {
        $data['perangkat'] = $this->perangkatModel->getDataDash();

        $configMutasi = new \Config\Mutasi();
        $data['statuses'] = $configMutasi->status;

        $userModel = new \App\Models\UserModel();
        $data['users'] = $userModel->findAll();

        return view('dashboard', $data);
    }

    public function getPerangkat($id)
    {
        $data = $this->perangkatModel->getDetailMutasi($id);
        return $this->response->setJSON($data);
    }

    public function updatePerangkat($id_perangkat)
    {
        $status = $this->request->getPost('status');
        $id_users = $this->request->getPost('id_users');
        $keterangan = $this->request->getPost('keterangan');

        $this->mutasiModel->insert([
            'id_perangkat'=>$id_perangkat,
            'id_users'=>$id_users,
            'status'=>$status,
            'keterangan'=>$keterangan
        ]);

        $this->perangkatModel->update($id_perangkat, [
            'user_id'=>$id_users,
            'keterangan'=>$keterangan,
            'status'=>$status
        ]);
        return redirect()->to('/dashboard');
    }

    public function ajaxUpdate()
    {
        $id_perangkat = $this->request->getPost('id');
        $status = $this->request->getPost('status_mutasi');
        $id_users = $this->request->getPost('id_users');
        $keterangan = $this->request->getPost('keterangan');

        $this->mutasiModel->insert([
            'id_perangkat'=>$id_perangkat,
            'id_users'=>$id_users,
            'status'=>$status,
            'keterangan'=>$keterangan,
            'is_checked'=>0
        ]);

        $this->perangkatModel->update($id_perangkat,[
            'user_id'=>$id_users,
            'status'=>$status,
            'keterangan'=>$keterangan
        ]);

        return $this->response->setJSON(['success'=>true]);
    }

    public function checkMutasi($id)
    {
        $mutasi = $this->mutasiModel->find($id);
        
        if (!$mutasi || $mutasi['status'] !== 'Terpasang'){
            return $this->response->setJSON(['success'=>false]);
        }

        $this->mutasiModel->update($id, [
            'is_checked'=>1,
            'checked_at'=>date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success'=>true]);
    }
    
    public function tambah()
    {
        return view('tambahbarang');   
    }
    public function simpanPerangkat()
    {
        $noreg = $this->request->getPost('noreg');
        $nama = $this->request->getPost('nama');
        $sn = $this->request->getPost('serial_number');
        $this->perangkatModel->insert([
            'noreg'         => $noreg,
            'nama'          => $nama,
            'serial_number' => $sn,
            'status'        => 'Tersedia'
    ]);
    return redirect()->to('/dashboard')->with('success', 'Data berhasil disimpan');
    }
    public function delete($id)
    {
        $perangkat = $this->perangkatModel->find($id);
        if ($perangkat) {
            $this->perangkatModel->delete($id);
            return redirect()->to('/dashboard')->with('success', 'Perangkat berhasil dihapus.');
        } else {
            return redirect()->to('/dashboard')->with('error', 'Data tidak ditemukan.');
        }
    }
}
