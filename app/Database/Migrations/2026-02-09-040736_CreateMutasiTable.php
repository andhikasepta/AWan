<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Mutasi;

class CreateMutasiTable extends Migration
{
    public function up()
    {
        $mutasi = new Mutasi();

        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'unsigned'=>true,
                'auto_increment'=>true,
            ],
            'id_perangkat'=>[
                'type'=>'INT',
                'unsigned'=>true,
            ],
            'id_users'=>[
                'type'=>'INT',
                'unsigned'=>true,
                'null'=>true,
            ],
            'status'=>[
                'type'=>'VARCHAR',
                'constraint'=>20,
                'null'=>true,
            ],
            'keterangan'=>[
                'type'=>'TEXT',
                'null'=>true,
            ],
            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ],
            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_perangkat','perangkat','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('id_users','users','id','CASCADE','CASCADE');
        $this->forge->createTable('mutasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('mutasi');
    }
}
