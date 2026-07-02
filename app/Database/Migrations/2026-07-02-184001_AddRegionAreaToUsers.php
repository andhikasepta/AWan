<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionAreaToUsers extends Migration
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
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'region');
        $this->forge->dropColumn('users', 'area');
    }
}
