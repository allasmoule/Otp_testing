<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected DianaHostSmsService $smsService;

    public function __construct(DianaHostSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Normalize Bangladesh Mobile Phone Number to +8801XXXXXXXXX format
     */
    public function normalizePhone(string $rawPhone): ?string
    {
        $digits = preg_replace('/\D/', '', $rawPhone);

        if (empty($digits)) {
            return null;
        }

        // Extract 10-digit core Bangladesh mobile number starting with 13-19 (e.g. 17XXXXXXXX, 15XXXXXXXX)
        if (preg_match('/(1[3-9]\d{8})$/', $digits, $matches)) {
            return '+880' . $matches[1];
        }

        return null;
    }

    /**
     * Send or Resend OTP Code
     */
    public function sendOtp(string $rawPhone, ?string $ipAddress = null): array
    {
        $phone = $this->normalizePhone($rawPhone);

        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Invalid Bangladesh mobile number format.'
            ];
        }

        $maskedPhone = substr($phone, 0, 5) . '****' . substr($phone, -4);

        // 1. Rate Limiting: Max 5 requests per hour
        $hourlyCount = OtpVerification::where('phone', $phone)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        $maxHourly = (int) config('dianahost.max_requests_per_hour', 5);

        if ($hourlyCount >= $maxHourly) {
            Log::warning("OTP Rate Limit Exceeded for {$maskedPhone}");
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.'
            ];
        }

        // 2. Cooldown check: 60 seconds between resends
        $latestRecord = OtpVerification::where('phone', $phone)
            ->latest()
            ->first();

        $cooldownSeconds = (int) config('dianahost.resend_cooldown_seconds', 60);

        if ($latestRecord && $latestRecord->last_sent_at) {
            $secondsSinceSent = (int) abs(now()->diffInSeconds($latestRecord->last_sent_at));
            if ($secondsSinceSent < $cooldownSeconds) {
                $wait = (int) ceil($cooldownSeconds - $secondsSinceSent);
                return [
                    'success' => false,
                    'message' => "Please wait {$wait} seconds before requesting a new OTP."
                ];
            }
        }


        // 3. Generate Cryptographically Secure 6-digit OTP
        $testMode = (bool) config('dianahost.test_mode', false);
        if ($testMode) {
            $otpCode = (string) config('dianahost.test_code', '123456');
            Log::info("OTP System [TEST MODE]: Generated Code for {$maskedPhone}");
        } else {
            $otpCode = (string) random_int(100000, 999999);
        }

        // 4. Hash OTP with bcrypt before saving to DB
        $otpHash = Hash::make($otpCode);
        $expiryMinutes = (int) config('dianahost.expiry_minutes', 5);

        // Record or Update OTP DB entry
        $record = OtpVerification::create([
            'phone' => $phone,
            'otp_hash' => $otpHash,
            'expires_at' => now()->addMinutes($expiryMinutes),
            'attempts' => 0,
            'request_count' => ($latestRecord ? $latestRecord->request_count + 1 : 1),
            'last_sent_at' => now(),
            'ip_address' => $ipAddress,
        ]);

        // 5. Build SMS Message Text (Matches approved DianaHost/ZendSMS Portal Template)
        $template = config('dianahost.message_template', 'Your verification code is ##otp##. This code is valid for 5 minutes. Do not share this code with anyone.');
        $messageText = str_replace(['##otp##', '{OTP}'], $otpCode, $template);


        // 6. Send via DianaHost SMS Gateway
        $smsResult = $this->smsService->sendSms($phone, $messageText);

        if (!$smsResult['success']) {
            return [
                'success' => false,
                'message' => $smsResult['message']
            ];
        }

        return [
            'success' => true,
            'message' => 'OTP sent successfully.',
            'test_mode' => $testMode,
            'test_otp' => $testMode ? $otpCode : null // Exposed ONLY when OTP_TEST_MODE=true for local dev
        ];
    }

    /**
     * Verify Submitted OTP Code
     */
    public function verifyOtp(string $rawPhone, string $enteredOtp): array
    {
        $phone = $this->normalizePhone($rawPhone);

        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Invalid Bangladesh mobile number format.'
            ];
        }

        $record = OtpVerification::where('phone', $phone)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'No active OTP verification found. Please request a new code.'
            ];
        }

        // Check Expiry
        if ($record->isExpired()) {
            return [
                'success' => false,
                'message' => 'OTP code has expired. Please request a new code.'
            ];
        }

        // Check Max Attempts (Brute Force Protection)
        if ($record->isMaxAttemptsExceeded()) {
            $record->update(['otp_hash' => null]); // Invalidate code
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.'
            ];
        }

        // Check Code Match using Hash::check()
        $isValid = Hash::check($enteredOtp, $record->otp_hash);

        // Fallback check if test mode is enabled
        if (!$isValid && config('dianahost.test_mode') && $enteredOtp === (string) config('dianahost.test_code')) {
            $isValid = true;
        }

        if ($isValid) {
            // SUCCESS: Invalidate OTP & Mark Verified
            $record->update([
                'verified_at' => now(),
                'otp_hash' => null // Single-use erasure
            ]);

            // Update user table if registered user exists
            User::where('phone', str_replace('+880', '0', $phone))
                ->orWhere('phone', str_replace('+880', '', $phone))
                ->orWhere('phone', $phone)
                ->update(['email_verified_at' => now()]);

            return [
                'success' => true,
                'message' => 'Phone number verified successfully.'
            ];
        }

        // FAIL: Increment attempts counter
        $record->increment('attempts');

        if ($record->attempts >= (int) config('dianahost.max_attempts', 5)) {
            $record->update(['otp_hash' => null]);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.'
            ];
        }

        $remaining = config('dianahost.max_attempts', 5) - $record->attempts;

        return [
            'success' => false,
            'message' => "Invalid OTP code. You have {$remaining} attempt(s) remaining."
        ];
    }
}
