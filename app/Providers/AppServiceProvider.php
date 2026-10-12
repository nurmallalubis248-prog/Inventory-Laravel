<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tampilan pagination bergaya Stockify (tanpa Tailwind)
        Paginator::defaultView('vendor.pagination.stockify');
    }
}
