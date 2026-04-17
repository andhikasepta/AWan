<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SpecPerangkatSeeder extends Seeder
{
    public function run()
    {
        $file = fopen(WRITEPATH . 'uploads/spek.csv', 'r');
        $data = [];
        $header = fgetcsv($file);


        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            $data[] = [
                'kode_spec'=>$row[0],
                'nama_perangkat' => $row[1],
            ];
        }

        fclose($file);

        $this->db->table('spec_perangkat')->insertBatch($data);
    }
}
