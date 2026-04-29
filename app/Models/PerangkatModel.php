<?php

namespace App\Models;

use CodeIgniter\Model;

class PerangkatModel extends Model
{
    protected $table            = 'perangkat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_spec', 'kode_id', 'nama', 'noreg', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getDataDash($filters = [], $limit, $offset)
    {
        $where = [];

        if (!empty($filters['keyword'])){
            $keyword = $this->db->escapeLikeString(sanitize_utf8($filters['keyword']));
            $where[] = "(p.noreg LIKE '%$keyword%' OR 
                        p.nama LIKE '%$keyword%' OR 
                        u.nama LIKE '%$keyword%' OR
                        m.status LIKE '%$keyword%' OR
                        m.keterangan LIKE '%$keyword%' OR
                        m.is_checked::text LIKE '%$keyword%')";
        }

        if (!empty($filters['status'])){
            $status = $this->db->escape($filters['status']);
            $where[] = "m.status = $status"; 
        }

        if (!empty($filters['user'])){
            $user = (int)$filters['user'];
            $where[] = "m.id_users = $user"; 
        }

        if (!empty($filters['filter_mutasi'])){
            if ($filters['filter_mutasi']=='belum'){
                $where[] = "(m.status NOT IN ('Terpasang', 'Terkirim') OR m.status IS NULL)";
            }

            elseif ($filters['filter_mutasi']=='crosscheck'){
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 0)";
            }

            elseif ($filters['filter_mutasi']=='check'){
                $where[] = "(m.status IN ('Terpasang', 'Terkirim') AND m.is_checked = 1)";
            }
        }

        $whereSql = '';
        if (!empty($where)){
            $whereSql = 'WHERE ' . implode(' AND ', $where);
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
        u.nama as nama_user
        FROM perangkat p
        LEFT JOIN mutasi m ON m.id = (
        SELECT id FROM mutasi
        WHERE id_perangkat = p.id
        ORDER BY created_at DESC
        LIMIT 1
        )
        LEFT JOIN users u ON u.id = m.id_users
        $whereSql
        ORDER BY p.id ASC
        LIMIT $limit OFFSET $offset
        ")->getResultArray();

        $total = $this->db->query("
        SELECT COUNT(*) as total
        FROM perangkat p
        LEFT JOIN mutasi m ON m.id = (
        SELECT id FROM mutasi
        WHERE id_perangkat = p.id
        ORDER BY created_at DESC
        LIMIT 1)
        LEFT JOIN users u ON u.id = m.id_users
        $whereSql")->getRow()->total;

        return [
            'data'=>$data,
            'total'=>$total
        ];
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
        u.nama as nama_user
        FROM perangkat p
        LEFT JOIN mutasi m ON m.id = (
        SELECT id FROM mutasi
        WHERE id_perangkat = p.id
        ORDER BY created_at DESC
        LIMIT 1)
        LEFT JOIN users u on u.id = m.id_users
        WHERE p.id =?",
        [$id])->getRowArray();
    }
        
}
