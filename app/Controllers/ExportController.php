<?php
namespace App\Controllers;

use App\Models\PerangkatModel;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class ExportController extends BaseController
{

    private function getFilters(): array
    {
        return [
            'keyword'       => $this->request->getGet('keyword'),
            'status'        => $this->request->getGet('status'),
            'filter_mutasi' => $this->request->getGet('filter_mutasi'),
            'user'          => $this->request->getGet('user'),
        ];
    }

    public function exportExcel()
    {
        $perangkatModel = new PerangkatModel();
        $filters = $this->getFilters();
        $dataPerangkat = $perangkatModel->getFilteredAll($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nomor Register');
        $sheet->setCellValue('C1', 'Nama Perangkat');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Keterangan');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Created At');
        $sheet->setCellValue('H1', 'Updated At');
        $sheet->setCellValue('I1', 'Mutasi');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2854']],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $row = 2;
        $no = 1;
        foreach ($dataPerangkat as $perangkat) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $perangkat['noreg'] ?? '');
            $sheet->setCellValue('C' . $row, $perangkat['nama'] ?? '');
            $sheet->setCellValue('D' . $row, $perangkat['nama_user'] ?? '-');
            $sheet->setCellValue('E' . $row, $perangkat['keterangan_mutasi'] ?? '-');
            $sheet->setCellValue('F' . $row, $perangkat['status_mutasi'] ?? '-');
            $sheet->setCellValue('G' . $row, isset($perangkat['created_at']) ? date('Y-m-d', strtotime($perangkat['created_at'])) : '-');
            $sheet->setCellValue('H' . $row, isset($perangkat['mutasi_updated']) ? date('Y-m-d', strtotime($perangkat['mutasi_updated'])) : '-');

            $mutasiLabel = '-';
            if (in_array($perangkat['status_mutasi'] ?? '', ['Terpasang', 'Terkirim'])) {
                $mutasiLabel = ($perangkat['mutasi_check'] ?? 0) == 1 ? 'Checked' : 'Crosscheck INTAN';
            } elseif (($perangkat['mutasi_check'] ?? 0) == 0) {
                $mutasiLabel = 'Belum Mutasi';
            }
            $sheet->setCellValue('I' . $row, $mutasiLabel);

            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $filename = 'report_perangkat';
        if (!empty($filters['status'])) $filename .= '_' . $filters['status'];
        if (!empty($filters['user'])) $filename .= '_user' . $filters['user'];
        $filename .= '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function exportPdf()
    {
        $perangkatModel = new PerangkatModel();
        $filters = $this->getFilters();
        $dataPerangkat = $perangkatModel->getFilteredAll($filters);

        $filterLabels = [];
        if (!empty($filters['keyword'])) $filterLabels[] = 'Keyword: ' . htmlspecialchars($filters['keyword']);
        if (!empty($filters['status'])) $filterLabels[] = 'Status: ' . htmlspecialchars($filters['status']);
        if (!empty($filters['filter_mutasi'])) $filterLabels[] = 'Filter Mutasi: ' . htmlspecialchars($filters['filter_mutasi']);
        if (!empty($filters['user'])) $filterLabels[] = 'User ID: ' . htmlspecialchars($filters['user']);

        $filterText = !empty($filterLabels) ? implode(' | ', $filterLabels) : 'Semua Data';

        $html = '
        <style>
            body { font-family: sans-serif; font-size: 10px; }
            h1 { font-size: 16px; margin-bottom: 4px; }
            .filter-info { font-size: 9px; color: #666; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #0F2854; color: white; padding: 6px 4px; text-align: left; font-size: 9px; }
            td { padding: 5px 4px; font-size: 9px; border-bottom: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f5f5f5; }
        </style>';

        $html .= '<h1>Data Perangkat</h1>';
        $html .= '<div class="filter-info">Filter: ' . $filterText . ' | Total: ' . count($dataPerangkat) . ' data | Tanggal: ' . date('d-m-Y H:i') . '</div>';
        $html .= '<table>';
        $html .= '<tr><th>No</th><th>No Registrasi</th><th>Nama Perangkat</th><th>User</th><th>Keterangan</th><th>Status</th><th>Created</th><th>Updated</th><th>Mutasi</th></tr>';

        $no = 1;
        foreach ($dataPerangkat as $perangkat) {
            $mutasiLabel = '-';
            if (in_array($perangkat['status_mutasi'] ?? '', ['Terpasang', 'Terkirim'])) {
                $mutasiLabel = ($perangkat['mutasi_check'] ?? 0) == 1 ? 'Checked' : 'Crosscheck INTAN';
            } elseif (($perangkat['mutasi_check'] ?? 0) == 0) {
                $mutasiLabel = 'Belum Mutasi';
            }

            $html .= '<tr>';
            $html .= '<td>' . $no++ . '</td>';
            $html .= '<td>' . htmlspecialchars($perangkat['noreg'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($perangkat['nama'] ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($perangkat['nama_user'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($perangkat['keterangan_mutasi'] ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($perangkat['status_mutasi'] ?? '-') . '</td>';
            $html .= '<td>' . (isset($perangkat['created_at']) ? date('Y-m-d', strtotime($perangkat['created_at'])) : '-') . '</td>';
            $html .= '<td>' . (isset($perangkat['mutasi_updated']) ? date('Y-m-d', strtotime($perangkat['mutasi_updated'])) : '-') . '</td>';
            $html .= '<td>' . $mutasiLabel . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'report_perangkat';
        if (!empty($filters['status'])) $filename .= '_' . $filters['status'];
        $filename .= '_' . date('Ymd') . '.pdf';

        $dompdf->stream($filename);
    }
}
