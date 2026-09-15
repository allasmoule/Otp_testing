<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show Protected Admin Dashboard View with Utility Bill Services
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $totalBillsPaidCount = BillPayment::where('status', 'SUCCESS')->count();
        $totalAmountCollected = BillPayment::where('status', 'SUCCESS')->sum('amount');
        $recentPayments = BillPayment::latest()->take(15)->get();

        return view('admin.dashboard', [
            'user' => $user,
            'totalBillsPaidCount' => $totalBillsPaidCount,
            'totalAmountCollected' => $totalAmountCollected,
            'recentPayments' => $recentPayments,
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
