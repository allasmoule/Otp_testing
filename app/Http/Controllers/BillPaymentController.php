<?php

namespace App\Http\Controllers;

use App\Models\BillPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillPaymentController extends Controller
{
    /**
     * Process Bill Payment via bKash or Nagad Merchant API Simulation
     */
    public function pay(Request $request)
    {
        $request->validate([
            'payer_name' => 'required|string|max:255',
            'bill_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bkash,nagad',
            'account_number' => 'required|string|min:10|max:15',
            'pin' => 'required|string|min:4|max:6',
        ]);

        // Generate realistic Merchant Transaction Reference ID
        $prefix = strtoupper($request->input('payment_method')) === 'BKASH' ? 'BKS' : 'NGD';
        $trxId = $prefix . strtoupper(Str::random(8)) . rand(10, 99);

        $payment = BillPayment::create([
            'user_id' => Auth::id(),
            'payer_name' => $request->input('payer_name'),
            'bill_type' => $request->input('bill_type'),
            'amount' => $request->input('amount'),
            'payment_method' => strtolower($request->input('payment_method')),
            'account_number' => $request->input('account_number'),
            'trx_id' => $trxId,
            'status' => 'SUCCESS',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment completed successfully!',
            'trx_id' => $trxId,
            'payment' => [
                'id' => $payment->id,
                'payer_name' => $payment->payer_name,
                'bill_type' => $payment->bill_type,
                'amount' => number_format($payment->amount, 2),
                'payment_method' => strtoupper($payment->payment_method),
                'account_number' => $payment->account_number,
                'trx_id' => $payment->trx_id,
                'date' => $payment->created_at->format('d M Y, h:i A'),
            ]
        ]);
    }

    /**
     * Get Bill Payment History Endpoint
     */
    public function history(Request $request)
    {
        $payments = BillPayment::latest()->take(20)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'payer_name' => $p->payer_name,
                'bill_type' => $p->bill_type,
                'amount' => number_format($p->amount, 2),
                'payment_method' => strtoupper($p->payment_method),
                'account_number' => $p->account_number,
                'trx_id' => $p->trx_id,
                'status' => $p->status,
                'date' => $p->created_at->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }
}
