<?php
namespace App\Http\Services;

use App\Models\City;
use App\Http\Interfaces\CityInterface;

class CityService implements CityInterface
{
    public function getCitiesByCountryId(int $countryId)
    {
        $cities = City::where('country_id', $countryId)->get();

        return $cities;
    }
}