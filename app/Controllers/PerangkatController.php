<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use App\Models\SpecPerangkatModel;

class PerangkatController extends BaseController
{
    protected $perangkatModel;
    protected $mutasiModel;
    protected $specModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->mutasiModel = new MutasiModel();
        $this->specModel = new SpecPerangkatModel();
    }

    public function tambahPerangkat()
    {
        $kode_id = $this->request->getPost('kode_id');
        $nama = $this->request->getPost('nama');
        $id_spec_input = $this->request->getPost('id_spec');

        if (empty($kode_id)) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Kode ID wajib diisi']);
        }

        $spec = null;
        if (is_numeric($id_spec_input)) {
            $spec = $this->specModel->find($id_spec_input);
        }

        if ($spec) {
            $id_spec = $spec['id'];
            $kode_spec = $spec['kode_spec'];
        } else {
            if (empty($nama)) {
                return $this->response->setJSON(['success' => false, 'msg' => 'Nama perangkat wajib diisi']);
            }

            $kode_spec = strtoupper($id_spec_input);
            $id_spec = $this->specModel->insert([
                'kode_spec' => $kode_spec,
                'nama_perangkat' => $nama
            ]);
        }

        $noreg = $kode_spec . $kode_id;
        $exist = $this->perangkatModel->where('noreg', $noreg)->first();

        if ($exist) {
            return $this->response->setJSON(['success' => false, 'msg' => 'Nomor registrasi sudah terdaftar']);
        }

        $nama_final = $spec ? $spec['nama_perangkat'] : $nama;

        $this->perangkatModel->insert([
            'id_spec' => $id_spec,
            'kode_id' => $kode_id,
            'noreg' => $noreg,
            'nama' => $nama_final,
            'status' => 'Tersedia'
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // public function import()
    // {
    //     set_time_limit(0);

    //     $file = $this->request->getFile('file');
    //     $handle = fopen($file->getTempName(), 'r');

    //     fgetcsv($handle);

    //     while (($row = fgetcsv($handle)) !== false){
    //         $kode_spec = strtoupper(trim($row[0]));
    //         $nama_spec = trim($row[1]);
    //         $kode_id = trim($row[2]);

    //         $spec = $this->specModel
    //         ->where('kode_spec', $kode_spec)
    //         ->where('nama_perangkat', $nama_spec)
    //         ->first();

    //         if(!$spec){
    //             $id_spec = $this->specModel->insert([
    //                 'kode_spec'=>$kode_spec,
    //                 'nama_perangkat'=>$nama_spec
    //             ]);
    //         }else{
    //             $id_spec = $spec['id'];
    //         }

    //         $noreg = $kode_spec . $kode_id;

    //         if($this->perangkatModel->where('noreg', $noreg)->first()) continue;

    //         $this->perangkatModel->insert([
    //             'id_spec'=>$id_spec,
    //             'kode_id'=>$kode_id,
    //             'noreg'=>$noreg,
    //             'nama'=>$nama_spec,
    //             'status'=>'Tersedia'
    //         ]);
    //     }

    //     fclose($handle);
    //     return redirect()->to('dashboard')->with('success', 'Import Data Berhasil');
    // }

    public function cekNoreg()
    {
        $noreg = $this->request->getGet('noreg');
        $exist = $this->perangkatModel->where('noreg', $noreg)->first();

        return $this->response->setJSON([
            'exists' => $exist ? true : false
        ]);
    }

    public function getSpec()
    {
        $search = $this->request->getGet('search') ?? '';

        $data = $this->specModel
            ->groupStart()
            ->like('kode_spec', $search)
            ->orLike('nama_perangkat', $search)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function getSpecById()
    {
        $id = $this->request->getGet('id');
        $data = null;
        if (is_numeric($id)) {
            $data = $this->specModel->find($id);
        }

        return $this->response->setJSON($data);
    }

    public function editPerangkat($id)
    {
        $data = $this->perangkatModel->getDetailMutasi($id);
        $data['status_mutasi'] = $data['status'];
        return $this->response->setJSON($data);
    }

    public function updatePerangkat()
    {
        $id_perangkat = $this->request->getPost('id');
        $statusMutasi = $this->request->getPost('status_mutasi');
        $id_users = $this->request->getPost('id_users');
        $keterangan = sanitize_utf8($this->request->getPost('keterangan'));

        $this->mutasiModel->insert([
            'id_perangkat' => $id_perangkat,
            'id_users' => $id_users,
            'status' => $statusMutasi,
            'keterangan' => $keterangan,
            'is_checked' => 0
        ]);

        $statusPerangkat = $this->mapStatusPerangkat($statusMutasi);

        $this->perangkatModel->update($id_perangkat, [
            'user_id' => $id_users,
            'status' => $statusPerangkat,
            'keterangan' => $keterangan
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $perangkat = $this->perangkatModel->find($id);

        if ($perangkat) {
            $this->perangkatModel->delete($id);
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    private function mapStatusPerangkat($statusMutasi)
    {
        if (empty($statusMutasi)) {
            return 'Tersedia';
        }

        $statusMutasi = strtolower($statusMutasi);

        if ($statusMutasi == 'dibawa' || $statusMutasi == 'terpasang' || $statusMutasi == 'pengiriman' || $statusMutasi == 'terkirim') {
            return 'Tidak Tersedia';
        } else if ($statusMutasi == 'kembali') {
            return 'Tersedia';
        }
        return 'Tersedia';
    }
}
