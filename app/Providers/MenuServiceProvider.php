<?php

namespace App\Providers;

use App\Http\ViewComposer\MenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        View::composer('*', MenuComposer::class);
    }
}
