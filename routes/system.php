<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\System\SystemDashboardController;
use App\Http\Controllers\System\UserManagementController;
use App\Http\Controllers\System\ShopManagementController;
use App\Http\Controllers\System\SystemSettingController;

/*
|--------------------------------------------------------------------------
| System Routes (Super Admin Only)
|--------------------------------------------------------------------------
|
| These routes are for Super Admin (System Owner / Platform Owner)
| to manage the entire platform including users, shops, and system settings.
|
*/

Route::group(['prefix' => 'system', 'middleware' => ['auth', 'super_admin']], function () {

    // System Dashboard
    Route::get('/dashboard', [SystemDashboardController::class, 'index'])->name('system.dashboard');

    // User Management
    Route::prefix('users')->controller(UserManagementController::class)->group(function () {
        Route::get('/', 'index')->name('system.users');
        Route::get('/create', 'create')->name('system.users.create');
        Route::post('/store', 'store')->name('system.users.store');
        Route::get('/edit/{user}', 'edit')->name('system.users.edit');
        Route::post('/update/{user}', 'update')->name('system.users.update');
        Route::post('/toggle-status/{user}', 'toggleStatus')->name('system.users.toggleStatus');
        Route::post('/reset-password/{user}', 'resetPassword')->name('system.users.resetPassword');
        Route::delete('/delete/{user}', 'destroy')->name('system.users.delete');
    });

    // Shop Management
    Route::prefix('shops')->controller(ShopManagementController::class)->group(function () {
        Route::get('/', 'index')->name('system.shops');
        Route::get('/create', 'create')->name('system.shops.create');
        Route::post('/store', 'store')->name('system.shops.store');
        Route::get('/edit/{shop}', 'edit')->name('system.shops.edit');
        Route::post('/update/{shop}', 'update')->name('system.shops.update');
        Route::post('/toggle-status/{shop}', 'toggleStatus')->name('system.shops.toggleStatus');
        Route::get('/assign-admin/{shop}', 'assignAdminForm')->name('system.shops.assignAdmin');
        Route::post('/assign-admin/{shop}', 'assignAdmin');
        Route::delete('/delete/{shop}', 'destroy')->name('system.shops.delete');
    });

    // System Settings
    Route::prefix('settings')->controller(SystemSettingController::class)->group(function () {
        Route::get('/', 'index')->name('system.settings');
        Route::post('/update', 'update')->name('system.settings.update');
        Route::post('/toggle-feature', 'toggleFeature')->name('system.settings.toggleFeature');
    });

    // Activity Logs
    Route::get('/activity-logs', [SystemDashboardController::class, 'activityLogs'])->name('system.activityLogs');

    // Reports
    Route::get('/reports', [SystemDashboardController::class, 'reports'])->name('system.reports');

});
