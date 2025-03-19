<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchPackageController;

Route::get('/aur/search', [ArchPackageController::class, 'searchAUR']);
Route::get('/alr/search', [ArchPackageController::class, 'searchALR']);
Route::get('/search', [ArchPackageController::class, 'searchAll']);
Route::get('/alr/info', [ArchPackageController::class, 'getALRInfo']);
Route::get('/aur/info', [ArchPackageController::class, 'getAURInfo']);
