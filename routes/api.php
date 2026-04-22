<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RouteController;

/*
|--------------------------------------------------------------------------
| API Routes - Coffee Shop POS
|--------------------------------------------------------------------------
|
| Base URL: http://localhost:8000/api
|
*/

// ==================== CATEGORY ROUTES ====================
// GET    /api/category/list         - List all categories
// GET    /api/category/{id}         - Get category detail
// POST   /api/category/create       - Create new category
// POST   /api/category/update       - Update category
// GET    /api/category/delete/{id}  - Delete category

Route::get('category/list', [RouteController::class, 'categoryList']);
Route::get('category/{id}', [RouteController::class, 'categoryDetail']);
Route::post('category/create', [RouteController::class, 'createCategory']);
Route::post('category/update', [RouteController::class, 'categoryUpdate']);
Route::get('category/delete/{id}', [RouteController::class, 'deleteCategory']);


// ==================== PRODUCT ROUTES ====================
// GET    /api/product/list              - List all products
// GET    /api/product/{id}              - Get product detail
// POST   /api/product/create            - Create new product
// POST   /api/product/update            - Update product
// GET    /api/product/delete/{id}       - Delete product
// GET    /api/product/sizes/{productId} - Get product sizes
// POST   /api/product/size/create       - Create product size
// POST   /api/product/size/update       - Update product size
// GET    /api/product/size/delete/{id}  - Delete product size

Route::get('product/list', [RouteController::class, 'productList']);
Route::get('product/{id}', [RouteController::class, 'productDetail']);
Route::post('product/create', [RouteController::class, 'createProduct']);
Route::post('product/update', [RouteController::class, 'updateProduct']);
Route::get('product/delete/{id}', [RouteController::class, 'deleteProduct']);

Route::get('product/sizes/{productId}', [RouteController::class, 'productSizeList']);
Route::post('product/size/create', [RouteController::class, 'createProductSize']);
Route::post('product/size/update', [RouteController::class, 'updateProductSize']);
Route::get('product/size/delete/{id}', [RouteController::class, 'deleteProductSize']);


// ==================== DISCOUNT ROUTES ====================
// GET    /api/discount/list         - List all discounts
// POST   /api/discount/create       - Create new discount
// POST   /api/discount/update       - Update discount
// GET    /api/discount/delete/{id}  - Delete discount

Route::get('discount/list', [RouteController::class, 'discountList']);
Route::post('discount/create', [RouteController::class, 'createDiscount']);
Route::post('discount/update', [RouteController::class, 'updateDiscount']);
Route::get('discount/delete/{id}', [RouteController::class, 'deleteDiscount']);


// ==================== ORDER ROUTES ====================
// GET    /api/order/list                - List all orders
// GET    /api/order/{id}                - Get order detail
// POST   /api/order/create              - Create new order
// POST   /api/order/status/update       - Update order status
// GET    /api/order/delete/{id}         - Delete order

Route::get('order/list', [RouteController::class, 'orderList']);
Route::get('order/{id}', [RouteController::class, 'orderDetail']);
Route::post('order/create', [RouteController::class, 'createOrder']);
Route::post('order/status/update', [RouteController::class, 'updateOrderStatus']);
Route::get('order/delete/{id}', [RouteController::class, 'deleteOrder']);


// ==================== TAX ROUTES ====================
// GET    /api/tax/list          - List all tax settings
// POST   /api/tax/create        - Create new tax
// POST   /api/tax/update        - Update tax
// GET    /api/tax/delete/{id}   - Delete tax

Route::get('tax/list', [RouteController::class, 'taxList']);
Route::post('tax/create', [RouteController::class, 'createTax']);
Route::post('tax/update', [RouteController::class, 'updateTax']);
Route::get('tax/delete/{id}', [RouteController::class, 'deleteTax']);


// ==================== FEEDBACK ROUTES ====================
// GET    /api/feedback/list           - List all feedback
// GET    /api/feedback/list/{productId} - List feedback for product
// POST   /api/feedback/create         - Create new feedback

Route::get('feedback/list', [RouteController::class, 'feedbackList']);
Route::get('feedback/list/{productId}', [RouteController::class, 'feedbackList']);
Route::post('feedback/create', [RouteController::class, 'createFeedback']);


// ==================== LEGACY ROUTES (Backward Compatibility) ====================
Route::post('create/category', [RouteController::class, 'createCategory']);
Route::post('create/feedback', [RouteController::class, 'createFeedback']);
Route::post('category/update', [RouteController::class, 'categoryUpdate']);
