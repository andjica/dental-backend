<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Interfaces\CountryInterface;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryInterface $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getCountries()
    {
        $countries = $this->countryService->getCountries();

        if(!$countries)
        {
            return response()->json(['message' => 'Not found'],404);
        }

         return response()->json([
            'success' => 'OK',
            'countries' => $countries
        ], 200);
    }

    public function getPhoneCode($countryId)
    {
        $phoneCode = $this->countryService->getPhoneCode($countryId);

         if(!$phoneCode)
        {
            return response()->json(['message' => 'Not found'],404);
        }

         return response()->json([
            'success' => 'OK',
            'phoneCode' => $phoneCode
        ], 200);
    }
}
