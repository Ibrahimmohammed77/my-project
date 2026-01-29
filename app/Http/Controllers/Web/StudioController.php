<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LookupValue;
use App\Models\Studio;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $studios = Studio::with(['status'])->latest()->get();
            return response()->json([
                'success' => true,
                'data' => ['studios' => $studios]
            ]);
        }

        $statuses = LookupValue::whereHas('master', function($q) {
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

        Studio::create($validated);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return Studio::with(['status'])->findOrFail($id);
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

        $studio = Studio::findOrFail($id);
        $studio->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        $studio->delete();
        return response()->json(['success' => true]);
    }
}
