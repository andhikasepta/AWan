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
        if ($admin && password_verify($password, $admin['password'])) {
            $this->session->set('admin', $admin);
            return redirect()->to('/dashboard');
        } else {
            return redirect()->back()->with('error', 'Username atau password salah');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }

    public function updatePassword()
    {
        $adminSession = session()->get('admin');

        if (!$adminSession || !isset($adminSession['id'])) {
            return redirect()->back()->with('error', 'Sesi tidak ditemukan, silakan login ulang.');
        }

        $adminId = $adminSession['id'];

        $oldPass = trim($this->request->getPost('current_password'));
        $newPass = trim($this->request->getPost('new_password'));
        $confPass = trim($this->request->getPost('confirm_password'));

        if (empty($oldPass) || empty($newPass)) {
            return redirect()->back()->with('error', 'Semua field password harus diisi.');
        }

        $adminDb = $this->db->table('admin')->where('id', $adminId)->get()->getRowArray();

        if (!password_verify($oldPass, $adminDb['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai. Silakan cek kembali.')->with('openModal', true);
        }

        if (strlen($newPass) < 5) {
            return redirect()->back()->with('error', 'Password baru harus minimal 5 karakter.')->with('openModal', true);
        }

        if ($oldPass === $newPass) {
            return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password lama.')->with('openModal', true);
        }

        if ($newPass !== $confPass) {
            return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.')->with('openModal', true);
        }

        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);

        $update = $this->db->table('admin')->where('id', $adminId)->update([
            'password' => $hashedPassword
        ]);

        if ($update) {
            session()->destroy();
            return redirect()->to('login');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui database.');
    }
}