<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'company'],
            ['id' => 3, 'name' => 'user'],
            ['id' => 4, 'name' => 'buyer'],
        ];

        DB::table('roles')->insert($roles);
    }
}
