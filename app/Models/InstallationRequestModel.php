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
    protected $allowedFields = ['id_mutasi', 'arep', 'node_sentral', 'status', 'is_read'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getPendingRequests()
    {
        $builder = $this->db->table($this->table . ' ir');
        $builder->select('ir.id as request_id, m.id as mutasi_id, u.nama as nama_user, p.noreg, p.nama as nama_perangkat, ir.arep, ir.node_sentral, ir.created_at, ir.is_read');
        $builder->join('mutasi m', 'm.id = ir.id_mutasi');
        $builder->join('users u', 'u.id = m.id_users', 'left');
        $builder->join('perangkat p', 'p.id = m.id_perangkat', 'left');
        $builder->where('ir.status', 'Pending');
        $builder->orderBy('ir.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getPendingRequestsGrouped()
    {
        $raw = $this->getPendingRequests();
        $grouped = [];
        
        foreach ($raw as $r) {
            // Group by user and exact timestamp
            $key = md5($r['nama_user'] . '_' . $r['created_at']);
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'group_id' => $key,
                    'nama_user' => $r['nama_user'],
                    'created_at' => $r['created_at'],
                    'is_read' => true,
                    'devices' => []
                ];
            }
            
            // If any device in the group is unread, the whole group is unread
            if ($r['is_read'] === 'f' || $r['is_read'] === false || $r['is_read'] === '0') {
                $grouped[$key]['is_read'] = false;
            }

            $grouped[$key]['devices'][] = [
                'request_id' => $r['request_id'],
                'noreg' => $r['noreg'],
                'nama_perangkat' => $r['nama_perangkat'],
                'arep' => $r['arep'],
                'node_sentral' => $r['node_sentral']
            ];
        }
        
        $result = array_values($grouped);
        
        // Sort: is_read ASC (false/new first), then created_at DESC
        usort($result, function($a, $b) {
            if ($a['is_read'] === $b['is_read']) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            }
            return $a['is_read'] ? 1 : -1;
        });
        
        return $result;
    }
}
