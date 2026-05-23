<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard.home')->name('home');

Route::get('/users', [PageController::class, 'show'])
    ->defaults('resource', 'users')
    ->name('users.index');

Route::get('/categories', [PageController::class, 'show'])
    ->defaults('resource', 'categories')
    ->name('categories.index');

Route::get('/products', [PageController::class, 'show'])
    ->defaults('resource', 'products')
    ->name('products.index');

Route::get('/tags', [PageController::class, 'show'])
    ->defaults('resource', 'tags')
    ->name('tags.index');
