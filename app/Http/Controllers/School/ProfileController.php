<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdateSchoolProfileRequest;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the school profile edit form.
     */
    public function edit()
    {
        $school = Auth::user()->school;

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'user' => Auth::user()
                ]
            ]);
        }

        return view('spa.school-profile.index', compact('school'));
    }

    /**
     * Update school profile data.
     */
    public function update(UpdateSchoolProfileRequest $request)
    {
        $user = Auth::user();
        $school = $user->school;

        $validated = $request->validated();

        if (!$school) {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'School not found'], 404)
                : redirect()->back()->with('error', 'المدرسة غير موجودة');
        }

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $validated['name']]);
        }

        // Handle logo upload if needed in future (placeholder logic)
        
        $data = $request->except(['email', 'phone', 'user_id', 'school_id', '_token', '_method', 'logo']);
        $school->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات المدرسة بنجاح',
                'data' => $school
            ]);
        }

        return redirect()->route('school.profile.edit')->with('success', 'تم تحديث بيانات المدرسة بنجاح');
    }
}
