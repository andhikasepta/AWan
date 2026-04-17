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
  protected $allowedFields    = ['id_perangkat', 'id_users', 'status', 'keterangan', 'is_checked', 'checked_at'];

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

  public function getAllHistory($filters = [], $limit = 50, $offset = 0)
  {
    $builder = $this->db->table('mutasi m');

    $subQuery = $this->db->table('mutasi')
    ->select('id_perangkat, MAX(updated_at) as latest')
    ->groupBy('id_perangkat')
    ->getCompiledSelect();

    $builder=$this->db->table('mutasi m');
    $builder->select('
      m.*,
      u.nama as nm_user,
      p.noreg,
      sp.nama_perangkat as nm_perangkat
    ');

    $builder->join("($subQuery) latest_data",
    'm.id_perangkat = latest_data.id_perangkat
    AND m.updated_at = latest_data.latest');

    $builder->join('users u', 'u.id=m.id_users', 'left');
    $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
    $builder->join('spec_perangkat sp', 'sp.id=p.id_spec', 'left');

    if (!empty($filters['search'])) {
      $keyword = $filters['search'];

      $builder->groupStart()
      ->like('u.nama', $keyword)
      ->orLike('sp.nama_perangkat', $keyword)
      ->orLike('p.noreg', $keyword)
      ->orLike('m.status', $keyword)
      ->orLike('m.keterangan', $keyword)
      ->groupEnd();
    }

    if (!empty($filters['status'])){
      $builder->where('m.status', $filters['status']);
    }
    
    if (!empty($filters['user'])){
      $builder->where('m.id_users', $filters['user']);
    }

    switch ($filters['filter_mutasi'] ?? ''){
      case 'belum':
        $builder->where([
          'm.status !=' => 'Terpasang',
          'm.is_checked'=> 0
        ]);
        break;

      case 'crosscheck':
        $builder->where([
          'm.status'=>'Terpasang',
          'm.is_checked'=> 0
        ]);
        break;

      case 'check':
        $builder->where('m.is_checked', 1);
        break;
    }

    $countBuilder = clone $builder;
    $total = $countBuilder->countAllResults();

    $data = $builder->orderBy('m.created_at', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();

    return [
      'data' => $data,
      'total' => $total
    ];
  }

  public function getDataHistory($id, $filters = [], $limit = 15, $offset = 0)
  {
    $builder = $this->db->table('mutasi m');
    $builder->select('m.*, u.nama as nm_user');
    $builder->join('users u', 'u.id = m.id_users', 'left');
    $builder->where('m.id_perangkat', $id);

    if (!empty($filters['searchHistory'])) {
      $keyword = $filters['searchHistory'];
      $fields = ['u.nama', 'm.status', 'm.keterangan'];

      $builder->groupStart();
      foreach ($fields as $field) {
        $builder->orLike($field, $keyword);
      }
      $builder->groupEnd();
    }

    $countBuilder = clone $builder;
    $total = $countBuilder->countAllResults(false);

    $builder->orderBy('m.created_at', 'DESC');
    $builder->limit($limit, $offset);

    $data = $builder->get()->getResultArray();

    return [
      'data' => $data,
      'total' => $total
    ];
  }
}
