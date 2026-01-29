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
        $studios = Studio::with('user', 'status', 'subscription')->get();

        $statuses = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'studios' => $studios
                ]
            ]);
        }

        return view('spa.studios.index', compact('studios', 'statuses'));
    }
}
