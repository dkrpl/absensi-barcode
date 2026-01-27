<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing to register
    }

    public function boot(): void
    {
        // Use Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        // Set timezone
        date_default_timezone_set('Asia/Jakarta');

        // Set locale
        app()->setLocale('id');
    }
}
