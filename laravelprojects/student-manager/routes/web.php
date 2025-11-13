<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');
    Route::post('/manager/store', [ManagerController::class, 'store'])->name('manager.store');
    Route::post('/manager/update/{id}', [ManagerController::class, 'update'])->name('manager.update');
    Route::post('/manager/delete/{id}', [ManagerController::class, 'destroy'])->name('manager.delete');
});
