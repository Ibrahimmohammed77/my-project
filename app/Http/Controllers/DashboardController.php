<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Identity\Models\Account;
use App\Domain\Core\Models\Studio;
use App\Domain\Core\Models\School;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات الحسابات
        $stats = [
            'total_accounts' => Account::count(),
            'active_accounts' => Account::active()->count(), // ScopeActive should exist
            'studios_count' => Studio::count(),
            'schools_count' => School::count(),
            'new_accounts_today' => Account::whereDate('created_at', today())->count(),
        ];

        // آخر الحسابات المسجلة
        $latestAccounts = Account::with('status')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'latestAccounts'));
    }
}
