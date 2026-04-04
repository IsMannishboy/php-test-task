<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;
Route::get('/admin', [AdminController::class, 'dashboard'])->middleware(['auth','role:manager']);
Route::put('/tickets/{id}', [TicketController::class, 'update'])->middleware(['auth','role:manager']);
Route::get('/tickets/filters', [TicketController::class, 'TicketFilter'])->middleware(['auth','role:manager']);
Route::get('/tickets/{ticket_id}/download', [TicketController::class, 'download'])->middleware(['auth','role:manager']);
Route::get('/widget', function () {
    return view('widget');
});
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/auth/login', [AdminController::class, 'login']);


