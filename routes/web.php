<?php

use App\Http\Controllers\ArchPackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [ArchPackageController::class, 'searchAll']);
Route::get('/alr-details', [PackageController::class, 'alrDetails'])->name('alr.details');
Route::get('/aur-details', [PackageController::class, 'aurDetails'])->name('aur.details');
