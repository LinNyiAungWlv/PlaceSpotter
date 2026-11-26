<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\WeatherController;
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
Route::get('/search/places', [PlaceController::class, 'search'])->name('places.search');
Route::get('/places/{place}/weather', [WeatherController::class, 'show'])->name('places.weather');
Route::resource('places', PlaceController::class)->only(['index', 'show']);
