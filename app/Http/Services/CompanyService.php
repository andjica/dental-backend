<?php

namespace App\Http\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $folder = 'company/logos';
            $filename = 'andjica.logo_' . uniqid() . '.' . $data['logo']->getClientOriginalExtension();

            // Provera da li folder postoji
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            // Snimanje
            $data['logo']->storeAs($folder, $filename, 'public');

            // Čuvanje putanje za frontend
            $data['logo'] = 'storage/' . $folder . '/' . $filename;
        } else {
            unset($data['logo']); // ne menjaj ako nije uploadovana nova slika
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

    public function getAllActiveCompanies()
    {
        $companies = Company::with([
            'user' => function ($query) {
                $query->withCount(['activeProducts']);
            }
        ])
        ->where('active', 1)       
        ->orderBy('created_at', 'desc')->get();

        return $companies();
    }

    public function getAllInactiveCompanies()
    {
        $companies = Company::where('active', 0)->orderBy('created_at', 'desc')->get();

        return $companies();
    }
}