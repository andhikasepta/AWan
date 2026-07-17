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
        helper(['form', 'password']);
    }

    public function index()
    {
        return view('login');
    }

    public function login()
    {
        helper('logsecurity');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $safeUsername = sanitizeLog((string) $username);

        $admin = $this->db->table('admin')->where('username', $username)->get()->getRowArray();
        if ($admin && verify_password($password, $admin['password'])) {
            $this->session->set('admin', $admin);
            log_message('info', 'Login berhasil username: ' . $safeUsername);

            if (verify_password('', $admin['password'])) {
                return redirect()->to('/setup-password');
            }

            if (password_needs_upgrade($admin['password'])) {
                $newHash = hash_password($password);
                $this->db->table('admin')->where('id', $admin['id'])->update([
                    'password'   => $newHash,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                log_message('info', 'Password rehashed ke Argon2ID untuk username: ' . $safeUsername);
            }

            return redirect()->to('/dashboard');
        } else {
            log_message('warning', 'Login gagal username: ' . $safeUsername);
            return redirect()->back()->with('error', 'Username atau password salah');
        }
    }

    /**
     * Session heartbeat — called by frontend JS to keep the
     * server-side session alive while the user is active.
     * Returns JSON so the frontend can detect an expired session.
     */
    public function sessionHeartbeat()
    {
        // Reading the session is enough to refresh its timestamp
        $admin = $this->session->get('admin');

        if (!$admin) {
            return $this->response->setJSON([
                'alive' => false,
                'msg'   => 'Session expired',
            ]);
        }

        return $this->response->setJSON([
            'alive' => true,
        ]);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }

    public function setupPassword()
    {
        $adminSession = session()->get('admin');

        if (!$adminSession || !isset($adminSession['id'])) {
            return redirect()->to('/login');
        }

        $adminDb = $this->db->table('admin')->where('id', $adminSession['id'])->get()->getRowArray();
        if (!verify_password('', $adminDb['password'])) {
            return redirect()->to('/dashboard');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $newPass  = trim($this->request->getPost('new_password'));
            $confPass = trim($this->request->getPost('confirm_password'));

            if (empty($newPass)) {
                return redirect()->back()->with('error', 'Password baru harus diisi.');
            }

            if (strlen($newPass) < 12) {
                return redirect()->back()->with('error', 'Password baru harus minimal 12 karakter.');
            }
            if (!preg_match('/[A-Z]/', $newPass)) {
                return redirect()->back()->with('error', 'Password harus mengandung minimal 1 huruf kapital.');
            }
            if (!preg_match('/[0-9]/', $newPass)) {
                return redirect()->back()->with('error', 'Password harus mengandung minimal 1 angka.');
            }
            if (!preg_match('/[^a-zA-Z0-9]/', $newPass)) {
                return redirect()->back()->with('error', 'Password harus mengandung minimal 1 karakter spesial (!@#$%^&*).');
            }
            if ($newPass !== $confPass) {
                return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
            }

            $this->db->table('admin')->where('id', $adminSession['id'])->update([
                'password'   => hash_password($newPass),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->session->destroy();
            return redirect()->to('/login')->with('success', 'Password berhasil diatur. Silakan login dengan password baru.');
        }

        return view('setup_password');
    }

    public function updatePassword()
    {
        $adminSession = session()->get('admin');

        if (!$adminSession || !isset($adminSession['id'])) {
            return redirect()->back()->with('error', 'Sesi tidak ditemukan, silakan login ulang.');
        }

        $adminId = $adminSession['id'];

        $oldPass  = trim($this->request->getPost('current_password'));
        $newPass  = trim($this->request->getPost('new_password'));
        $confPass = trim($this->request->getPost('confirm_password'));

        if (empty($oldPass) || empty($newPass)) {
            return redirect()->back()->with('error', 'Semua field password harus diisi.')->with('openModal', true);
        }

        $adminDb = $this->db->table('admin')->where('id', $adminId)->get()->getRowArray();

        if (!verify_password($oldPass, $adminDb['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai. Silakan cek kembali.')->with('openModal', true);
        }

        if (strlen($newPass) < 12) {
            return redirect()->back()->with('error', 'Password baru harus minimal 12 karakter.')->with('openModal', true);
        }

        if (!preg_match('/[A-Z]/', $newPass)) {
            return redirect()->back()->with('error', 'Password harus mengandung minimal 1 huruf kapital.')->with('openModal', true);
        }

        if (!preg_match('/[0-9]/', $newPass)) {
            return redirect()->back()->with('error', 'Password harus mengandung minimal 1 angka.')->with('openModal', true);
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $newPass)) {
            return redirect()->back()->with('error', 'Password harus mengandung minimal 1 karakter spesial (!@#$%^&*).')->with('openModal', true);
        }

        if ($oldPass === $newPass) {
            return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password lama.')->with('openModal', true);
        }

        if ($newPass !== $confPass) {
            return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.')->with('openModal', true);
        }

        $hashedPassword = hash_password($newPass);

        $update = $this->db->table('admin')->where('id', $adminId)->update([
            'password' => $hashedPassword
        ]);

        if ($update) {
            $this->session->destroy();
            return redirect()->to('/login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui database.')->with('openModal', true);
    }
}