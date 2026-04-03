<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth']);
Route::get('/widget', function () {
    return view('widget');
});
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/auth/login', [AdminController::class, 'login']);


