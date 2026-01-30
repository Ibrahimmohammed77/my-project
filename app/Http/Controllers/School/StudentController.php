<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of students who have activated school cards.
     */
    public function index()
    {
        $school = Auth::user()->school;
        
        // Get users who hold cards owned by this school
        $students = User::whereHas('cards', function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id);
        })->with(['cards' => function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id)
                  ->with('albums');
        }])->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'students' => $students
                ]
            ]);
        }

        return view('spa.school-students.index', compact('students'));
    }

    /**
     * Display student details and their associated albums in this school.
     */
    public function show($id)
    {
        $school = Auth::user()->school;
        
        $student = User::whereHas('cards', function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id);
        })->with(['cards' => function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id)
                  ->with('albums');
        }])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'student' => $student
                ]
            ]);
        }

        return view('spa.school-students.show', compact('student'));
    }
}
