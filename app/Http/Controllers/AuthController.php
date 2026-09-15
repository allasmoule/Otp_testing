<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use ZendSms\Laravel\Facades\ZendSms;

class AuthController extends Controller
{
    /**
     * Show Auth Portal (Login & OTP Sign Up)
     */
    public function showAuthPortal()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.portal');
    }

    /**
     * Send OTP Code via ZendSMS (or simulation mode if API key default)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'phone' => 'required|string|min:10',
        ]);

        $phone = preg_replace('/\D/', '', $request->input('phone'));
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        $fullPhone = '+880' . $phone;

        // Generate 6-digit random OTP
        $otpCode = (string) random_int(100000, 999999);

        // Store OTP & metadata in Laravel session
        session([
            'signup_name' => $request->input('name'),
            'signup_phone' => $phone,
            'signup_full_phone' => $fullPhone,
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(5)->timestamp,
            'otp_verified' => false
        ]);

        $zendsmsSent = false;
        $smsError = null;

        // Attempt sending via ZendSMS package if configured
        try {
            $senderId = config('zendsms.sender_id', 'ZENDSMS');
            $apiKey = config('zendsms.api_key');

            if ($apiKey && $apiKey !== 'sk_your_api_key' && $apiKey !== 'sk_test_zendsms_key') {
                ZendSms::sendSms($fullPhone, $senderId, "Your verification code is: {$otpCode}. Valid for 5 minutes.");
                $zendsmsSent = true;
            }
        } catch (\Throwable $e) {
            Log::warning('ZendSMS API send warning: ' . $e->getMessage());
            $smsError = $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'phone' => $fullPhone,
            'otp' => $otpCode, // Returned for interactive on-screen toast simulation
            'zendsms_sent' => $zendsmsSent,
            'sms_error' => $smsError
        ]);
    }

    /**
     * Verify Submitted OTP Code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $enteredCode = $request->input('otp_code');
        $sessionCode = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$sessionCode) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found. Please request a new code.'
            ], 422);
        }

        if (now()->timestamp > $expiresAt) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired. Please resend code.'
            ], 422);
        }

        if ($enteredCode === $sessionCode) {
            session(['otp_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Incorrect OTP code. Please check and try again.'
        ], 422);
    }

    /**
     * Complete Registration after OTP Verification
     */
    public function register(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $name = $request->input('name', session('signup_name'));
        $rawPhone = $request->input('phone', session('signup_phone'));

        if (!$rawPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Registration session timed out. Please verify your phone number via OTP first.'
            ], 422);
        }

        // Clean digits
        $phone = preg_replace('/\D/', '', $rawPhone);
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        $formattedPhone = '+880' . $phone;

        // Check verification in Session OR OtpVerification database table
        $isSessionVerified = session('otp_verified', false);
        $isDbVerified = \App\Models\OtpVerification::where('phone', $formattedPhone)
            ->whereNotNull('verified_at')
            ->exists();

        if (!$isSessionVerified && !$isDbVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your phone number via OTP first.'
            ], 422);
        }

        // Create or update user in database
        $user = User::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => $name ?? 'Admin User',
                'password' => Hash::make($request->input('password')),
                'is_otp_verified' => true
            ]
        );

        // Auto-login user right after registration & OTP verification
        Auth::login($user);
        $request->session()->regenerate();

        // Clean up OTP session data
        session()->forget(['signup_name', 'signup_phone', 'otp_code', 'otp_expires_at', 'otp_verified']);

        return response()->json([
            'success' => true,
            'message' => 'Account created & verified! Opening Admin Dashboard...',
            'phone' => '0' . $phone,
            'redirect' => route('admin.dashboard')
        ]);
    }



    /**
     * Password Login (No OTP needed for verified users)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($request->input('login_identifier'));
        $password = $request->input('password');

        // Extract digits if user entered a phone number
        $cleanPhone = preg_replace('/\D/', '', $identifier);
        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        // Attempt login by phone or email
        $user = User::where('phone', $identifier)
            ->orWhere('phone', $cleanPhone)
            ->orWhere('email', $identifier)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => route('admin.dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid mobile number or password.'
        ], 422);
    }

    /**
     * Logout Admin User
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
