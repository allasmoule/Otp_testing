# Student Account Registration OTP Verification & DianaHost SMS Integration Prompt

Use this master prompt in any AI assistant or project to instantly build/integrate the DianaHost SMS OTP verification system for Student Registration in Laravel.

---

## System Requirements & Specifications

1. **SMS Gateway**: DianaHost / ZendSMS API (`https://zendsms.com/api/v1/send-sms`).
2. **Sender ID**: `8809612781000` (Non-Masking Sender ID).
3. **Phone Normalization**: Convert all BD mobile formats (`017XXXXXXXX`, `17XXXXXXXX`, `+88017XXXXXXXX`, `88017XXXXXXXX`) cleanly to 10 core digits for DB storage and `88017XXXXXXXX` for API dispatch.
4. **OTP Rules**:
   - 6-digit random numeric code (e.g. `482910`).
   - Expiration time: 5 minutes.
   - Maximum attempt limit: 5 attempts per code.
   - Resend timer cooldown: 60 seconds.
5. **Workflow**:
   - **Step 1**: Student inputs Name & Mobile Number -> System sends OTP via DianaHost SMS.
   - **Step 2**: Student enters 6-digit OTP in 6 auto-advancing input boxes -> System verifies code.
   - **Step 3**: Student sets password -> Student account created, verified (`is_otp_verified = true`), auto-logged in, and redirected.

---

## 1. Environment Configuration (`.env` & `config/dianahost.php`)

Add to `.env`:

```env
DIANAHOST_SMS_API_KEY=your_dianahost_api_key_here
DIANAHOST_SMS_SENDER_ID=8809612781000
DIANAHOST_SMS_ENDPOINT=https://zendsms.com/api/v1/send-sms
```

Create `config/dianahost.php`:

```php
<?php

return [
    'api_key' => env('DIANAHOST_SMS_API_KEY'),
    'sender_id' => env('DIANAHOST_SMS_SENDER_ID', '8809612781000'),
    'endpoint' => env('DIANAHOST_SMS_ENDPOINT', 'https://zendsms.com/api/v1/send-sms'),
];
```

---

## 2. Database Migration (`otp_verifications` table)

Create `database/migrations/2026_01_01_000000_create_otp_verifications_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->string('otp_code', 6);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('otp_verifications');
    }
};
```

---

## 3. Eloquent Model (`app/Models/OtpVerification.php`)

Create `app/Models/OtpVerification.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model {
    protected $fillable = [
        'phone',
        'otp_code',
        'attempts',
        'expires_at',
        'verified_at',
        'last_sent_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public function isExpired(): bool {
        return now()->greaterThan($this->expires_at);
    }
}
```

---

## 4. DianaHost SMS & OTP Service (`app/Services/DianaHostSmsService.php`)

Create `app/Services/DianaHostSmsService.php`:

