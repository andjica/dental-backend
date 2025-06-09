<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\City; // Assuming you have a City model
use App\Models\Country; // Assuming you have a Country model

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '1048M');
    
        $jsonPath = database_path('seeders/data/cities.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("Cities data file not found at: {$jsonPath}");
            return;
        }
    
        $json = File::get($jsonPath);
        $cities = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("JSON Decode Error in cities.json: " . json_last_error_msg());
            return;
        }
    
        $countries = Country::pluck('id', 'iso_code'); // Use the Eloquent `pluck` method
        if ($countries->isEmpty()) {
            $this->command->error("No countries found in the database.");
            return;
        }
    
        $this->command->info("Found " . $countries->count() . " countries and " . count($cities) . " cities.");
    
        $cityData = array_map(function ($city) use ($countries) {
            $countryCode = $city['country_code'];
            return [
                'name' => $city['name'],
                'country_id' => $countries[$countryCode] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $cities);
        
        $nullCountryCities = array_filter($cityData, fn($city) => $city['country_id'] === null);
        $this->command->info("Cities with null country_id: " . count($nullCountryCities));
    
        $cityData = array_filter($cityData, fn($city) => $city['country_id'] !== null);
    
        $chunks = array_chunk($cityData, 500);
        foreach ($chunks as $chunk) {
            try {
                // Use Eloquent to insert the chunk
                City::insert($chunk); // Bulk insert using Eloquent
            } catch (\Exception $e) {
                $this->command->error("Insert Error: " . $e->getMessage());
            }
        }
    
        $this->command->info("Cities seeding completed successfully.");
    }
}
