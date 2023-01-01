<?php

namespace App\Providers;

use App\Http\Controllers\ModulesController;
use App\Http\Controllers\NotificationController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if (session('locale') == null){
            session()->put('locale', app()->getLocale());
        };

        Paginator::useBootstrapFive();
        date_default_timezone_set('Europe/Bratislava');

        View::composer('*', function ($view) {
            $view->with('notifications', (new NotificationController)->WebNotification());
            $view->with('activated_modules', (new ModulesController())->Users_modules());
        });
    }
}
