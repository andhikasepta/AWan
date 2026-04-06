<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use Config\Database;
use Config\Services;

class DashboardController extends BaseController
{
    protected $perangkatModel;
    protected $mutasiModel;

    public function __construct()
    {
        $this->perangkatModel = new PerangkatModel();
        $this->mutasiModel = new MutasiModel();
    }

    public function dashboard()
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = 30;
        $offset = ($page - 1)*$limit;
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
        $data['totalPage'] = ceil($totalData/$limit);

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

        $limit = 30;
        $offset = ($page - 1) * $limit;

        $filters=[
            'searchHistory' => $search
        ];

        $result = $model->getDataHistory($id, $filters, $limit, $offset);

        $total = $result['total'];
        $totalPage = ceil($total/$limit);

        return $this->response->setJSON([
            'data'=>$result['data'],
            'total'=>$total,
            'totalPage'=>$totalPage,
            'currentPage'=>(int)$page
        ]);
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
}