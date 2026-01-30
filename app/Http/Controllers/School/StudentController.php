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
    public function index(Request $request)
    {
        $school = Auth::user()->school;
        
        $query = User::whereHas('cards', function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id);
        })->with(['cards' => function ($query) use ($school) {
            $query->where('owner_type', 'App\Models\School')
                  ->where('owner_id', $school->school_id)
                  ->with('albums');
        }]);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $students = $query->paginate($request->get('per_page', 10));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'students' => $students->items(),
                    'pagination' => [
                        'total' => $students->total(),
                        'per_page' => $students->perPage(),
                        'current_page' => $students->currentPage(),
                        'last_page' => $students->lastPage(),
                        'from' => $students->firstItem(),
                        'to' => $students->lastItem()
                    ]
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
