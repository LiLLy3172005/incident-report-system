<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;

// Breeze auth routes
require __DIR__.'/auth.php';

// Trang chủ
Route::get('/', function () {
    return view('home');
})->name('home');

// Report routes (cần đăng nhập)
Route::middleware(['auth'])->group(function () {
     Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/my-reports', [ReportController::class, 'userReports'])->name('reports.my');
    Route::get('/my-reports/{id}', [ReportController::class, 'show'])->name('reports.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/approve', [AdminController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/reject', [AdminController::class, 'reject'])->name('reports.reject');
});