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

    public function bulkDelete()
    {
        $json = $this->request->getJSON();
        $ids = $json->ids ?? [];

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        $deleted = 0;
        foreach ($ids as $id) {
            $perangkat = $this->perangkatModel->find($id);
            if ($perangkat) {
                $this->perangkatModel->delete($id);
                $deleted++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'deleted' => $deleted
        ]);
    }

    public function bulkUpdatePerangkat()
    {
        $json = $this->request->getJSON();

        $ids = $json->ids ?? [];
        $id_users = $json->id_users ?? '';
        $status_mutasi = $json->status_mutasi ?? '';
        $keterangan = isset($json->keterangan) ? sanitize_utf8($json->keterangan) : '';

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        // At least one field must be provided
        if (empty($id_users) && empty($status_mutasi) && empty($keterangan)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada perubahan yang diisi']);
        }

        $updated = 0;

        foreach ($ids as $id_perangkat) {
            $perangkat = $this->perangkatModel->find($id_perangkat);
            if (!$perangkat) continue;

            // Get current latest mutasi for this perangkat to use as defaults
            $currentMutasi = $this->perangkatModel->getDetailMutasi($id_perangkat);

            // Determine final values: use new value if provided, otherwise keep current
            $finalUser = !empty($id_users) ? $id_users : ($currentMutasi['id_users'] ?? null);
            $finalStatus = !empty($status_mutasi) ? $status_mutasi : ($currentMutasi['status'] ?? null);
            $finalKet = !empty($keterangan) ? $keterangan : ($currentMutasi['keterangan'] ?? '');

            // Insert new mutasi record (same as single edit)
            $this->mutasiModel->insert([
                'id_perangkat' => $id_perangkat,
                'id_users'     => $finalUser,
                'status'       => $finalStatus,
                'keterangan'   => $finalKet,
                'is_checked'   => 0
            ]);

            // Update perangkat status
            $statusPerangkat = $this->mapStatusPerangkat($finalStatus);
            $this->perangkatModel->update($id_perangkat, [
                'user_id'     => $finalUser,
                'status'      => $statusPerangkat,
                'keterangan'  => $finalKet
            ]);

            $updated++;
        }

        return $this->response->setJSON([
            'success' => true,
            'updated' => $updated
        ]);
    }

    public function validateCsvNoreg()
    {
        $json = $this->request->getJSON();
        $noregList = $json->noreg_list ?? [];

        $results = [];
        // Count occurrences in CSV to detect internal duplicates
        $csvCounts = array_count_values(array_map('trim', $noregList));

        foreach ($noregList as $index => $noreg) {
            $noreg = trim($noreg);

            if (empty($noreg)) {
                $results[] = ['index' => $index, 'noreg' => $noreg, 'status' => 'invalid', 'message' => 'Noreg kosong'];
                continue;
            }

            // Check CSV internal duplicates
            if ($csvCounts[$noreg] > 1) {
                $results[] = ['index' => $index, 'noreg' => $noreg, 'status' => 'csv_duplicate', 'message' => 'Duplikat dalam CSV'];
                continue;
            }

            // Check database
            $exist = $this->perangkatModel->where('noreg', $noreg)->first();
            if ($exist) {
                $results[] = ['index' => $index, 'noreg' => $noreg, 'status' => 'db_duplicate', 'message' => 'Sudah terdaftar di database'];
            } else {
                $results[] = ['index' => $index, 'noreg' => $noreg, 'status' => 'tersedia', 'message' => 'Tersedia'];
            }
        }

        return $this->response->setJSON(['success' => true, 'results' => $results]);
    }

    public function importCsv()
    {
        $json = $this->request->getJSON();
        $rows = $json->rows ?? [];

        if (empty($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data untuk diimport']);
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $noreg = trim($row->noreg ?? '');
            $nama = trim($row->nama ?? '');

            if (empty($noreg) || empty($nama)) {
                $skipped++;
                continue;
            }

            // Double-check duplicate before insert
            $exist = $this->perangkatModel->where('noreg', $noreg)->first();
            if ($exist) {
                $skipped++;
                continue;
            }

            try {
                $this->perangkatModel->insert([
                    'noreg' => $noreg,
                    'nama' => $nama,
                    'status' => 'Tersedia'
                ]);
                $inserted++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'inserted' => $inserted,
            'skipped' => $skipped
        ]);
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
