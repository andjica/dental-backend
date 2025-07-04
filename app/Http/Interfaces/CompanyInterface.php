<?php

namespace App\Http\Interfaces;

use App\Models\Company;

interface CompanyInterface
{
    public function get(int $id): ?Company;
    public function getCompanyByUserId($userId);
    public function updateCompany(array $data, int $userId);
    public function getAllActiveCompanies();
    public function getAllInactiveCompanies();
    public function activateCompany(int $companyId);
    public function deleteCompany(int $companyId): bool;
    public function countRegistered(): int;
}
