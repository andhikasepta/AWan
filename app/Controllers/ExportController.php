<?php
namespace App\Controllers;

use App\Models\PerangkatModel;
use App\Models\UserModel;
use App\Models\MutasiModel;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class ExportController extends BaseController
{
    public function exportExcel()
    {
        $perangkatModel = new PerangkatModel();
        $dataPerangkat = $perangkatModel->select('perangkat.*, users.nama as nama_user, mutasi.keterangan as info_mutasi, mutasi.status as status_mutasi')
            ->join('users', 'users.id = perangkat.id', 'left')
            ->join('mutasi', 'mutasi.id = (SELECT MAX(id) FROM mutasi WHERE mutasi.id = perangkat.id)', 'left')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nomor Register');
        $sheet->setCellValue('C1', 'Nama Perangkat');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Keterangan');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Created At');
        $sheet->setCellValue('H1', 'Updated At');
        $sheet->setCellValue('I1', 'Mutasi');

        $row = 2;
        foreach ($dataPerangkat as $perangkat) {
            $sheet->setCellValue('A' . $row, $perangkat['id']);
            $sheet->setCellValue('B' . $row, $perangkat['noreg']);
            $sheet->setCellValue('C' . $row, $perangkat['nama']);
            $sheet->setCellValue('D' . $row, $perangkat['nama_user']);
            $sheet->setCellValue('E' . $row, $perangkat['info_mutasi']);
            $sheet->setCellValue('F' . $row, $perangkat['status_mutasi']);
            $sheet->setCellValue('G' . $row, date('Y-m-d', strtotime($perangkat['created_at'])));
            $sheet->setCellValue('H' . $row, date('Y-m-d', strtotime($perangkat['updated_at'])));
            $sheet->setCellValue('I' . $row, $perangkat['info_mutasi']);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report.xlsx"');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
    public function exportPdf()
    {
        $perangkatModel = new PerangkatModel();
        $dataPerangkat = $perangkatModel->select('perangkat.*, users.nama as nama_user, mutasi.keterangan as info_mutasi, mutasi.status as status_mutasi')
            ->join('users', 'users.id = perangkat.id', 'left')
            ->join('mutasi', 'mutasi.id = (SELECT MAX(id) FROM mutasi WHERE mutasi.id = perangkat.id)', 'left')
            ->findAll();

        $html = '<h1>Data Perangkat</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>ID</th><th>Nomor Register</th><th>Nama Perangkat</th><th>User</th><th>Keterangan Mutasi</th><th>Status Mutasi</th><th>Created At</th><th>Updated At</th></tr>';

        foreach ($dataPerangkat as $perangkat) {
            $html .= '<tr>';
            $html .= '<td>' . $perangkat['id'] . '</td>';
            $html .= '<td>' . $perangkat['noreg'] . '</td>';
            $html .= '<td>' . $perangkat['nama'] . '</td>';
            $html .= '<td>' . $perangkat['nama_user'] . '</td>';
            $html .= '<td>' . $perangkat['info_mutasi'] . '</td>';
            $html .= '<td>' . $perangkat['status_mutasi'] . '</td>';
            $html .= '<td>' . date('Y-m-d', strtotime($perangkat['created_at'])) . '</td>';
            $html .= '<td>' . date('Y-m-d', strtotime($perangkat['updated_at'])) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('report.pdf');
    }
}


