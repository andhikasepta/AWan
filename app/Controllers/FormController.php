<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;
use App\Models\UserModel;
use App\Models\MutasiModel;
use CodeIgniter\HTTP\ResponseInterface;

class FormController extends BaseController
{
    protected $perangkatModel;
    protected $userModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $perangkat = $this->perangkatModel->where('status', 'Tersedia')->findAll();
        $user = $this->userModel->findAll();
    
        return view('formmutasi', [
            'perangkat'=>$perangkat,
            'users'=>$user
        ]);
    }

    public function submit()
    {
        $perangkatModel = new PerangkatModel();
        $mutasiModel = new MutasiModel();

        $noreg = $this->request->getPost('noreg');

        $perangkat = $perangkatModel->where('noreg', $noreg)->first();

        if (!$perangkat){
            return redirect()->back()->with('error', 'Perangkat tidak ditemukan!');
        }
        if ($perangkat['status']=='Tidak Tersedia'){
            return redirect()->back()->with('error', 'Perangkat sedang digunakan!');
        }

        $mutasiModel->insert([
            'id_perangkat'=>$perangkat['id'],
            'id_users'=>$this->request->getPost('user'),
            'keterangan'=>$this->request->getPost('keterangan'),
            'status'=>'Dibawa'
        ]);
        $perangkatModel->update($perangkat['id'], [
            'status'=>$this->mapStatusPerangkat('Dibawa')
        ]);

        return redirect()->to('/')->with('success', 'Data berhasil disimpan, Silakan konfirmasi ke Admin');
    }

    private function mapStatusPerangkat($statusMutasi)
    {
        if(empty($statusMutasi)){
            return 'Tersedia';
        }

        $statusMutasi = strtolower($statusMutasi);

        if($statusMutasi=='dibawa' || $statusMutasi=='terpasang'){
            return 'Tidak Tersedia';
        }else if ($statusMutasi=='kembali'){
            return 'Tersedia';
        }
        return 'Tersedia';
    }
}
