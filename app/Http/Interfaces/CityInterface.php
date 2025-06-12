<?php
namespace App\Http\Interfaces;

interface CityInterface 
{
    public function getCitiesByCountryId(int $countryId);
}