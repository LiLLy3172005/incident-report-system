<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\Admin\CommunityController as AdminCommunityController;
// Breeze auth routes
require __DIR__.'/auth.php';

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/{post}', [CommunityController::class, 'show'])->name('community.show');


// Report routes (cần đăng nhập)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/my-reports', [ReportController::class, 'userReports'])->name('reports.my');  // ← ĐÃ CÓ
    Route::get('/my-reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    
    Route::post('/reports/step1', [ReportController::class, 'storeStep1'])->name('reports.step1');
    Route::post('/reports/step2', [ReportController::class, 'storeStep2'])->name('reports.step2');
    Route::post('/reports/step3', [ReportController::class, 'storeStep3'])->name('reports.step3');
    Route::post('/reports/final', [ReportController::class, 'storeFinal'])->name('reports.final');

    Route::get('/heatmap', [App\Http\Controllers\HeatmapController::class, 'index'])->name('heatmap');

   Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::post('/community/{post}/comment', [CommunityController::class, 'comment'])->name('community.comment');
    Route::post('/community/{post}/like', [CommunityController::class, 'like'])->name('community.like');
    
});

// Auth routes (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
 

});

// Logout
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ExportController::class, 'reports'])->name('reports.export');
    Route::get('/reports/{id}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{id}/approve', [AdminReportController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{id}/reject', [AdminReportController::class, 'reject'])->name('reports.reject');


    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{id}/unban', [UserController::class, 'unban'])->name('users.unban');
    Route::post('/users/{id}/reset-strikes', [UserController::class, 'resetStrikes'])->name('users.reset-strikes');

    Route::get('/heatmap', [App\Http\Controllers\HeatmapController::class, 'index'])->name('heatmap');
     
        Route::get('/community', [AdminCommunityController::class, 'index'])->name('community.pending');
    Route::post('/community/{post}/approve', [AdminCommunityController::class, 'approve'])->name('community.approve');
    Route::post('/community/{post}/reject', [AdminCommunityController::class, 'reject'])->name('community.reject');
    Route::delete('/community/{post}', [AdminCommunityController::class, 'destroy'])->name('community.destroy');
});

// API routes
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::post('/reports', [ReportApiController::class, 'store']);
    Route::get('/my-reports', [ReportApiController::class, 'userReports']);
    Route::get('/reports/{id}', [ReportApiController::class, 'show']);
});