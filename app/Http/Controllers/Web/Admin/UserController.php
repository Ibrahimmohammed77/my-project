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
    use \App\Traits\MapsRoleToType;

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
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role_id', 'status_id', 'type_id']);
        $users = $this->listUsersUseCase->execute($filters, $request->get('per_page', 15));

        $roles = Role::where('name', '!=', 'super_admin')
            ->where('is_active', true)
            ->get();
            
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'USER_STATUS');
        })->where('is_active', true)->get();

        $schoolTypes = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'SCHOOL_TYPE');
        })->where('is_active', true)->get();

        $schoolLevels = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'SCHOOL_LEVEL');
        })->where('is_active', true)->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($users, 'accounts');
        }

        return view('spa.accounts.index', compact('users', 'roles', 'statuses', 'filters', 'schoolTypes', 'schoolLevels'));
    }

    /**
     * Store a newly created user.
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Determine User Type from Role
            if (isset($data['role_id'])) {
                $typeId = $this->getUserTypeIdFromRole($data['role_id']);
                if ($typeId) {
                    $data['user_type_id'] = $typeId;
                }
            }

            $user = $this->createUserUseCase->execute($data);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء المستخدم بنجاح',
                    'data' => ['user' => $user->load(['roles', 'status', 'type'])]
                ], 201);
            }
            
            return redirect()->route('spa.accounts')->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إنشاء المستخدم',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء إنشاء المستخدم')->withInput();
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
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
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();

            // Determine User Type from Role
            if (isset($data['role_id'])) {
                $typeId = $this->getUserTypeIdFromRole($data['role_id']);
                if ($typeId) {
                    $data['user_type_id'] = $typeId;
                }
            }

            $updatedUser = $this->updateUserUseCase->execute($user, $data);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث بيانات المستخدم بنجاح',
                    'data' => ['user' => $updatedUser->load(['roles', 'status', 'type'])]
                ]);
            }
            
            return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث بيانات المستخدم',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء تحديث بيانات المستخدم')->withInput();
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف المستخدم بنجاح'
                ]);
            }
            
            return redirect()->route('spa.accounts')->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف المستخدم',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء حذف المستخدم');
        }
    }
}
