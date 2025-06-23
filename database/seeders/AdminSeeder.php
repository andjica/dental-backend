<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Company;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // promeni šifru po potrebi
            'role_id' => 1, // pretpostavka da je 1 = admin
            'email_verified_at' => Carbon::now(),
        ]);

        // Create related company
        Company::create([
            'user_id' => $admin->id,
        ]);
    }
}
