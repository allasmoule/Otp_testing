<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StakeholderController extends Controller
{
    /**
     * Show Main Stakeholder Control Panel Dashboard
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Retrieve summary statistics
        $totalAccounts = User::count();
        $totalPaymentsCount = BillPayment::where('status', 'SUCCESS')->count();
        $totalRevenue = BillPayment::where('status', 'SUCCESS')->sum('amount');
        
        $bkashRevenue = BillPayment::where('payment_method', 'bkash')->where('status', 'SUCCESS')->sum('amount');
        $nagadRevenue = BillPayment::where('payment_method', 'nagad')->where('status', 'SUCCESS')->sum('amount');
        $bkashCount = BillPayment::where('payment_method', 'bkash')->where('status', 'SUCCESS')->count();
        $nagadCount = BillPayment::where('payment_method', 'nagad')->where('status', 'SUCCESS')->count();

        // Retrieve full lists for stakeholder tables
        $allUsers = User::latest()->get();
        $allPayments = BillPayment::latest()->get();

        return view('stakeholder.dashboard', [
            'user' => $currentUser,
            'totalAccounts' => $totalAccounts,
            'totalPaymentsCount' => $totalPaymentsCount,
            'totalRevenue' => $totalRevenue,
            'bkashRevenue' => $bkashRevenue,
            'nagadRevenue' => $nagadRevenue,
            'bkashCount' => $bkashCount,
            'nagadCount' => $nagadCount,
            'allUsers' => $allUsers,
            'allPayments' => $allPayments,
        ]);
    }
}
