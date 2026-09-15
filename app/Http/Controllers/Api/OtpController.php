<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP Request
     *
     * Endpoint: POST /api/otp/send
     */
    public function send(SendOtpRequest $request): JsonResponse
    {
        $result = $this->otpService->sendOtp(
            $request->input('phone'),
            $request->ip()
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'test_mode' => $result['test_mode'] ?? false,
                'test_otp' => $result['test_otp'] ?? null
            ], 200);
        }

        $statusCode = str_contains($result['message'], 'Too many') || str_contains($result['message'], 'Please wait') ? 429 : 400;

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], $statusCode);
    }

    /**
     * Verify Submitted OTP Code
     *
     * Endpoint: POST /api/otp/verify
     */
    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->otpService->verifyOtp(
            $request->input('phone'),
            $request->input('otp')
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 422);
    }

    /**
     * Resend OTP Request
     *
     * Endpoint: POST /api/otp/resend
     */
    public function resend(SendOtpRequest $request): JsonResponse
    {
        return $this->send($request);
    }
}
