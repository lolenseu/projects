<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/student', [StudentController::class, 'index'])->name('student.index');
    Route::post('/student/store', [StudentController::class, 'store'])->name('student.store');
    Route::post('/student/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::post('/student/delete/{id}', [StudentController::class, 'destroy'])->name('student.delete');

    Route::get('/teacher', [TeacherController::class, 'index'])->name('teacher.index');
    Route::post('/teacher/store', [TeacherController::class, 'store'])->name('teacher.store');
    Route::post('/teacher/update/{id}', [TeacherController::class, 'update'])->name('teacher.update');
    Route::post('/teacher/delete/{id}', [TeacherController::class, 'destroy'])->name('teacher.delete');

    Route::get('/subject', [SubjectController::class, 'index'])->name('subject.index');
    Route::post('/subject/store', [SubjectController::class, 'store'])->name('subject.store');
    Route::post('/subject/update/{id}', [SubjectController::class, 'update'])->name('subject.update');
    Route::post('/subject/delete/{id}', [SubjectController::class, 'destroy'])->name('subject.delete');
    
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::post('/report/store', [ReportController::class, 'store'])->name('report.store');
    Route::post('/report/update/{id}', [ReportController::class, 'update'])->name('report.update');
    Route::post('/report/delete/{id}', [ReportController::class, 'destroy'])->name('report.delete');
    
    Route::post('/teacher/assign/{subject_id}', [TeacherController::class, 'assignTeacher'])->name('teacher.assign');
    Route::post('/teacher/unassign/{teacher_id}', [TeacherController::class, 'unassignTeacher'])->name('teacher.unassign');
});