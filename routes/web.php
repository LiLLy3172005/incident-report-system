<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;

// Breeze auth routes
require __DIR__.'/auth.php';

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Report routes (cần đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/my-reports', [ReportController::class, 'userReports'])->name('reports.my');
    Route::get('/my-reports/{id}', [ReportController::class, 'show'])->name('reports.show');


    Route::post('/reports/step1', [ReportController::class, 'storeStep1'])->name('reports.step1');
    Route::post('/reports/step2', [ReportController::class, 'storeStep2'])->name('reports.step2');
    Route::post('/reports/step3', [ReportController::class, 'storeStep3'])->name('reports.step3');
    Route::post('/reports/final', [ReportController::class, 'storeFinal'])->name('reports.final');
   


});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/approve', [AdminController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/reject', [AdminController::class, 'reject'])->name('reports.reject');
});