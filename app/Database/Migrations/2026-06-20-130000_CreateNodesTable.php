<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNodesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'arep' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'node_sentral' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('nodes');

        // Seed initial data
        $db = \Config\Database::connect();
        $builder = $db->table('nodes');
        $initialData = [
            [
                'arep'         => 'Semarang',
                'node_sentral' => 'SMGHCPGA01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Semarang',
                'node_sentral' => 'UNRIDSXR01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Semarang',
                'node_sentral' => 'SMGHCPHW04',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Tegal',
                'node_sentral' => 'WLRMTIHW01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Solo',
                'node_sentral' => 'SLGHCPGA01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Yogyakarta',
                'node_sentral' => 'YKGHCPGA01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'arep'         => 'Purwokerto',
                'node_sentral' => 'PWTIDSXR01',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
        ];
        $builder->insertBatch($initialData);
    }

    public function down()
    {
        $this->forge->dropTable('nodes');
    }
}
