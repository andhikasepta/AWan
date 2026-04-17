<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpecPerangkat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'unsigned'=>true,
                'auto_increment'=>true,
            ],
            'kode_spec'=>[
                'type'=>'VARCHAR',
                'constraint'=>50,
            ],
            'nama_perangkat'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('spec_perangkat');
    }

    public function down()
    {
        $this->forge->dropTable('spec_perangkat');
    }
}
