<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $schools = \App\Domain\Core\Models\School::with(['type', 'level', 'status'])->latest()->get();
            return response()->json([
                'success' => true,
                'data' => ['schools' => $schools]
            ]);
        }
        
        $types = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SCHOOL_TYPE');
        })->get(['lookup_value_id', 'code', 'name']);

        $levels = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SCHOOL_LEVEL');
        })->get(['lookup_value_id', 'code', 'name']);

        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
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

        \App\Domain\Core\Models\School::create($validated);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return \App\Domain\Core\Models\School::with(['type', 'level', 'status'])->findOrFail($id);
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

        $school = \App\Domain\Core\Models\School::findOrFail($id);
        $school->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $school = \App\Domain\Core\Models\School::findOrFail($id);
        $school->delete();
        return response()->json(['success' => true]);
    }
}
