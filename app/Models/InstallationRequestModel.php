<?php

namespace App\Models;

use CodeIgniter\Model;

class InstallationRequestModel extends Model
{
    protected $table = 'installation_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_mutasi', 'arep', 'site_sentral', 'node_sentral', 'status', 'is_read', 'qty'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPendingRequests($adminRegion = null, $adminArea = null)
    {
        $builder = $this->db->table($this->table . ' ir');
        $builder->select('ir.id as request_id, m.id as mutasi_id, u.nama as nama_user, p.noreg, p.nama as nama_perangkat, nr.kode_spec as nr_noreg, nr.nama_material as nr_nama, ir.arep, ir.site_sentral, ir.node_sentral, ir.created_at, ir.is_read, ir.qty');
        $builder->join('mutasi m', 'm.id = ir.id_mutasi');
        $builder->join('users u', 'u.id = m.id_users', 'left');
        $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
        $builder->join('non_registration nr', 'nr.id = m.id_non_reg', 'left');
        $builder->where('ir.status', 'Pending');

        if ($adminRegion && $adminArea) {
            $builder->groupStart();
            $adminRegions = explode(',', $adminRegion);
            foreach ($adminRegions as $r) {
                $builder->orLike('u.region', trim($r), 'both');
            }
            $builder->groupEnd();

            $builder->groupStart();
            $adminAreas = explode(',', $adminArea);
            foreach ($adminAreas as $a) {
                $builder->orLike('u.area', trim($a), 'both');
            }
            $builder->groupEnd();
        }

        $builder->orderBy('ir.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getPendingRequestsGrouped($adminRegion = null, $adminArea = null)
    {
        $raw = $this->getPendingRequests($adminRegion, $adminArea);
        $grouped = [];
        
        foreach ($raw as $r) {
            $minuteTimestamp = substr($r['created_at'], 0, 16);
            $key = md5($r['nama_user'] . '_' . $minuteTimestamp);
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'group_id' => $key,
                    'nama_user' => $r['nama_user'],
                    'created_at' => $r['created_at'],
                    'is_read' => true,
                    'devices' => []
                ];
            }
            
            if ($r['is_read'] == 0 || $r['is_read'] === 'f' || $r['is_read'] === false) {
                $grouped[$key]['is_read'] = false;
            }

            $grouped[$key]['devices'][] = [
                'request_id' => $r['request_id'],
                'mutasi_id' => $r['mutasi_id'],
                'noreg' => !empty($r['noreg']) ? $r['noreg'] : $r['nr_noreg'],
                'nama_perangkat' => !empty($r['nama_perangkat']) ? $r['nama_perangkat'] : $r['nr_nama'],
                'arep' => $r['arep'],
                'site_sentral' => $r['site_sentral'],
                'node_sentral' => $r['node_sentral'],
                'qty' => $r['qty']
            ];
        }
        
        $result = array_values($grouped);
        
        usort($result, function($a, $b) {
            if ($a['is_read'] === $b['is_read']) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            }
            return $a['is_read'] ? 1 : -1;
        });
        
        return $result;
    }
}
