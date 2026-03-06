<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\PerangkatModel;
use App\Models\MutasiModel;
use Config\Database;
use Config\Services;

class DashboardController extends BaseController
{
    public function index()
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = 30;
        $offset = ($page - 1)*$limit;

        $model = new PerangkatModel();
        $perangkat = $model->orderBy('id', 'DESC')->findAll($limit, $offset);
        $totalData = $model->countAllResults();
        $totalPage = ceil($totalData/$limit);

        return view('dashboard', [
            'perangkat'=>$perangkat,
            'currentPage'=>$page,
            'totalPage'=>$totalPage,
            'limit'=>$limit
        ]);
    }
}