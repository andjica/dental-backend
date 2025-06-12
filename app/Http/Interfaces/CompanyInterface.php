<?php

namespace App\Http\Interfaces;

interface CompanyInterface
{
    public function getCompanyByUserId($userId);
    public function updateCompany(array $data, int $userId);
}
