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

        $perangkatList = $this->request->getPost('perangkat');

        if (empty($perangkatList)){
            return redirect()->back()->with('error', 'Belum ada perangkat yang ditambahkan!');
        }

        foreach ($perangkatList as $pl){
            $mutasiModel->insert([
                'id_perangkat'=>$pl['id'],
                'noreg'=>$pl['noreg'],
                'id_users'=>$this->request->getPost('user'),
                'keterangan'=>sanitize_utf8($this->request->getPost('keterangan')),
                'status'=>'Dibawa'
            ]);

            $perangkatModel->update($pl['id'],[
                'status'=>$this->mapStatusPerangkat('Dibawa')
            ]);
        }

        return redirect()->to('/')->with('success', 'Data berhasil disimpan, Silakan konfirmasi ke Admin');
    }

    private function mapStatusPerangkat($statusMutasi)
    {
        if(empty($statusMutasi)){
            return 'Tersedia';
        }

        $statusMutasi = strtolower($statusMutasi);

        if($statusMutasi=='dibawa' || $statusMutasi=='terpasang' || $statusMutasi=='pengiriman' || $statusMutasi=='terkirim'){
            return 'Tidak Tersedia';
        }else if ($statusMutasi=='kembali'){
            return 'Tersedia';
        }
        return 'Tersedia';
    }
}
