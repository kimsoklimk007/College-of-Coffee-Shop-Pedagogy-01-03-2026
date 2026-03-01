<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RouteController;


//Get
Route::get('category/list', [RouteController::class, 'categoryList']);

Route::get('product/list', [RouteController::class, 'productList']);//http://localhost:8000/api/admin/product/list
Route::get('order/list', [RouteController::class, 'orderList']);//http://localhost:8000/api/admin/order/list

//POST
Route::post('create/category', [RouteController::class, 'createCategory']);//http://localhost:8000/api/admin/create/category
Route::post('create/feedback', [RouteController::class, 'createFeedback']);//http://localhost:8000/api/admin/create/feedback


// Route::post('delete/category', [RouteController::class, 'deleteCategory']);//testing with post method // http://localhost:8000/api/admin/delete/category/13
Route::get('delete/category/{id}', [RouteController::class, 'deleteCategory']);//testing with get method //

Route::get('category/detail/{id}', [RouteController::class, 'categoryDetail']);//http://localhost:8000/api/admin/category/detail/1
Route::post('category/update', [RouteController::class, 'categoryUpdate']); //http://localhost:8000/api/admin/category/update

Route::get('tax/list',[RouteController::class,'taxList']);


/*
key
name => name
id  => category_id

*/


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//http://localhost:8000/api/admin/category/list
