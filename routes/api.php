<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class)->names('api.users');
Route::apiResource('categories', CategoryController::class)->names('api.categories');
Route::apiResource('products', ProductController::class)->names('api.products');
Route::apiResource('tags', TagController::class)->names('api.tags');
