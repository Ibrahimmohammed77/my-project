<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    /**
     * Display a listing of studios.
     */
    public function index(Request $request): View|JsonResponse
    {
        $studios = Studio::with('user', 'subscription')
            ->filter($request->only('search', 'status_id'))
            ->paginate($request->get('per_page', 15));

        $statuses = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($studios, 'studios', 'تم استرجاع الاستوديوهات بنجاح');
        }

        return view('spa.studios.index', compact('studios', 'statuses'));
    }
}
