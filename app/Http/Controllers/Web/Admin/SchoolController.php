<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
{
    /**
     * Display a listing of schools.
     */
    public function index(Request $request): View|JsonResponse
    {
        $schools = School::with('user', 'status', 'type', 'level')->get();

        $statuses = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        $types = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_type');
        })->get();

        $levels = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_level');
        })->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'schools' => $schools
                ]
            ]);
        }

        return view('spa.schools.index', compact('schools', 'statuses', 'types', 'levels'));
    }
}
