<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\BusinessSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        // Share counts and business settings with all views
        View::composer('*', function ($view) {
            static $counts = null;
            if ($counts === null) {
                $userId = Auth::id();
                $counts = [
                    'cartCount' => $userId ? Cart::where('user_id', $userId)->count() : 0,
                    'orderCount' => $userId ? Order::where('user_id', $userId)->count() : 0,
                ];
            }
            $view->with($counts);
        });

        // Share business settings with all views
        View::composer('*', function ($view) {
            $businessSettings = BusinessSetting::getAll();
            $view->with('businessSettings', $businessSettings);
        });

        Paginator::useBootstrap();
    }
}
