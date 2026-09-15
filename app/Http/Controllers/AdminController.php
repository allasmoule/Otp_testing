<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use App\Models\User;
use App\Services\DianaHostSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected DianaHostSmsService $smsService;

    public function __construct(DianaHostSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show Protected Admin Dashboard View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $totalUsers = User::count();
        $totalOtpsSent = OtpVerification::count();
        $verifiedOtpsCount = OtpVerification::whereNotNull('verified_at')->count();
        $pendingOtpsCount = OtpVerification::whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->count();

        $recentUsers = User::latest()->take(10)->get();
        $recentOtps = OtpVerification::latest()->take(10)->get();

        $senderId = config('dianahost.sender_id');
        $apiKeyConfigured = !empty(config('dianahost.api_key'));

        return view('admin.dashboard', [
            'user' => $user,
            'sessionTime' => now()->format('h:i A'),
            'verifiedAt' => $user->created_at ? $user->created_at->format('M d, Y') : 'Today',
            'totalUsers' => $totalUsers,
            'totalOtpsSent' => $totalOtpsSent,
            'verifiedOtpsCount' => $verifiedOtpsCount,
            'pendingOtpsCount' => $pendingOtpsCount,
            'recentUsers' => $recentUsers,
            'recentOtps' => $recentOtps,
            'senderId' => $senderId,
            'apiKeyConfigured' => $apiKeyConfigured,
        ]);
    }

    /**
     * Purge Expired OTP Logs from Database
     */
    public function purgeExpiredOtps(Request $request)
    {
        $deleted = OtpVerification::where('expires_at', '<', now())->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', "Cleaned up {$deleted} expired OTP records from database.");
    }

    /**
     * Send Quick Test SMS via Admin Panel
     */
    public function sendTestSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'message' => 'required|string|max:160'
        ]);

        $res = $this->smsService->sendSms($request->input('phone'), $request->input('message'));

        if ($res['success']) {
            return redirect()->route('admin.dashboard')
                ->with('status', '✅ Live SMS dispatched successfully to ' . $request->input('phone'));
        }

        return redirect()->route('admin.dashboard')
            ->with('error', '❌ SMS sending failed: ' . $res['message']);
    }
}
