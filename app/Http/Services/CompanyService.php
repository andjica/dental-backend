<?php

namespace App\Http\Services;

use App\Models\Company;
use App\Http\Interfaces\CompanyInterface;

class CompanyService implements CompanyInterface
{
    public function getCompanyByUserId($userId)
    {
       return Company::where('user_id', $userId)->first();
    }
}