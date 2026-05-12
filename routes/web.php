<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserCustomer\AuthController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\UserCustomer\ProfileController;
use App\Http\Controllers\UserCustomer\ProviderController;
use App\Http\Controllers\UserAdmin\CategoryController;
use App\Http\Controllers\UserCustomer\UserDashboardController;
use App\Http\Controllers\UserAdmin\AdminDashboardController;

require __DIR__.'/auth.php';
require_once __DIR__.'/admin.php';
require_once __DIR__.'/user.php';


// $user->token
Route::get('/dashboard', function () {
    return view('user_customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::redirect('/','auth/login');

Route::get('auth/register',[AuthController::class,'registerPage'])->name('userRegister');
Route::get('auth/login',[AuthController::class,'loginPage'])->name('userLogin');

Route::middleware('admin')->group(function(){

//login for google and github
Route::get('/auth/{provider}/redirect', [ProviderController::class,'redirect']);
Route::get('/auth/{provider}/callback', [ProviderController::class, 'callback']);

Route::get('/auth/google/redirect', [ProviderController::class,'redirect']);
Route::get('/auth/google/callback', [ProviderController::class, 'callback']);

Route::get('/auth/github/redirect', [ProviderController::class,'redirect']);
Route::get('/auth/github/callback', [ProviderController::class, 'callback']);

});

