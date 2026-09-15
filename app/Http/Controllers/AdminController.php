<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show Protected Admin Dashboard View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return view('admin.dashboard', [
            'user' => $user,
            'sessionTime' => now()->format('h:i A'),
            'verifiedAt' => $user->created_at ? $user->created_at->format('M d, Y') : 'Today'
        ]);
    }
}
