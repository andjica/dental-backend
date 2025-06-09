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
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'company', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
