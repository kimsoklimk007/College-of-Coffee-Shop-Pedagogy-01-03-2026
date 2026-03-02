<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
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
        //
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

        Paginator::useBootstrap();
    }
}
