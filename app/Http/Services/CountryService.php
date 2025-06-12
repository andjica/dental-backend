<?php
namespace App\Http\Services;

use App\Models\Country;
use App\Http\Interfaces\CountryInterface;

class CountryService implements CountryInterface
{
    public function getCountries()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return $countries;
    }

    public function getCurrency($countryId)
    {
        $country = Country::where('id', $countryId)->first() ?? abort(404);

        return $country;
    }

    public function getPhoneCode(int $countryId)
    {
        $country = Country::where('id', $countryId)->first() ?? abort(404);
        $country = $country->phone_code;
        
        return $country;
    }
}