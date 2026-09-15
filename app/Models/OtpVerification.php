<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $table = 'otp_verifications';

    protected $fillable = [
        'phone',
        'otp_hash',
        'expires_at',
        'attempts',
        'request_count',
        'last_sent_at',
        'verified_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
            'request_count' => 'integer',
        ];
    }

    /**
     * Check if OTP has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at === null || now()->greaterThan($this->expires_at);
    }

    /**
     * Check if maximum verification attempts exceeded
     */
    public function isMaxAttemptsExceeded(): bool
    {
        $maxAttempts = (int) config('dianahost.max_attempts', 5);
        return $this->attempts >= $maxAttempts;
    }

    /**
     * Check if phone is verified
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
