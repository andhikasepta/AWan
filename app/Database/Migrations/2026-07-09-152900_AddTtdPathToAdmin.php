<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTtdPathToAdmin extends Migration
{
    public function up()
    {
        $fields = [
            'ttd_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('admin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('admin', 'ttd_path');
    }
}
