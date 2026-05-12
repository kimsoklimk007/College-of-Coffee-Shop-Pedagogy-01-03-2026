<?php

use App\Http\Controllers\UserCustomer\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user', 'middleware' => 'user'], function () {
    Route::get('/home', [UserDashboardController::class, 'index'])->name('userDashboard');
});
