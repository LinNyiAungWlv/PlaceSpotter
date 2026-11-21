<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlaceSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// JSON list of places for the map (public)
Route::get('/search', [PlaceSearchController::class, 'index'])->name('places.search');

// anyone can view a place; whereNumber stops "/places/create" being read as a place id
Route::get('/places/{place}', [PlaceController::class, 'show'])->whereNumber('place')->name('places.show');

// pages only for visitors who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    // max 5 login attempts per minute to slow down password guessing
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

// pages only for logged in users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/my-places', [PlaceController::class, 'mine'])->name('places.mine');

    Route::get('/places/create', [PlaceController::class, 'create'])->name('places.create');
    Route::post('/places', [PlaceController::class, 'store'])->name('places.store');

    // the policy checks inside the controller make sure only the owner gets through
    Route::get('/places/{place}/edit', [PlaceController::class, 'edit'])->whereNumber('place')->name('places.edit');
    Route::put('/places/{place}', [PlaceController::class, 'update'])->whereNumber('place')->name('places.update');
    Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->whereNumber('place')->name('places.destroy');
});
