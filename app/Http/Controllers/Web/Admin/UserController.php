<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\UseCases\Admin\CreateUserUseCase;
use App\UseCases\Admin\ListUsersUseCase;
use App\UseCases\Admin\UpdateUserUseCase;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $createUserUseCase;
    protected $listUsersUseCase;
    protected $updateUserUseCase;

    public function __construct(
        CreateUserUseCase $createUserUseCase,
        ListUsersUseCase $listUsersUseCase,
        UpdateUserUseCase $updateUserUseCase
    ) {
        $this->createUserUseCase = $createUserUseCase;
        $this->listUsersUseCase = $listUsersUseCase;
        $this->updateUserUseCase = $updateUserUseCase;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'role_id', 'status_id', 'type_id']);
        $users = $this->listUsersUseCase->execute($filters, $request->get('per_page', 15));

        $roles = Role::all();
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        $types = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_type');
        })->get();

        $schoolTypes = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_type');
        })->get();

        $schoolLevels = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_level');
        })->get();

        $accountTypeIds = [
            'STUDIO' => $types->where('code', 'studio_owner')->first()?->lookup_value_id,
            'SCHOOL' => $types->where('code', 'school_admin')->first()?->lookup_value_id,
        ];

        return view('spa.accounts.index', compact('users', 'roles', 'statuses', 'types', 'filters', 'schoolTypes', 'schoolLevels', 'accountTypeIds'));
    }

    /**
     * Store a newly created user.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            $this->createUserUseCase->execute($request->validated());
            return redirect()->route('spa.accounts')->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء المستخدم')->withInput();
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // For SPA/Modal, we might just return the user data as JSON
        // Or if it's a separate page, return a view
        // simpler for now to return JSON if it's an AJAX request, or view if standard
        
        $roles = Role::all();
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        if (request()->wantsJson()) {
             return response()->json([
                'user' => $user->load('roles'),
                'roles' => $roles,
                'statuses' => $statuses
            ]);
        }

        return view('spa.accounts.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->updateUserUseCase->execute($user, $request->validated());
            return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث بيانات المستخدم')->withInput();
        }
    }
}
