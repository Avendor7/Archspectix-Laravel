<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArchPackageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [ArchPackageController::class, 'searchAll']);
Route::get('/alr-details', [PackageController::class, 'alrDetails'])->name('alr.details');
Route::get('/aur-details', [PackageController::class, 'aurDetails'])->name('aur.details');

Route::get('/aur/search', [ArchPackageController::class, 'searchAUR']);
Route::get('/alr/search', [ArchPackageController::class, 'searchALR']);
Route::get('/alr/info', [ArchPackageController::class, 'getALRInfo']);
Route::get('/aur/info', [ArchPackageController::class, 'getAURInfo']);

Route::get('/welcome', function () {
    return Inertia::render('Welcome');
})->name('home');
