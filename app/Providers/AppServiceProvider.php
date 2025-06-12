<?php

namespace App\Providers;

use App\Http\Services\CityService;
use App\Http\Services\CountryService;
use App\Http\Interfaces\CityInterface;
use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\CountryInterface;
use App\Http\Services\CompanyService;

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
    }
}
