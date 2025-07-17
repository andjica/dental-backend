<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\BuyerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\ProductInterface;
use App\Http\Interfaces\UserInfoInterface;

class AdminController extends Controller
{
    protected $userInfoService;
    protected $companyService;
    protected $buyerService;
    protected $productService;

    public function __construct(UserInfoInterface $userInfoService, CompanyInterface $companyService, BuyerInterface $buyerService, ProductInterface $productService)
    {
        $this->userInfoService = $userInfoService;
        $this->companyService = $companyService;
        $this->buyerService = $buyerService;
        $this->productService = $productService;
    }


    public function  index() {}

    public function getUsersInfo()
    {
        Auth::user()->id;
        $users = $this->userInfoService->getAllUserInfo();

        return response()->json([
            'success' => 'Users info retrieved',
            'data' => $users
        ], 200);
    }

    public function getCompanies()
    {
        Auth::user()->id;
        $companies = $this->companyService->getAllActiveCompanies();
        return response()->json([
            'success' => 'Active Companies retrieved',
            'data' => $companies
        ], 200);
    }

    public function getInactiveCompanies()
    {
        Auth::user()->id;
        $companies = $this->companyService->getAllInactiveCompanies();
        $companies = $companies->load(['country', 'city']);
        
        return response()->json([
            'success' => 'Inactive Companies retrieved',
            'data' => $companies
        ], 200);
    }

    public function activateCompany($companyId)
    {

        $company = $this->companyService->activateCompany((int)$companyId);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company activated successfully.',
            'data' => $company,
        ]);
    }

    public function deleteCompany($companyId)
    {
        $deleted = $this->companyService->deleteCompany((int)$companyId);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found or deletion failed.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company and associated user deleted successfully.'
        ]);
    }

    public function getActiveProducts()
    {
        return response()->json([
            'success' => true,
            'count' => $this->productService->countActive()
        ]);
    }

    public function getRegisteredCompanies()
    {
        return response()->json([
            'success' => true,
            'count' => $this->companyService->countRegistered()
        ]);
    }

    public function getTotalBuyers()
    {
        return response()->json([
            'success' => true,
            'count' => $this->buyerService->countTotal()
        ]);
    }
}
