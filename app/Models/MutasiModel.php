<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiModel extends Model
{
    protected $table            = 'mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_perangkat', 'id_users', 'status','keterangan','is_checked','checked_at'];

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
    
    public function getDataHistory($id, $filters =[], $limit = 10, $offset = 0)
    {
      $builder = $this->db->table('mutasi m');
      $builder->select('m.*, u.nama as nm_user');
      $builder->join('users u', 'u.id = m.id_users', 'left');
      $builder->where('m.id_perangkat', $id);

      if(!empty($filters['searchHistory'])){
        $builder->groupStart()
                ->like('u.nama', $filters['searchHistory'])
                ->orLike('m.status', $filters['searchHistory'])
                ->orLike('m.keterangan', $filters['searchHistory'])
                ->groupEnd();
      }

      $countBuilder = clone $builder;
      $total = $countBuilder->countAllResults(false);

      $builder->orderBy('m.created_at', 'DESC');
      $builder->limit($limit, $offset);

      $data = $builder->get()->getResultArray();

      return [
        'data'=>$data,
        'total'=>$total
      ];
    }
}