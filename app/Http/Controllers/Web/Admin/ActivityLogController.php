<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of variables.
     */
    public function index(): View
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
