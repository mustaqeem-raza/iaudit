<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);

        // The admin panel has no Tailwind, so links() uses the project's own
        // plain view (resources/views/pagination.blade.php, styled by admin.css).
        Paginator::defaultView('pagination');
        Paginator::defaultSimpleView('pagination::simple-default');
    }
}