```php
<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DianaHostSmsService {

    /**
     * Clean & Normalize Bangladesh Mobile Number to standard 10-digit format
     */
    public function normalizePhone(string $phone): string {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '880')) {
            $digits = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        return $digits; // Returns 10 digits e.g. 1712345678
    }

    /**
     * Dispatch SMS via DianaHost API
     */
    public function sendSms(string $phone, string $message): array {
        $cleanPhone = $this->normalizePhone($phone);
        $fullPhone = '880' . $cleanPhone;

        $apiKey = config('dianahost.api_key');
        $senderId = config('dianahost.sender_id', '8809612781000');
        $endpoint = config('dianahost.endpoint', 'https://zendsms.com/api/v1/send-sms');

        try {
            $response = Http::withoutVerifying()->post($endpoint, [
                'api_key' => $apiKey,
                'type' => 'text',
                'contacts' => $fullPhone,
                'senderid' => $senderId,
                'msg' => $message,
            ]);

            $result = $response->json();
            Log::info("DianaHost SMS Response for {$fullPhone}:", $result ?? []);

            if ($response->successful() && isset($result['response_code']) && $result['response_code'] == 202) {
                return ['success' => true, 'message' => 'SMS sent successfully', 'data' => $result];
            }

            return ['success' => false, 'message' => $result['message'] ?? 'SMS API error', 'data' => $result];
        } catch (\Throwable $e) {
            Log::error("DianaHost SMS Exception for {$fullPhone}: " . $e->getMessage());
            return ['success' => false, 'message' => 'SMS gateway exception: ' . $e->getMessage()];
        }
    }

    /**
     * Generate & Send Student Registration OTP
     */
    public function generateAndSendOtp(string $phone): array {
        $cleanPhone = $this->normalizePhone($phone);
        $otpCode = (string) random_int(100000, 999999);

        OtpVerification::updateOrCreate(
            ['phone' => $cleanPhone],
            [
                'otp_code' => $otpCode,
                'attempts' => 0,
                'expires_at' => now()->addMinutes(5),
                'verified_at' => null,
                'last_sent_at' => now(),
            ]
        );

        $smsMessage = "Your Student Verification Code is: {$otpCode}. Valid for 5 minutes.";
        $smsResult = $this->sendSms($cleanPhone, $smsMessage);

        return [
            'success' => true,
            'message' => 'OTP dispatched to mobile number',
            'phone' => '+880' . $cleanPhone,
            'otp' => $otpCode,
            'sms_status' => $smsResult['success']
        ];
    }

    /**
     * Verify Submitted OTP Code
     */
    public function verifyOtp(string $phone, string $otpCode): array {
        $cleanPhone = $this->normalizePhone($phone);
        $record = OtpVerification::where('phone', $cleanPhone)->first();

        if (!$record) {
            return ['success' => false, 'message' => 'No OTP record found for this number.'];
        }

        if ($record->isExpired()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new code.'];
        }

        if ($record->attempts >= 5) {
            return ['success' => false, 'message' => 'Maximum attempt limit reached. Please request a new code.'];
        }

        $record->increment('attempts');

        if ($record->otp_code === trim($otpCode)) {
            $record->update(['verified_at' => now()]);
            return ['success' => true, 'message' => 'OTP verified successfully!'];
        }

        return ['success' => false, 'message' => 'Incorrect OTP verification code.'];
    }
}
```

---

## 5. Controller Implementation (`app/Http/Controllers/StudentAuthController.php`)

Create `app/Http/Controllers/StudentAuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DianaHostSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller {

    protected DianaHostSmsService $smsService;

    public function __construct(DianaHostSmsService $smsService) {
        $this->smsService = $smsService;
    }

    public function sendStudentOtp(Request $request) {
        $request->validate(['phone' => 'required|string|min:10']);
        $res = $this->smsService->generateAndSendOtp($request->input('phone'));
        return response()->json($res);
    }

    public function verifyStudentOtp(Request $request) {
        $request->validate(['phone' => 'required|string', 'otp' => 'required|string|size:6']);
        $res = $this->smsService->verifyOtp($request->input('phone'), $request->input('otp'));
        return response()->json($res);
    }

    public function registerStudent(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $cleanPhone = $this->smsService->normalizePhone($request->input('phone'));

        $user = User::updateOrCreate(
            ['phone' => $cleanPhone],
            [
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'role' => 'student',
                'is_otp_verified' => true,
            ]
        );

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Student Account registered successfully!',
            'redirect' => route('student.dashboard')
        ]);
    }
}
```

---

## 6. Routes Setup (`routes/web.php`)

Add routes:

```php
Route::post('/api/student/otp/send', [StudentAuthController::class, 'sendStudentOtp']);
Route::post('/api/student/otp/verify', [StudentAuthController::class, 'verifyStudentOtp']);
Route::post('/api/student/register', [StudentAuthController::class, 'registerStudent'])->name('student.register');
```

---

## 7. Frontend JavaScript (Auto-advancing 6-digit OTP & 60s Resend)

```javascript
// Send OTP Click Handler
document.getElementById('btn-send-otp').addEventListener('click', async () => {
    const phone = document.getElementById('student-phone').value;
    const res = await fetch('/api/student/otp/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
        body: JSON.stringify({ phone: phone })
    });
    const data = await res.json();
    if (data.success) {
        showStep2OtpVerification();
        startResendCountdown(60);
    }
});

// Auto-advancing OTP digit boxes
const otpDigits = document.querySelectorAll('.otp-digit');
otpDigits.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        if (e.target.value && index < 5) otpDigits[index + 1].focus();
    });
});
```
