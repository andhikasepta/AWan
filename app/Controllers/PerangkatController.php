<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;

class PerangkatController extends BaseController
{
    protected $perangkatModel;
    protected $mutasiModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->mutasiModel = new MutasiModel();
    }

    public function tambahPerangkat()
    {
        $this->perangkatModel->insert([
            'noreg'         => $this->request->getPost('noreg'),
            'nama'          => $this->request->getPost('nama'),
            'status'        => 'Tersedia'
        ]);
        
        return $this->response->setJSON(['success'=>true]);
    }

    public function editPerangkat($id)
    {
        $data = $this->perangkatModel->getDetailMutasi($id);
        return $this->response->setJSON($data);
    }

    public function updatePerangkat()
    {
        $id_perangkat = $this->request->getPost('id');
        $statusMutasi = $this->request->getPost('status_mutasi');
        $id_users = $this->request->getPost('id_users');
        $keterangan = $this->request->getPost('keterangan');

        $this->mutasiModel->insert([
            'id_perangkat'=>$id_perangkat,
            'id_users'=>$id_users,
            'status'=>$statusMutasi,
            'keterangan'=>$keterangan,
            'is_checked'=>0
        ]);

        $statusPerangkat = $this->mapStatusPerangkat($statusMutasi);

        $this->perangkatModel->update($id_perangkat,[
            'user_id'=>$id_users,
            'status'=>$statusPerangkat,
            'keterangan'=>$keterangan
        ]);

        return $this->response->setJSON(['success'=>true]);
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
