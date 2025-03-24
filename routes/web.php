<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

    Route::get('/', [HomeController::class, 'index']);

    Route::get('/search', [SearchController::class, 'index'])->name('search');

