<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest Auth Routes
Route::get('/', [AuthController::class, 'showAuthPortal'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// OTP Sign-Up Routes
Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('otp.send');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Protected Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
