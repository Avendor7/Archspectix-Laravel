<?php

use App\Http\Controllers\ArchPackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [ArchPackageController::class, 'searchAll']);


