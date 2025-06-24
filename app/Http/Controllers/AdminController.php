<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\UserInfoInterface;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $userInfoServices;
    protected $companyServices;

    public function __construct(UserInfoInterface $userInfoServices, CompanyInterface $companyServices)
    {
        $this->userInfoServices = $userInfoServices;
        $this->companyServices = $companyServices;
    }


    public function  index()
    {

    }

    public function getUsersInfo()
    {
        $users = $this->userInfoServices->getAllUserInfo();

        return response()->json([
            'success' => 'Users info retrieved',
            'data' => $users 
        ], 200);
    }

    public function getCompanies()
    {
        $companies = $this->companyServices->getAllActiveCompanies();

         return response()->json([
            'success' => 'Active Companies retrieved',
            'data' => $companies
        ], 200);
    }

    public function getInactiveCompanies()
    {
        $companies = $this->companyServices->getAllInactiveCompanies();

         return response()->json([
            'success' => 'Inactive Companies retrieved',
            'data' => $companies
        ], 200);
    }
}
