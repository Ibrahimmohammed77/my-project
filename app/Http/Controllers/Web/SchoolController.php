<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LookupValue;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $schools = School::with(['type', 'level', 'status'])->latest()->get();
            return response()->json([
                'success' => true,
                'data' => ['schools' => $schools]
            ]);
        }

        $types = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SCHOOL_TYPE');
        })->get(['lookup_value_id', 'code', 'name']);

        $levels = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SCHOOL_LEVEL');
        })->get(['lookup_value_id', 'code', 'name']);

        $statuses = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SCHOOL_STATUS');
        })->get(['lookup_value_id', 'code', 'name']);

        return view('spa.schools.index', compact('types', 'levels', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'nullable|string|max:20',
            'school_type_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_level_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'website' => 'nullable|url',
            'city' => 'nullable|string',
        ]);

        School::create($validated);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return School::with(['type', 'level', 'status'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email,' . $id . ',school_id',
            'phone' => 'nullable|string|max:20',
            'school_type_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_level_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'website' => 'nullable|url',
            'city' => 'nullable|string',
        ]);

        $school = School::findOrFail($id);
        $school->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $school->delete();
        return response()->json(['success' => true]);
    }
}
