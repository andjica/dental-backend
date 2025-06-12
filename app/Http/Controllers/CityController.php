<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CityInterface;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityService;

    public function __construct(CityInterface $cityService)
    {
        $this->cityService = $cityService;
    }

    public function getCitiesByCountry($countryId)
    {
        $cities = $this->cityService->getCitiesByCountryId($countryId);

        if(!$cities)
        {
            return response()->json([
                'message' => 'Cities not found'
            ], 404);
        }

        return response()->json([
            'success' => 'OK',
            'cities' => $cities 
        ], 200);
    }
}
