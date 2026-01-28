<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $studios = \App\Domain\Core\Models\Studio::with(['status'])->latest()->get();
            return response()->json([
                'success' => true,
                'data' => ['studios' => $studios]
            ]);
        }
        
        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'STUDIO_STATUS');
        })->get(['lookup_value_id', 'code', 'name']);

        return view('spa.studios.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:studios,email',
            'phone' => 'nullable|string|max:20',
            'studio_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'website' => 'nullable|url',
        ]);

        \App\Domain\Core\Models\Studio::create($validated);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return \App\Domain\Core\Models\Studio::with(['status'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:studios,email,' . $id . ',studio_id',
            'phone' => 'nullable|string|max:20',
            'studio_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'website' => 'nullable|url',
        ]);

        $studio = \App\Domain\Core\Models\Studio::findOrFail($id);
        $studio->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $studio = \App\Domain\Core\Models\Studio::findOrFail($id);
        $studio->delete();
        return response()->json(['success' => true]);
    }
}
