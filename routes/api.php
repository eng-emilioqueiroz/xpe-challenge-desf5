<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);           // findAll
    Route::get('/count', [ProductController::class, 'count']);      // count
    Route::get('/search', [ProductController::class, 'searchByName']); // findByName
    Route::get('/{id}', [ProductController::class, 'show']);        // findById
    Route::post('/', [ProductController::class, 'store']);          // create
    Route::put('/{id}', [ProductController::class, 'update']);      // update
    Route::delete('/{id}', [ProductController::class, 'destroy']);  // delete
});
