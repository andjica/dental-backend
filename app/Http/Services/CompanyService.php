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

    public function updateCompany(array $data, int $userId)
    {
        $company = Company::where('user_id', $userId)->first();

        if (!$company) {
            return null;
        }

        // Ako je logo fajl (slika), sačuvaj je
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $filename = uniqid('logo_') . '.' . $data['logo']->getClientOriginalExtension();
            $path = $data['logo']->storeAs('public/company/logos', $filename);
            $data['logo'] = 'storage/company/logos/' . $filename;
        }


        $isFinished = !empty($data['country_id']) &&
              !empty($data['city_id']) &&
              !empty($data['address']) &&
              !empty($data['postal_code']) &&
              !empty($data['phone_code']) &&
              !empty($data['name']) &&
              !empty($data['tax_number']) &&
              !empty($data['registration_number']) &&
              !empty($data['email']);


        $company->update([
            'country_id'           => $data['country_id'] ?? null,
            'city_id'              => $data['city_id'] ?? null,
            'address'              => $data['address'] ?? null,
            'postal_code'          => $data['postal_code'] ?? null,
            'phone_code'           => $data['phone_code'] ?? null,
            'name'                 => $data['name'] ?? null,
            'logo'                 => $data['logo'] ?? $company->logo,
            'tax_number'           => $data['tax_number'] ?? null,
            'registration_number' => $data['registration_number'] ?? null,
            'email'                => $data['email'] ?? null,
            'is_finished_profile' => $isFinished
        ]);

        return $company;
    }
}