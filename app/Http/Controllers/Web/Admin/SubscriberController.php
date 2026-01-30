<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the subscribers.
     */
    public function index()
    {
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'USER_STATUS');
        })->where('is_active', true)->get();

        return view('spa.subscribers.index', compact('statuses'));
    }
}
