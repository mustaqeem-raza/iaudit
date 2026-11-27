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

        // The admin panel has no Tailwind, so links() uses Laravel's plain
        // markup, styled by .pagination in public/css/admin.css.
        Paginator::defaultView('pagination::default');
        Paginator::defaultSimpleView('pagination::simple-default');
    }
}
