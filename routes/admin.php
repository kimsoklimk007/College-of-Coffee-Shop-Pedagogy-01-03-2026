<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\BusinessSettingController;

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/home', [AdminDashboardController::class, 'index'])->name('adminDashboard');

    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('detail', 'detail')->name('profile.detail');
        Route::post('update/{id}', 'update')->name('profile.update');
        Route::get('overview', 'overview')->name('profile.overview');
        Route::get('createnewuser', 'createNewUser')->name('profile.createNewUser');
        Route::post('addnewuser', 'addNewUser')->name('profile.addNewUser');
        Route::get('changeprofilepage', 'changeProfilePage')->name('changeProfilePage');
        Route::post('updateRole/{id}', 'updateField')->name('updateField');
    });

    Route::prefix('password')->controller(AuthController::class)->group(function () {
        Route::get('passwordpage', 'passwordpage')->name('passwordpage');
        Route::post('update/{id}', 'passwordchange')->name('passwordchange');

        Route::get('resetPage', 'resetPasswordPage')->name('resetPasswordPage');
        Route::post('resetPassword', 'resetPassword')->name('resetPassword');

    });

    Route::prefix('category')->controller(CategoryController::class)->group(function () {
        Route::get('list', 'list')->name('category.list');
        Route::get('create', 'create')->name('category.create');
        Route::post('store', 'store')->name('category.store');
        Route::get('edit/{id}', 'edit')->name('category.edit');
        Route::post('update/{id}', 'update')->name('category.update');
        Route::delete('delete/{id}', 'delete')->name('category.delete');
    });

    Route::prefix('product')->controller(ProductController::class)->group(function () {
        Route::get('list', 'prodlist')->name('product.prodlist');
        Route::get('prodcreate', 'prodcreate')->name('product.prodcreate');
        Route::post('prodstore', 'prodstore')->name('product.prodstore');
        Route::get('prodedit/{id}', 'prodedit')->name('product.prodedit');
        Route::post('produpdate', 'produpdate')->name('product.produpdate');
        Route::delete('proddelete/{id}', 'proddelete')->name('product.proddelete');
        Route::get('prodsize/{id}', 'prodsize')->name('prodsize');
        Route::post('prodsizestore/{id}', 'prodsizestore')->name('prodsizestore');
        Route::get('getCategorySizes/{categoryId}', 'getCategorySizes')->name('product.getCategorySizes');

    });

    Route::prefix('order')->controller(OrderController::class)->group(function () {
        Route::get('list', 'orderlist')->name('order.orderlist');
        Route::get('viewOrder/{orderCode}', 'viewOrder')->name('order.viewOrder');
        Route::post('update', 'updateOrder')->name('order.updateOrder');

        //Booking
        Route::get('booking', 'bookingPage')->name('bookingPage');
        Route::get('getproducts', 'getProductsByCategory')->name('getProductsByCategory');
        Route::post('additems', 'additems')->name('additems');
        Route::post('storeordercode', 'storeOrderCode')->name('storeOrderCode');
        Route::post('clearcart', 'clearCart')->name('clearCart');
        Route::get('getOrderCodes', 'getOrderCodes')->name('getOrderCodes');

        //Proceed Order
        Route::post('orderConfirm', 'orderConfirm')->name('orderConfirm');
        Route::post('generatePaymentSlip', 'generatePaymentSlip')->name('generatePaymentSlip');
        Route::get('paymentRecord', 'paymentRecord')->name('paymentRecord');
        Route::get('searchRecord', 'searchRecord')->name('searchRecord');
        Route::get('paymentSlip', 'paymentSlip')->name('paymentSlip');
        Route::get('printPaymentSlip/{orderCode}', 'printPaymentSlip')->name('printPaymentSlip');
    });

    Route::prefix('discount')->controller(ProductController::class)->group(function () {
        Route::get('discountPage', 'discountPage')->name('discountPage');
        Route::post('adddiscount', 'adddiscount')->name('adddiscount');
    });

    Route::prefix('tax')->controller(AdminDashboardController::class)->group(function () {
        Route::get('taxPage', 'taxPage')->name('taxPage');
        Route::post('addTaxRate', 'addTaxRate')->name('addTaxRate');
    });

    // Business Settings Routes
    Route::prefix('business')->controller(BusinessSettingController::class)->group(function () {
        Route::get('settings', 'index')->name('business.settings');
        Route::post('update', 'update')->name('business.update');
        Route::delete('deleteLogo', 'deleteLogo')->name('business.deleteLogo');
    });

    Route::prefix('delivery')->controller(AdminDashboardController::class)->group(function () {
        Route::get('deliveryInfoPage', 'deliveryInfoPage')->name('deliveryInfoPage');
        Route::post('addDeliFees', 'addDeliFees')->name('addDeliFees');
    });

    Route::prefix('report')->controller(ReportController::class)->group(function () {
        Route::get('reportOverview', 'reportOverview')->name('reportOverview');
        Route::get('salesReportPage', 'salesReportPage')->name('salesReportPage');
        Route::get('salesReport', 'salesReport')->name('salesReport');

        Route::get('inventoryPage', 'inventoryPage')->name('inventoryPage');
        Route::get('productAnalysis', 'productAnalysis')->name('productAnalysis');

        Route::get('supplierPurchasePage', 'supplierPurchasePage')->name('supplierPurchasePage');
        Route::get('supplierPurchase', 'supplierPurchase')->name('supplierPurchase');

        Route::get('assetPage', 'assetPage')->name('assetPage');
        Route::get('assetReport', 'assetReport')->name('assetReport');

        Route::get('purchasedetailsPage', 'purchasedetailsPage')->name('purchasedetailsPage');
        Route::get('purchaseDetails', 'purchaseDetails')->name('purchaseDetails');

        Route::get('feedbackPage', 'feedbackPage')->name('feedbackPage');
        Route::get('feedbackReport', 'feedbackReport')->name('feedbackReport');

    });

    Route::prefix('purchase')->controller(PurchaseController::class)->group(function () {
        Route::get('list', 'index')->name('supplier.index');
        Route::get('create', 'createSupplierPage')->name('createSupplierPage');
        Route::post('createSupplier', 'createSupplier')->name('createSupplier');
        Route::get('edit/{id}', 'editSupplier')->name('editSupplier');
        Route::post('update/{id}', 'updateSupplier')->name('updateSupplier');
        Route::delete('delete/{id}', 'deleteSupplier')->name('deleteSupplier');

        Route::get('purchasePage', 'purchasePage')->name('purchasePage');
        Route::post('addItem', 'addItem')->name('addItem');
        Route::post('storePurchase', 'storePurchase')->name('storePurchase');
        Route::post('removeItem/{id}', 'removeItem')->name('removeItem');

    });

    Route::prefix('assetcategory')->controller(AssetController::class)->group(function(){
        Route::get('index','index')->name('assetCategories.index');
        Route::post('store','store')->name('assetCategories.store');
        Route::get('edit/{id}','edit')->name('assetCategories.edit');
        Route::put('update/{id}','update')->name('assetCategories.update');
        Route::delete('destroy/{id}','destroy')->name('assetCategories.destroy');

    });

    Route::prefix('assets')->controller(AssetController::class)->group(function(){
        Route::get('index','asset_index')->name('assets.index');
        Route::get('create','asset_create')->name('assets.create');
        Route::post('store','asset_store')->name('assets.store');
        Route::get('edit/{id}','asset_edit')->name('assets.edit');
        Route::put('update/{id}','asset_update')->name('assets.update');
        Route::delete('destroy/{id}','asset_destroy')->name('assets.destroy');
    });

    // Currency Exchange Rate Routes
    Route::prefix('currency')->controller(CurrencyController::class)->group(function(){
        Route::get('exchange-rate', 'getExchangeRate')->name('currency.exchangeRate');
        Route::post('convert', 'convertCurrency')->name('currency.convert');
        Route::post('clear-cache', 'clearCache')->name('currency.clearCache');
    });

    // Inventory Routes
    Route::prefix('inventory')->controller(InventoryController::class)->group(function(){
        Route::get('index', 'index')->name('inventory.index');
        Route::get('create', 'create')->name('inventory.create');
        Route::post('store', 'store')->name('inventory.store');
        Route::get('edit/{id}', 'edit')->name('inventory.edit');
        Route::post('update/{id}', 'update')->name('inventory.update');
        Route::delete('delete/{id}', 'destroy')->name('inventory.destroy');
        Route::post('adjust-stock/{id}', 'adjustStock')->name('inventory.adjustStock');
        Route::get('low-stock', 'lowStock')->name('inventory.lowStock');
        Route::get('history/{id}', 'stockHistory')->name('inventory.history');
    });

    // Transaction Routes (Income-Expense)
    Route::prefix('transaction')->controller(TransactionController::class)->group(function(){
        Route::get('index', 'index')->name('transaction.index');
        Route::get('create', 'create')->name('transaction.create');
        Route::post('store', 'store')->name('transaction.store');
        Route::get('edit/{id}', 'edit')->name('transaction.edit');
        Route::post('update/{id}', 'update')->name('transaction.update');
        Route::delete('delete/{id}', 'destroy')->name('transaction.destroy');
        Route::get('summary', 'summary')->name('transaction.summary');
    });

    // Menu/Pricing Routes
    Route::prefix('menu')->controller(MenuController::class)->group(function(){
        Route::get('index', 'index')->name('menu.index');
        Route::get('generate', 'generateTelegramMenu')->name('menu.generate');
        Route::get('export', 'exportTelegram')->name('menu.export');
        Route::post('send-telegram', 'sendToTelegram')->name('menu.sendTelegram');
    });

    // Staff Management Routes
    Route::prefix('staff')->controller(StaffController::class)->group(function(){
        Route::get('index', 'index')->name('staff.index');
        Route::get('create', 'create')->name('staff.create');
        Route::post('store', 'store')->name('staff.store');
        Route::get('edit/{id}', 'edit')->name('staff.edit');
        Route::post('update/{id}', 'update')->name('staff.update');
        Route::delete('delete/{id}', 'destroy')->name('staff.destroy');
        Route::post('toggle-status/{id}', 'toggleStatus')->name('staff.toggleStatus');
    });

    // Employee Management Routes
    Route::prefix('employee')->controller(EmployeeController::class)->group(function(){
        Route::get('settings', 'settings')->name('employee.settings');
        Route::get('index', 'index')->name('employee.index');
        Route::get('create', 'create')->name('employee.create');
        Route::post('store', 'store')->name('employee.store');
        Route::get('show/{id}', 'show')->name('employee.show');
        Route::get('edit/{id}', 'edit')->name('employee.edit');
        Route::put('update/{id}', 'update')->name('employee.update');
        Route::delete('delete/{id}', 'destroy')->name('employee.delete');
        Route::post('toggle-status/{id}', 'toggleStatus')->name('employee.toggleStatus');
        Route::post('generate-qr/{id}', 'generateQrCode')->name('employee.generateQr');
        Route::get('export', 'export')->name('employee.export');
        Route::post('import', 'import')->name('employee.import');
    });

    // Shift Management Routes
    Route::prefix('shift')->controller(ShiftController::class)->group(function(){
        Route::get('index', 'index')->name('shift.index');
        Route::get('create', 'create')->name('shift.create');
        Route::post('store', 'store')->name('shift.store');
        Route::get('show/{id}', 'show')->name('shift.show');
        Route::get('edit/{id}', 'edit')->name('shift.edit');
        Route::post('update/{id}', 'update')->name('shift.update');
        Route::delete('delete/{id}', 'destroy')->name('shift.delete');
        Route::post('toggle-status/{id}', 'toggleStatus')->name('shift.toggleStatus');
    });

    // Role Management Routes
    Route::prefix('role')->controller(RoleController::class)->group(function(){
        Route::get('index', 'index')->name('role.index');
        Route::get('create', 'create')->name('role.create');
        Route::post('store', 'store')->name('role.store');
        Route::get('show/{id}', 'show')->name('role.show');
        Route::get('edit/{id}', 'edit')->name('role.edit');
        Route::post('update/{id}', 'update')->name('role.update');
        Route::delete('delete/{id}', 'destroy')->name('role.delete');
        Route::post('toggle-status/{id}', 'toggleStatus')->name('role.toggleStatus');
    });

    // Attendance Routes
    Route::prefix('attendance')->controller(AttendanceController::class)->group(function(){
        Route::get('index', 'index')->name('attendance.index');
        Route::get('create', 'create')->name('attendance.create');
        Route::post('store', 'store')->name('attendance.store');
        Route::get('show/{id}', 'show')->name('attendance.show');
        Route::get('edit/{id}', 'edit')->name('attendance.edit');
        Route::post('update/{id}', 'update')->name('attendance.update');
        Route::delete('delete/{id}', 'destroy')->name('attendance.delete');
        Route::post('qr-scan', 'qrScan')->name('attendance.qrScan');
        Route::get('report', 'report')->name('attendance.report');
    });

    // Leave Management Routes
    Route::prefix('leave')->controller(LeaveController::class)->group(function(){
        Route::get('index', 'index')->name('leave.index');
        Route::get('create', 'create')->name('leave.create');
        Route::post('store', 'store')->name('leave.store');
        Route::get('show/{id}', 'show')->name('leave.show');
        Route::get('edit/{id}', 'edit')->name('leave.edit');
        Route::post('update/{id}', 'update')->name('leave.update');
        Route::delete('delete/{id}', 'destroy')->name('leave.delete');
        Route::post('approve/{id}', 'approve')->name('leave.approve');
        Route::post('reject/{id}', 'reject')->name('leave.reject');
        Route::get('balances', 'balances')->name('leave.balances');
    });

    // Payroll Routes
    Route::prefix('payroll')->controller(PayrollController::class)->group(function(){
        Route::get('index', 'index')->name('payroll.index');
        Route::get('create', 'create')->name('payroll.create');
        Route::post('calculate', 'calculate')->name('payroll.calculate');
        Route::post('store', 'store')->name('payroll.store');
        Route::get('show/{id}', 'show')->name('payroll.show');
        Route::get('edit/{id}', 'edit')->name('payroll.edit');
        Route::post('update/{id}', 'update')->name('payroll.update');
        Route::delete('delete/{id}', 'destroy')->name('payroll.delete');
        Route::get('report', 'report')->name('payroll.report');
    });

});

