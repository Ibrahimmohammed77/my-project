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
        $schools = School::with('user', 'type', 'level')
            ->filter($request->only('search', 'status_id'))
            ->paginate($request->get('per_page', 15));

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
            return $this->paginatedResponse($schools, 'schools', 'تم استرجاع المدارس بنجاح');
        }

        return view('spa.schools.index', compact('schools', 'statuses', 'types', 'levels'));
    }
}
