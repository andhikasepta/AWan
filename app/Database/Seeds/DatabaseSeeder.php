<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('AdminSeeder');
        $this->call('UsersSeeder');
        $this->call('PerangkatSeeder');
        $this->call('SpecPerangkatSeeder');
        // $this->call('MutasiSeeder');
    }
}
