<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MutasiModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class HistoryController extends BaseController
{
    public function index()
    {
        $mutasiModel = new MutasiModel();
        $userModel = new UserModel();

        $page = $this->request->getVar('page') ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'filter_mutasi' => $this->request->getGet('filter_mutasi'),
            'user' => $this->request->getGet('user')
        ];

        $result = $mutasiModel->getAllHistory($filters, $limit, $offset);

        $data = [
            'history' => $result['data'],
            'totalPage' => ceil($result['total'] / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'users' => $userModel->findAll()
        ];

        return view('history', $data);
    }

    public function historylog($id)
    {
        $model = new \App\Models\MutasiModel();

        $page = $this->request->getVar('page') ?? 1;
        $search = $this->request->getVar('searchHistory') ?? '';

        $limit = 10;
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
}