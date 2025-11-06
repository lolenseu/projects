<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/todo', [TodoController::class, 'index'])->name('todo.index');
    Route::post('/todo/store', [TodoController::class, 'store'])->name('todo.store');
    Route::post('/todo/update/{id}', [TodoController::class, 'update'])->name('todo.update');
    Route::post('/todo/delete/{id}', [TodoController::class, 'destroy'])->name('todo.delete');

});
