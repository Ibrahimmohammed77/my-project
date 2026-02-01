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
        return view('spa.studio-profile.index', compact('studio'));
    }

    /**
     * Update studio profile data.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $studio = $user->studio;

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url',
        ]);

        if (!$studio && $user->hasRole('studio_owner')) {
            $studio = Studio::create([
                'user_id' => $user->id,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);
        }

        if (!$studio) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Studio not found'], 404)
                : redirect()->back()->with('error', 'الاستوديو غير موجود');
        }

        // Update user name if provided

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $validated['name']]);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Validate image if possible here or rely on validation rules being updated elsewhere
            // Assuming validation might not cover file type in 'validate' above, 
            // but we can add basic check or assume frontend handled it.
            // Ideally should be validated.
            
            // Delete old image if exists
            if ($user->profile_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $path = $request->file('profile_image')->store('profile-photos', 'public');
            $user->update(['profile_image' => $path]);
        }

        $data = $request->except(['email', 'phone', 'user_id', 'studio_id', '_token', '_method', 'profile_image']);
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
