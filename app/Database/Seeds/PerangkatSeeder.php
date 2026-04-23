<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\SpecPerangkatModel;
use App\Models\PerangkatModel;
use CodeIgniter\I18n\Time;

class PerangkatSeeder extends Seeder
{
    public function run()
    {
        set_time_limit(0);

        $file = fopen(WRITEPATH . 'uploads/coba2.csv', 'r');
        fgetcsv($file);

        $specModel = new SpecPerangkatModel;
        $perangkatModel = new PerangkatModel;

        $mapSpec =[];

        while(($row = fgetcsv($file, 1000, ';')) !== false){
            $kode_spec = strtoupper(trim($row[0]));
            $nama_spec = trim($row[1]);
            $kode_id = trim($row[2]);

            $key = $kode_spec . '|' . $nama_spec;

            if(isset($mapSpec[$key])){
                $id_spec = $mapSpec[$key];
            }else{
                $spec = $specModel
                ->where('kode_spec', $kode_spec)
                ->where('nama_perangkat', $nama_spec)
                ->first();

                if($spec){
                    $id_spec=$spec['id'];
                }else{
                    $id_spec=$specModel->insert([
                        'kode_spec'=>$kode_spec,
                        'nama_perangkat'=>$nama_spec
                    ]);
                }

                $mapSpec[$key]=$id_spec;
            }

            $noreg = $kode_spec.$kode_id;

            if($perangkatModel->where('noreg', $noreg)->first())continue;

            $perangkatModel->insert([
                'id_spec'=>$id_spec,
                'kode_id'=>$kode_id,
                'noreg'=>$noreg,
                'nama'=>$nama_spec,
                'status'=>'Tersedia'
            ]);
        }

        fclose($file);
    }
}
