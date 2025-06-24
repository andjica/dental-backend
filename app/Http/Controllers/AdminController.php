<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\UserInfoInterface;

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
        Auth::user()->id;
        $users = $this->userInfoServices->getAllUserInfo();

        return response()->json([
            'success' => 'Users info retrieved',
            'data' => $users 
        ], 200);
    }

    public function getCompanies()
    {
        Auth::user()->id;
        $companies = $this->companyServices->getAllActiveCompanies();
         return response()->json([
            'success' => 'Active Companies retrieved',
            'data' => $companies
        ], 200);
    }

    public function getInactiveCompanies()
    {
         Auth::user()->id;
        $companies = $this->companyServices->getAllInactiveCompanies();

         return response()->json([
            'success' => 'Inactive Companies retrieved',
            'data' => $companies
        ], 200);
    }
}
