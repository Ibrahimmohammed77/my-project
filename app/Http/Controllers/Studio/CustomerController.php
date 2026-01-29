<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $studio = Auth::user()->studio;
        
        // Fetch users who hold cards owned by this studio
        $customers = User::whereHas('cards', function($q) use ($studio) {
            $q->where('owner_id', $studio->studio_id)
              ->where('owner_type', \App\Models\Studio::class);
        })->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'customers' => $customers
                ]
            ]);
        }

        return view('spa.studio-customers.index', compact('customers'));
    }

    // Other CRUD methods...
}
