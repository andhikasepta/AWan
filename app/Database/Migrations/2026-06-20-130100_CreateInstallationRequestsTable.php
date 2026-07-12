<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstallationRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_mutasi' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'arep' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'node_sentral' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Pending',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addForeignKey('id_mutasi', 'mutasi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('installation_requests', true);
    }

    public function down()
    {
        $this->forge->dropTable('installation_requests');
    }
}
