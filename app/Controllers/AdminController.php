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
            return redirect()->to('login');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }
}