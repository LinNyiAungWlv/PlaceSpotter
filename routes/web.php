<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

// only for visitors who are not logged in
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('places', PlaceController::class)->except(['index', 'show']);
});

// anyone can browse places
Route::get('/map/places', [PlaceController::class, 'mapData'])->name('places.mapData');
Route::resource('places', PlaceController::class)->only(['index', 'show']);
