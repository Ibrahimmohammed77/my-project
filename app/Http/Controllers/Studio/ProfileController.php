<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the studio profile edit form.
     */
    public function edit()
    {
        $studio = Auth::user()->studio;
        return view('studio.profile.edit', compact('studio'));
    }

    /**
     * Update studio profile data.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $studio = $user->studio;

        if (!$studio) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Studio not found'], 404)
                : redirect()->back()->with('error', 'الاستوديو غير موجود');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url',
        ]);

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $validated['name']]);
        }

        $data = $request->except(['email', 'phone', 'user_id', 'studio_id', '_token', '_method']);
        $studio->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات الاستوديو بنجاح',
                'data' => $studio
            ]);
        }

        return redirect()->route('studio.profile.edit')->with('success', 'تم تحديث بيانات الاستوديو بنجاح');
    }
}
