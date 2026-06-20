<?php

namespace App\Models;

use CodeIgniter\Model;

class PerangkatModel extends Model
{
    protected $table = 'perangkat';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_spec', 'kode_id', 'nama', 'noreg', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getDataDash($filters = [], $limit, $offset)
    {
        $where = [];

        if (!empty($filters['keyword'])) {
            $keywords = explode(';', $filters['keyword']);
            $keywordConditions = [];
            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $escaped_kw = strtolower($this->db->escapeLikeString(sanitize_utf8($kw)));
                    $keywordConditions[] = "(LOWER(p.noreg) LIKE '%$escaped_kw%' OR 
                                LOWER(p.nama) LIKE '%$escaped_kw%' OR 
                                LOWER(u.nama) LIKE '%$escaped_kw%' OR
                                LOWER(m.status) LIKE '%$escaped_kw%' OR
                                LOWER(m.keterangan) LIKE '%$escaped_kw%' OR
                                LOWER(m.is_checked::text) LIKE '%$escaped_kw%')";
                }
            }
            if (!empty($keywordConditions)) {
                $where[] = '(' . implode(' OR ', $keywordConditions) . ')';
            }
        }

        if (!empty($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $where[] = "m.status = $status";
        }

        if (!empty($filters['user'])) {
            $user = (int) $filters['user'];
            $where[] = "m.id_users = $user";
        }

        if (!empty($filters['filter_mutasi'])) {
            if ($filters['filter_mutasi'] == 'belum') {
                $where[] = "(m.status NOT IN ('Terpasang', 'Terkirim') OR m.status IS NULL)";
            } elseif ($filters['filter_mutasi'] == 'crosscheck') {
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 0)";
            } elseif ($filters['filter_mutasi'] == 'check') {
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 1)";
            }
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        // Server-side sorting
        $sortMap = [
            'noreg'      => 'p.noreg',
            'nama'       => 'p.nama',
            'user'       => 'u.nama',
            'keterangan' => 'm.keterangan',
            'status'     => 'm.status',
            'created'    => 'p.created_at',
            'updated'    => 'm.updated_at',
            'mutasi'     => 'm.is_checked',
        ];

        $orderSql = 'p.id ASC';
        if (!empty($filters['sort_by']) && isset($sortMap[$filters['sort_by']])) {
            $sortCol = $sortMap[$filters['sort_by']];
            $sortDir = (!empty($filters['sort_dir']) && strtolower($filters['sort_dir']) === 'desc') ? 'DESC' : 'ASC';
            $orderSql = "$sortCol $sortDir";
        }

        $data = $this->db->query("
        SELECT
        p.*,
        m.id as mutasi_id,
        m.status as status_mutasi,
        m.keterangan as keterangan_mutasi,
        m.created_at as mutasi_created,
        m.updated_at as mutasi_updated,
        m.is_checked as mutasi_check,
        m.updated_by as mutasi_updated_by,
        u.nama as nama_user
        FROM perangkat p
        LEFT JOIN (
            SELECT MAX(id) as max_id, id_perangkat
            FROM mutasi
            GROUP BY id_perangkat
        ) latest_mutasi ON latest_mutasi.id_perangkat = p.id
        LEFT JOIN mutasi m ON m.id = latest_mutasi.max_id
        LEFT JOIN users u ON u.id = m.id_users
        $whereSql
        ORDER BY $orderSql
        LIMIT $limit OFFSET $offset
        ")->getResultArray();

        $total = $this->db->query("
        SELECT COUNT(*) as total
        FROM perangkat p
        LEFT JOIN (
            SELECT MAX(id) as max_id, id_perangkat
            FROM mutasi
            GROUP BY id_perangkat
        ) latest_mutasi ON latest_mutasi.id_perangkat = p.id
        LEFT JOIN mutasi m ON m.id = latest_mutasi.max_id
        LEFT JOIN users u ON u.id = m.id_users
        $whereSql")->getRow()->total;

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    public function getFilteredAll($filters = [])
    {
        $where = [];

        if (!empty($filters['keyword'])) {
            $keyword = strtolower($this->db->escapeLikeString(sanitize_utf8($filters['keyword'])));
            $where[] = "(LOWER(p.noreg) LIKE '%$keyword%' OR 
                        LOWER(p.nama) LIKE '%$keyword%' OR 
                        LOWER(u.nama) LIKE '%$keyword%' OR
                        LOWER(m.status) LIKE '%$keyword%' OR
                        LOWER(m.keterangan) LIKE '%$keyword%' OR
                        LOWER(m.is_checked::text) LIKE '%$keyword%')";
        }

        if (!empty($filters['status'])) {
            $status = $this->db->escape($filters['status']);
            $where[] = "m.status = $status";
        }

        if (!empty($filters['user'])) {
            $user = (int) $filters['user'];
            $where[] = "m.id_users = $user";
        }

        if (!empty($filters['filter_mutasi'])) {
            if ($filters['filter_mutasi'] == 'belum') {
                $where[] = "(m.status NOT IN ('Terpasang', 'Terkirim') OR m.status IS NULL)";
            } elseif ($filters['filter_mutasi'] == 'crosscheck') {
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 0)";
            } elseif ($filters['filter_mutasi'] == 'check') {
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 1)";
            }
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        return $this->db->query("
        SELECT
        p.*,
        m.id as mutasi_id,
        m.status as status_mutasi,
        m.keterangan as keterangan_mutasi,
        m.created_at as mutasi_created,
        m.updated_at as mutasi_updated,
        m.is_checked as mutasi_check,
        m.updated_by as mutasi_updated_by,
        u.nama as nama_user
        FROM perangkat p
        LEFT JOIN (
            SELECT MAX(id) as max_id, id_perangkat
            FROM mutasi
            GROUP BY id_perangkat
        ) latest_mutasi ON latest_mutasi.id_perangkat = p.id
        LEFT JOIN mutasi m ON m.id = latest_mutasi.max_id
        LEFT JOIN users u ON u.id = m.id_users
        $whereSql
        ORDER BY p.id ASC
        ")->getResultArray();
    }

    public function getDetailMutasi($id)
    {
        return $this->db->query("
        SELECT
        p.id,
        p.noreg,
        p.nama,
        m.id_users,
        m.status,
        m.keterangan,
        m.is_checked,
        m.updated_by,
        u.nama as nama_user
        FROM perangkat p
        LEFT JOIN (
            SELECT MAX(id) as max_id, id_perangkat
            FROM mutasi
            GROUP BY id_perangkat
        ) latest_mutasi ON latest_mutasi.id_perangkat = p.id
        LEFT JOIN mutasi m ON m.id = latest_mutasi.max_id
        LEFT JOIN users u on u.id = m.id_users
        WHERE p.id =?",
            [$id]
        )->getRowArray();
    }

}
