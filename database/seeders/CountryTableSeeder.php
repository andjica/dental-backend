<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $country = [
        //     ['name' => 'Serbia', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Italy', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Netherlands', 'created_at' => now(), 'updated_at' => now()],
        // ];

        // DB::table('countries')->insert($country);
        ini_set('memory_limit', '1024M'); // 1GB
        $json = File::get(database_path('seeders/data/countries.json'));
        $countries = json_decode($json, true);

        // Prepare data for insertion
        $countryData = array_map(function($country) {
            return [
                'name' => $country['name'],
                'iso_code' => $country['iso2'],
                'phone_code' => $country['phone_code'],
                'currency'=> $country['currency'],
                'currency_name' => $country['currency_name'],
                'currency_symbol' => $country['currency_symbol'],// Assuming 'iso3' is the field for ISO 3166-1 alpha-3
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $countries);

        // Insert data into the countries table
        DB::table('countries')->insert($countryData);
    }
}
