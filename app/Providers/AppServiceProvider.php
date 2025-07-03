<?php

namespace App\Providers;

use App\Http\Services\CityService;
use App\Http\Services\AuctionService;
use App\Http\Services\CompanyService;
use App\Http\Services\CountryService;
use App\Http\Services\ProductService;
use App\Http\Interfaces\CityInterface;
use App\Http\Services\CategoryService;
use App\Http\Services\UserInfoService;
use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\AuctionInterface;
use App\Http\Interfaces\CompanyInterface;
use App\Http\Interfaces\CountryInterface;
use App\Http\Interfaces\ProductInterface;
use App\Http\Services\SubCategoryService;
use App\Http\Interfaces\CategoryInterface;
use App\Http\Interfaces\UserInfoInterface;
use App\Http\Interfaces\SubCategoryInterface;

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
        $this->app->bind(ProductInterface::class, ProductService::class);
        $this->app->bind(CategoryInterface::class, CategoryService::class);
        $this->app->bind(SubCategoryInterface::class, SubCategoryService::class);
        $this->app->bind(AuctionInterface::class, AuctionService::class);

    }
}
