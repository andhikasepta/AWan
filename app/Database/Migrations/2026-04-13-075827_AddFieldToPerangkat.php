<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldToPerangkat extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perangkat', [
            'id_spec'=>[
                'type'=>'INT',
                'null'=>true,
                'unsigned'=>true,
                'after'=>'id',
            ],
            'kode_id'=>[
                'type'=>'VARCHAR',
                'constraint'=>10,
                'null'=>true,
                'after'=>'id_spec',
            ],
        ]);

        $this->db->query("
        ALTER TABLE perangkat
        ADD CONSTRAINT fk_spec
        FOREIGN KEY (id_spec)
        REFERENCES spec_perangkat(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->forge->dropColumn('perangkat', ['id_spec', 'kode_id']);
    }
}
