<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionAreaToAdmin extends Migration
{
    public function up()
    {
        $fields = [
            'region' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('admin', 'region');
        $this->forge->dropColumn('admin', 'area');
    }
}
