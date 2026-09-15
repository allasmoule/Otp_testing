<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZendSms\Laravel\Facades\ZendSms;

class DianaHostSmsService
{
    protected string $apiKey;
    protected string $senderId;
    protected string $apiUrl;
    protected bool $testMode;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('dianahost.api_key', config('zendsms.api_key'));
        $this->senderId = (string) config('dianahost.sender_id', config('zendsms.default_sender_id', 'DIANAHOST'));
        $this->apiUrl = (string) config('dianahost.api_url', 'https://api.zendsms.com/api/v1/send-sms');
        $this->testMode = (bool) config('dianahost.test_mode', false);
        $this->timeout = (int) config('dianahost.timeout', 15);
    }

    /**
     * Send Transactional OTP SMS via DianaHost / ZendSMS API
     *
     * @param string $phone Normalized phone number (+8801XXXXXXXXX)
     * @param string $message Prepared SMS text
     * @return array Standardized result array
     */
    public function sendSms(string $phone, string $message): array
    {
        $maskedPhone = substr($phone, 0, 5) . '****' . substr($phone, -4);

        // TEST MODE HANDLER
        if ($this->testMode) {
            Log::info("DianaHost SMS [TEST MODE]: Simulated SMS to {$maskedPhone}");
            return [
                'success' => true,
                'message' => 'SMS simulated successfully (Test Mode enabled)',
                'provider_status' => 'SIMULATED',
                'message_id' => 'test_msg_' . time()
            ];
        }

        // Validate API Key configuration
        if (empty($this->apiKey)) {
            Log::error('DianaHost SMS Error: API Key is not set in environment.');
            return [
                'success' => false,
                'message' => 'SMS provider credentials not configured in .env file.',
                'provider_status' => 'NOT_CONFIGURED'
            ];
        }

        try {
            Log::info("DianaHost SMS: Initiating live SMS request to {$maskedPhone}");

            // Direct HTTP Request with 5s fast timeout & SSL bypass
            $httpRequest = Http::timeout(5)
                ->withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ]);

            $httpResponse = $httpRequest->post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender_id' => $this->senderId,
                'to' => $phone,
                'recipient' => $phone,
                'mobile' => $phone,
                'message' => $message,
            ]);

            if ($httpResponse->successful()) {
                $data = $httpResponse->json();
                Log::info("DianaHost SMS (HTTP Direct) Success to {$maskedPhone}");

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully via DianaHost.',
                    'provider_status' => 'SENT',
                    'message_id' => $data['data']['message_id'] ?? $data['message_id'] ?? null
                ];
            }

            $status = $httpResponse->status();
            Log::error("DianaHost SMS HTTP Error {$status} for {$maskedPhone}: " . $httpResponse->body());

            $errorMessage = match ($status) {
                401, 403 => 'SMS provider authentication failed. Check API key.',
                402 => 'Insufficient SMS provider account balance.',
                429 => 'SMS provider rate limit exceeded.',
                default => 'SMS gateway service error (' . $status . ').'
            };

            return [
                'success' => false,
                'message' => $errorMessage,
                'provider_status' => 'HTTP_ERROR_' . $status
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("DianaHost SMS Connection Timeout for {$maskedPhone}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS gateway request timed out. Please try again.',
                'provider_status' => 'TIMEOUT'
            ];
        } catch (\Throwable $e) {
            Log::error("DianaHost SMS Exception for {$maskedPhone}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS dispatch error occurred.',
                'provider_status' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Query Delivery Report for a given message ID
     */
    public function getDeliveryReport(string $messageId): array
    {
        if ($this->testMode) {
            return ['success' => true, 'status' => 'delivered'];
        }

        try {
            $dlrUrl = config('dianahost.dlr_url', 'https://api.zendsms.com/api/v1/dlr');
            $response = Http::timeout($this->timeout)
                ->withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get($dlrUrl, ['message_id' => $messageId]);

            if ($response->successful()) {
                return ['success' => true, 'status' => $response->json('status', 'unknown')];
            }
        } catch (\Throwable $e) {
            Log::error("DianaHost DLR query failed: " . $e->getMessage());
        }

        return ['success' => false, 'status' => 'unknown'];
    }
}
