<?php

namespace App\Models;

use CodeIgniter\Model;

class BrpModel extends Model
{
    protected $table            = 'brp_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'filename',
        'user_name',
        'generated_number',
        'period_month',
        'period_year',
        'mutasi_ids',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;

    /**
     * Get the next sequential BRP number for a given month/year period.
     * Atomically increments the counter using INSERT ... ON CONFLICT (upsert).
     *
     * @param int $month
     * @param int $year
     * @return int The next number (1-based)
     */
    public function getNextNumber(int $month, int $year): int
    {
        $db = \Config\Database::connect();

        $sql = "INSERT INTO brp_counter (period_month, period_year, last_number)
                VALUES (?, ?, 1)
                ON CONFLICT (period_month, period_year)
                DO UPDATE SET last_number = brp_counter.last_number + 1
                RETURNING last_number";

        $result = $db->query($sql, [$month, $year]);
        $row = $result->getRowArray();

        return (int) $row['last_number'];
    }

    /**
     * Get all BRP documents for a given month/year.
     *
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getByMonth(int $month, int $year): array
    {
        return $this->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('generated_number', 'ASC')
            ->findAll();
    }

    /**
     * Get available months/years that have BRP documents.
     *
     * @return array
     */
    public function getAvailableMonths(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('brp_documents');
        $builder->select('period_month, period_year, COUNT(*) as total');
        $builder->groupBy('period_month, period_year');
        $builder->orderBy('period_year', 'DESC');
        $builder->orderBy('period_month', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Save a BRP document record.
     *
     * @param array $data
     * @return int|false Insert ID or false on failure
     */
    public function saveDocument(array $data)
    {
        $this->insert($data);
        return $this->getInsertID();
    }

    /**
     * Format the generated number as a zero-padded 5-digit string.
     *
     * @param int $number
     * @return string e.g. "00001"
     */
    public static function formatNumber(int $number): string
    {
        return str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the BRP filename.
     *
     * @param string $userName e.g. "Khoerul Faizin"
     * @param int    $number   e.g. 1
     * @return string e.g. "BRP_24062026_00001_KhoerulFaizin.pdf"
     */
    public static function generateFilename(string $userName, int $number): string
    {
        $dateStr = date('dmY');
        $numStr  = self::formatNumber($number);
        $nameStr = str_replace(' ', '', $userName);

        return "BRP_{$dateStr}_{$numStr}_{$nameStr}.pdf";
    }
}
