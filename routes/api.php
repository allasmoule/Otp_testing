<?php

use App\Http\Controllers\Api\OtpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile OTP Verification API Routes
|--------------------------------------------------------------------------
|
| POST /api/otp/send   - Send OTP SMS to mobile
| POST /api/otp/verify - Verify 6-digit OTP code
| POST /api/otp/resend - Resend OTP code with cooldown
|
*/

Route::prefix('otp')->group(function () {
    Route::post('/send', [OtpController::class, 'send'])->name('api.otp.send');
    Route::post('/verify', [OtpController::class, 'verify'])->name('api.otp.verify');
    Route::post('/resend', [OtpController::class, 'resend'])->name('api.otp.resend');
});
