<?php

// Suppress deprecated PDO warnings for Laravel vendor files
error_reporting(E_ALL & ~E_DEPRECATED);

use Illuminate\Foundation\Application;
use App\Http\Middleware\ChefMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CashierMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/admin',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware -> alias([
            'admin' =>AdminMiddleware::class,
            'user' => UserMiddleware::class,
            // 'cashier' => CashierMiddleware::class
            //             'chef' => ChefMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
