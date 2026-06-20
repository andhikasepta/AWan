<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerangkatModel;
use App\Models\UserModel;
use App\Models\MutasiModel;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;

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
        $user = $this->userModel->orderBy('nama', 'ASC')->findAll();

        return view('formmutasi', [
            'users' => $user
        ]);
    }

    public function cekNoreg()
    {
        $noreg = $this->request->getGet('noreg');
        
        $perangkat = $this->perangkatModel
            ->where('noreg', $noreg)
            ->first();

        if (!$perangkat) {
            return $this->response->setJSON([
                'exists' => false,
                'toast_type' => 'error',
                'message' => 'No registrasi tidak ditemukan di database.'
            ]);
        }

        if (strtolower($perangkat['status']) !== 'tersedia') {
            $mutasiModel = new MutasiModel();
            $latestMutasi = $mutasiModel->where('id_perangkat', $perangkat['id'])
                                        ->orderBy('id', 'DESC')
                                        ->first();
            
            $userName = 'User';
            $statusStr = $perangkat['status'];

            if ($latestMutasi) {
                $statusStr = $latestMutasi['status'];
                $user = $this->userModel->find($latestMutasi['id_users']);
                if ($user) {
                    $userName = $user['nama'];
                }
                
                $message = "Status masih {$statusStr} oleh {$userName}";
            } else {
                $message = "Status masih {$statusStr}";
            }
            
            return $this->response->setJSON([
                'exists' => false,
                'toast_type' => 'warning',
                'message' => $message
            ]);
        }

        return $this->response->setJSON([
            'exists' => true,
            'data' => [
                'id' => $perangkat['id'],
                'noreg' => $perangkat['noreg'],
                'nama' => $perangkat['nama']
            ]
        ]);
    }

    public function submit()
    {
        $perangkatModel = new PerangkatModel();
        $mutasiModel = new MutasiModel();

        $perangkatList = $this->request->getPost('perangkat');

        if (empty($perangkatList)) {
            return redirect()->back()->with('error', 'Belum ada perangkat yang ditambahkan!');
        }

        $mutasiIds = [];

        $adminSession = session()->get('admin');
        $updatedBy = $adminSession ? $adminSession['username'] : 'admin';

        foreach ($perangkatList as $pl) {
            $mutasiModel->insert([
                'id_perangkat' => $pl['id'],
                'noreg' => $pl['noreg'],
                'id_users' => $this->request->getPost('user'),
                'keterangan' => sanitize_utf8($this->request->getPost('keterangan')),
                'status' => 'Dibawa',
                'updated_by' => $updatedBy
            ]);

            $mutasiIds[] = $mutasiModel->getInsertID();

            $perangkatModel->update($pl['id'], [
                'status' => $this->mapStatusPerangkat('Dibawa')
            ]);
        }

        session()->set('mutasi_pdf_ids', $mutasiIds);

        return redirect()->to('/')->with('success', 'Data berhasil disimpan, Silakan konfirmasi ke Admin');
    }

    public function generatePdf()
    {
        $mutasiIds = session()->get('mutasi_pdf_ids');

        if (empty($mutasiIds)) {
            return redirect()->to('/')->with('error', 'Data PDF tidak tersedia, silakan submit ulang.');
        }

        // Remove from session after consuming
        session()->remove('mutasi_pdf_ids');

        $mutasiModel = new MutasiModel();
        $perangkatModel = new PerangkatModel();
        $userModel = new UserModel();

        // Fetch all mutasi records with related data
        $mutasiData = [];
        $userName = '';
        $keterangan = '';
        $tanggal = '';

        foreach ($mutasiIds as $id) {
            $mutasi = $mutasiModel->find($id);
            if ($mutasi) {
                $perangkat = $perangkatModel->find($mutasi['id_perangkat']);
                $user = $userModel->find($mutasi['id_users']);

                $mutasiData[] = [
                    'noreg' => $perangkat['noreg'] ?? '-',
                    'nama' => $perangkat['nama'] ?? '-',
                    'status' => $mutasi['status'] ?? '-',
                    'keterangan' => $mutasi['keterangan'] ?? '-',
                ];

                if (empty($userName) && $user) {
                    $userName = $user['nama'];
                }
                if (empty($keterangan)) {
                    $keterangan = $mutasi['keterangan'] ?? '-';
                }
                if (empty($tanggal)) {
                    $tanggal = date('d M Y, H:i', strtotime($mutasi['created_at']));
                }
            }
        }

        if (empty($mutasiData)) {
            return redirect()->to('/')->with('error', 'Data mutasi tidak ditemukan.');
        }

        // Encode logo to base64 for embedding in PDF
        $logoPath = FCPATH . 'images/Lintasarta.png';
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $ttdPath = FCPATH . 'images/TTD.png';
        $ttdBase64 = '';
        if (file_exists($ttdPath)) {
            $ttdBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdPath));
        }
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    font-size: 11px;
                    color: #2D3748;
                    background: #fff;
                }
                .container {
                    padding: 30px 40px;
                }
                .header {
                    border-bottom: 3px solid #1C4D8D;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                }
                .header-table {
                    width: 100%;
                }
                .header-table td {
                    vertical-align: middle;
                }
                .logo {
                    width: 140px;
                }
                .header-right {
                    text-align: right;
                }
                .doc-title {
                    font-size: 20px;
                    font-weight: bold;
                    color: #1C4D8D;
                    letter-spacing: 1px;
                }
                .doc-subtitle {
                    font-size: 10px;
                    color: #718096;
                    margin-top: 3px;
                }
                .info-section {
                    margin-bottom: 20px;
                    background: #F7FAFC;
                    border: 1px solid #E2E8F0;
                    border-radius: 6px;
                    padding: 15px 20px;
                }
                .info-table {
                    width: 100%;
                }
                .info-table td {
                    padding: 4px 0;
                    vertical-align: top;
                }
                .info-label {
                    font-weight: bold;
                    color: #4A5568;
                    width: 140px;
                }
                .info-value {
                    color: #2D3748;
                }
                .section-title {
                    font-size: 13px;
                    font-weight: bold;
                    color: #1C4D8D;
                    margin-bottom: 10px;
                    padding-bottom: 5px;
                    border-bottom: 2px solid #E2E8F0;
                }
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 25px;
                }
                .data-table thead th {
                    background: #1C4D8D;
                    color: #fff;
                    font-size: 10px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding: 10px 12px;
                    text-align: left;
                    border: 1px solid #1a4478;
                }
                .data-table tbody td {
                    padding: 9px 12px;
                    border: 1px solid #E2E8F0;
                    font-size: 10px;
                }
                .data-table tbody tr:nth-child(even) {
                    background: #F7FAFC;
                }
                .data-table tbody tr:hover {
                    background: #EBF4FF;
                }
                .text-center { text-align: center; }
                .status-badge {
                    display: inline-block;
                    background: #FEF3C7;
                    color: #92400E;
                    padding: 2px 10px;
                    border-radius: 10px;
                    font-size: 9px;
                    font-weight: bold;
                }
                .signature-section {
                    margin-top: 10px;
                    width: 100%;
                    table-layout: fixed;
                }
                .signature-section td {
                    width: 50%;
                    text-align: center;
                    vertical-align: top;
                    padding: 5px;
                }   
                .signature-label {
                    font-size: 10px;
                    color: #4A5568;
                    font-weight: bold;
                    margin-bottom: 5px; /* Kecilkan dari 60px ke 5px */
                }
                .signature-box {
                    height: 60px; /* Gunakan ini sebagai ruang tanda tangan */
                    line-height: 60px; /* Membantu gambar tegak lurus di tengah */
                }
                .signature-img {
                    max-height: 60px; /* Samakan dengan tinggi box */
                    max-width: 120px;
                    vertical-align: middle;
                }
                .signature-line {
                    border-top: 1px solid #2D3748;
                    width: 150px; /* Sedikit diperkecil agar tidak terlalu lebar */
                    margin: 0 auto;
                    padding-top: 5px;
                    font-size: 10px;
                    color: #4A5568;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 30px;
                    padding-top: 10px;
                    border-top: 2px solid #E2E8F0;
                    text-align: center;
                    font-size: 8px;
                    color: #A0AEC0;
                }
                .watermark {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-30deg);
                    font-size: 60px;
                    color: rgba(28, 77, 141, 0.04);
                    font-weight: bold;
                    letter-spacing: 10px;
                    z-index: -1;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <table class="header-table">
                        <tr>
                            <td>' . ($logoBase64 ? '<img src="' . $logoBase64 . '" class="logo">' : '<span class="doc-title">Lintasarta</span>') . '</td>
                            <td class="header-right">
                                <div class="doc-title">BUKTI REQUEST PERANGKAT</div>
                                <div class="doc-subtitle">Asset And Warehouse Management - Central Java & D.I.Y Operation</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="info-section">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Tanggal/Waktu</td>
                            <td class="info-value">: ' . esc($tanggal) . ' WIB</td>
                        </tr>
                        <tr>
                            <td class="info-label">Nama User</td>
                            <td class="info-value">: ' . esc($userName) . '</td>
                        </tr>
                        <tr>
                            <td class="info-label">Keterangan</td>
                            <td class="info-value">: ' . esc($keterangan) . '</td>
                        </tr>
                        <tr>
                            <td class="info-label">Jumlah Perangkat</td>
                            <td class="info-value">: ' . count($mutasiData) . ' unit</td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Daftar Perangkat</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">No</th>
                            <th>Nomor Registrasi</th>
                            <th>Nama Perangkat</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($mutasiData as $i => $item) {
            $html .= '
                            <tr>
                                <td class="text-center">' . ($i + 1) . '</td>
                                <td>' . esc($item['noreg']) . '</td>
                                <td>' . esc($item['nama']) . '</td>
                            </tr>';
        }

        $html .= '
                </tbody>
        </table>

                <table class="signature-section">
                <tr>
                    <td class="text-center">
            <div class="signature-label">Admin Warehouse</div>
            <div class="signature-box">
                ' . ($ttdBase64 ? '<img src="' . $ttdBase64 . '" class="signature-img">' : '<br><br><br>') . '
            </div>
            <div class="signature-line">Andhika Septa Prawira</div>
        </td>
        <td class="text-center">
            <div class="signature-label">Penerima</div>
            <div class="signature-box">
                <br><br><br>
            </div>
            <div class="signature-line">' . esc($userName) . '</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            &copy;' . date('Y') . ' PT. Aplikanusa Lintasarta
        </div>
            </div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'BRP_' . date('Ymd') . '_' . date('His') . '_' . str_replace(' ', '_', $userName) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    public function clearPdfSession()
    {
        session()->remove('mutasi_pdf_ids');
        return $this->response->setJSON(['status' => 'ok']);
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
    public function getDevicesDibawa($userId)
    {
        $db = \Config\Database::connect();
        
        // Subquery to get the latest mutasi ID for each perangkat
        $subQuery = $db->table('mutasi')
            ->select('MAX(id) as max_id')
            ->groupBy('id_perangkat')
            ->getCompiledSelect();

        $builder = $db->table('mutasi m');
        $builder->select('m.id as mutasi_id, p.noreg, p.nama');
        $builder->select('CASE WHEN EXISTS (SELECT 1 FROM return_requests rr WHERE rr.id_mutasi = m.id AND rr.status = \'Pending\') THEN 1 ELSE 0 END as is_pending', false);
        // Join to ensure we are only looking at the LATEST mutasi for the device
        $builder->join("($subQuery) latest", 'latest.max_id = m.id', 'inner');
        $builder->join('perangkat p', 'p.id = m.id_perangkat');
        
        $builder->where('m.id_users', $userId);
        $builder->where('m.status', 'Dibawa');
        
        // Return all Dibawa devices regardless of pending status, frontend will handle UI
        
        $devices = $builder->get()->getResultArray();
        
        return $this->response->setJSON($devices);
    }

    public function submitReturnRequest()
    {
        $mutasiIds = $this->request->getPost('mutasi_ids');
        
        if (empty($mutasiIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Belum ada perangkat yang dipilih.']);
        }
        
        $returnRequestModel = new \App\Models\ReturnRequestModel();
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        foreach ($mutasiIds as $mutasiId) {
            $returnRequestModel->insert([
                'id_mutasi' => $mutasiId,
                'status'    => 'Pending'
            ]);
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengirim request pengembalian.']);
        }
        
        return $this->response->setJSON(['success' => true, 'message' => 'Request pengembalian berhasil dikirim.']);
    }

    public function getNodes()
    {
        $nodeModel = new \App\Models\NodeModel();
        $nodes = $nodeModel->orderBy('arep', 'ASC')->orderBy('node_sentral', 'ASC')->findAll();
        
        // Group by arep
        $grouped = [];
        foreach ($nodes as $n) {
            $grouped[$n['arep']][] = $n['node_sentral'];
        }
        
        return $this->response->setJSON($grouped);
    }

    public function submitInstallationRequest()
    {
        $mutasiIds = $this->request->getPost('mutasi_ids');
        $arep = $this->request->getPost('arep');
        $nodeSentral = $this->request->getPost('node_sentral');
        
        if (empty($mutasiIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Belum ada perangkat yang dipilih.']);
        }
        
        if (empty($arep) || empty($nodeSentral)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Arep dan Node Sentral wajib dipilih.']);
        }
        
        $installationModel = new \App\Models\InstallationRequestModel();
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        foreach ($mutasiIds as $mutasiId) {
            $installationModel->insert([
                'id_mutasi'    => $mutasiId,
                'arep'         => $arep,
                'node_sentral' => $nodeSentral,
                'status'       => 'Pending'
            ]);
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengirim request pemasangan.']);
        }
        
        return $this->response->setJSON(['success' => true, 'message' => 'Request pemasangan berhasil dikirim.']);
    }
}
