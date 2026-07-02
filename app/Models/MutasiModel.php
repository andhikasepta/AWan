<?php

namespace App\Models;

use CodeIgniter\Model;

helper('utf8');

class MutasiModel extends Model
{
  protected $table = 'mutasi';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $returnType = 'array';
  protected $useSoftDeletes = false;
  protected $protectFields = true;
  protected $allowedFields = ['id_perangkat', 'id_users', 'status', 'keterangan', 'is_checked', 'checked_at', 'updated_by', 'id_non_reg', 'qty'];

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

  public function getAllHistory($filters = [], $limit = 50, $offset = 0)
  {
    $builder = $this->db->table('mutasi m');

    $subQuery = $this->db->table('mutasi')
      ->select('MAX(id) as latest_id')
      ->groupBy('COALESCE(id_perangkat, -id_non_reg)')
      ->getCompiledSelect();

    $builder = $this->db->table('mutasi m');
    $builder->select('
      m.*,
      u.nama as nm_user,
      COALESCE(p.noreg, nr.kode_spec) as noreg,
      COALESCE(sp.nama_perangkat, p.nama, nr.nama_material) as nm_perangkat
    ');

    $builder->join(
      "($subQuery) latest_data",
      'm.id = latest_data.latest_id'
    );

    $builder->join('users u', 'u.id=m.id_users', 'left');
    $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
    $builder->join('spec_perangkat sp', 'sp.id=p.id_spec', 'left');
    $builder->join('non_registration nr', 'nr.id = m.id_non_reg', 'left');

    if (!empty($filters['type'])) {
      if ($filters['type'] === 'nonreg') {
        $builder->where('m.id_non_reg IS NOT NULL', null, false);
      } else {
        $builder->where('m.id_perangkat IS NOT NULL', null, false);
      }
    }

    if (!empty($filters['search'])) {
      $keywords = explode(';', $filters['search']);
      $builder->groupStart();
      foreach ($keywords as $kw) {
        $kw = trim($kw);
        if ($kw !== '') {
          $escaped_kw = $this->db->escapeLikeString(sanitize_utf8($kw));
          $builder->orGroupStart()
            ->where("u.nama ILIKE '%$escaped_kw%'", null, false)
            ->orWhere("sp.nama_perangkat ILIKE '%$escaped_kw%'", null, false)
            ->orWhere("p.nama ILIKE '%$escaped_kw%'", null, false)
            ->orWhere("p.noreg ILIKE '%$escaped_kw%'", null, false)
            ->orWhere("m.status ILIKE '%$escaped_kw%'", null, false)
            ->orWhere("m.keterangan ILIKE '%$escaped_kw%'", null, false)
            ->groupEnd();
        }
      }
      $builder->groupEnd();
    }

    if (!empty($filters['status'])) {
      $builder->where('m.status', $filters['status']);
    }

    if (!empty($filters['user'])) {
      $builder->where('m.id_users', $filters['user']);
    }

    switch ($filters['filter_mutasi'] ?? '') {
      case 'belum':
        $builder->whereNotIn('m.status', ['Terpasang', 'Terkirim']);
        $builder->where('m.is_checked', 0);
        break;

      case 'crosscheck':
        $builder->whereIn('m.status', ['Terpasang', 'Terkirim']);
        $builder->where('m.is_checked', 0);
        break;

      case 'check':
        $builder->where('m.is_checked', 1);
        break;
    }

    if (!empty($filters['admin_region']) && !empty($filters['admin_area'])) {
        $builder->groupStart()
                ->where('u.id IS NULL', null, false)
                ->orGroupStart()
                    ->where('u.region', $filters['admin_region'])
                    ->where('u.area', $filters['admin_area'])
                ->groupEnd()
                ->groupEnd();
    }

    $countBuilder = clone $builder;
    $total = $countBuilder->countAllResults();

    // Server-side sorting
    $sortMap = [
        'noreg'      => 'p.noreg',
        'nama'       => 'p.nama',
        'user'       => 'u.nama',
        'keterangan' => 'm.keterangan',
        'status'     => 'm.status',
        'created'    => 'm.created_at',
        'updated'    => 'm.updated_at',
        'mutasi'     => 'm.is_checked',
    ];

    $sortCol = 'm.created_at';
    $sortDir = 'DESC';
    if (!empty($filters['sort_by']) && isset($sortMap[$filters['sort_by']])) {
        $sortCol = $sortMap[$filters['sort_by']];
        $sortDir = (!empty($filters['sort_dir']) && strtolower($filters['sort_dir']) === 'desc') ? 'DESC' : 'ASC';
    }

    $data = $builder->orderBy($sortCol, $sortDir)
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
      $keyword = sanitize_utf8($filters['searchHistory']);
      $fields = ['u.nama', 'm.status', 'm.keterangan'];

      $builder->groupStart();
      foreach ($fields as $field) {
        $builder->orWhere("$field ILIKE '%$keyword%'", null, false);
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

  public function getNonRegHistory($id, $filters = [], $limit = 15, $offset = 0)
  {
    $builder = $this->db->table('mutasi m');
    $builder->select('m.*, u.nama as nm_user');
    $builder->join('users u', 'u.id = m.id_users', 'left');
    $builder->where('m.id_non_reg', $id);

    if (!empty($filters['searchHistory'])) {
      $keyword = sanitize_utf8($filters['searchHistory']);
      $fields = ['u.nama', 'm.status', 'm.keterangan'];

      $builder->groupStart();
      foreach ($fields as $field) {
        $builder->orWhere("$field ILIKE '%$keyword%'", null, false);
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
