<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingRateController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryProductController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Cart
Route::post('/cart', [CartController::class, 'store']);
Route::get('/cart', [CartController::class, 'index']);
// Route::get('/cart/{cart}', [CartController::class, 'show']);
Route::patch('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'remove']);
Route::get('/cart/{id}', [CartController::class, 'getCartDetails']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::patch('/products/{product}',[ProductController::class, 'update']);
Route::delete('/products/{product}',[ProductController::class, 'destroy']);

//Shipping
Route::post('/shipping_rates', [ShippingRateController::class, 'store']);
Route::patch('/shipping_rates/{id}', [ShippingRateController::class, 'update']);
Route::get('/shipping_rates', [ShippingRateController::class, 'index']);

//Discounts
Route::post('/discounts', [DiscountController::class, 'store']);
Route::patch('/discounts/{id}', [DiscountController::class, 'update']);
Route::get('/discounts', [DiscountController::class, 'index']);
Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);

// Categories
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::patch('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

// Categories Products
Route::get('/categories/{category}/products',[CategoryProductController::class, 'index']);
Route::get('/categories',[CategoryProductController::class, 'showAll']);
Route::post('/categories/{category}/products',[CategoryProductController::class, 'store']);
Route::patch('/categories/{category}/products',[CategoryProductController::class, 'update']);
Route::delete('/categories/{category}/products',[CategoryProductController::class, 'destroy']);