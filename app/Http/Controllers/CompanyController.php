<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    protected $companyService;
    protected $userId;

    public function __construct(CompanyInterface $companyService)
    {
        $this->companyService = $companyService;
    }

    public function showCompany()
    {
        $userId = Auth::user()->id;
        
        $company = $this->companyService->getCompanyByUserId($userId);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json(['data' => $company], 200);
    }

        public function update(UpdateCompanyRequest $request): JsonResponse
        {
            $userId = Auth::user()->id;

            $company = $this->companyService->updateCompany($request->validated(), $userId);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            return response()->json(['data' => $company], 200);
        }
}

