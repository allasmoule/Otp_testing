<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | DianaHost SMS API Credentials
    |--------------------------------------------------------------------------
    |
    | Environment variable keys for DianaHost OTP SMS Gateway.
    |
    */

    'api_key' => env('DIANAHOST_API_KEY'),

    'sender_id' => env('DIANAHOST_SENDER_ID'),

    'api_url' => env('DIANAHOST_API_URL', 'https://bulk-sms.dianahost.com/api/v1/send'),

    'timeout' => (int) env('DIANAHOST_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Development / Local Test Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, SMS sending is simulated locally without hitting provider API.
    |
    */

    'test_mode' => (bool) env('OTP_TEST_MODE', false),

    'test_code' => env('OTP_TEST_CODE', '123456'),

    /*
    |--------------------------------------------------------------------------
    | OTP SMS Transactional Message Template
    |--------------------------------------------------------------------------
    */

    'message_template' => env('OTP_SMS_TEMPLATE', 'Your verification code is ##otp##. This code is valid for 5 minutes. Do not share this code with anyone.'),


    /*
    |--------------------------------------------------------------------------
    | OTP Security Rules
    |--------------------------------------------------------------------------
    */

    'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 5),

    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    'resend_cooldown_seconds' => (int) env('OTP_RESEND_COOLDOWN', 60),

    'max_requests_per_hour' => (int) env('OTP_MAX_HOURLY_REQUESTS', 5),

];
