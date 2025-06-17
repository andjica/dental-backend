<?php

namespace App\Providers;

use App\Http\Services\CityService;
use App\Http\Services\CountryService;
use App\Http\Interfaces\CityInterface;
use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\CountryInterface;
use App\Http\Interfaces\UserInfoInterface;
use App\Http\Services\CompanyService;
use App\Http\Services\UserInfoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(CountryInterface::class, CountryService::class);
        $this->app->bind(CityInterface::class, CityService::class);
        $this->app->bind(CompanyInterface::class, CompanyService::class);
        $this->app->bind(UserInfoInterface::class, UserInfoService::class);
    }
}
