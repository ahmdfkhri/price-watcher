<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::resource('store', StoreController::class);
Route::get('store-prices', [StoreController::class, 'getStorePrices']);
Route::resource('transaction', TransactionController::class);
Route::resource('product', ProductController::class);