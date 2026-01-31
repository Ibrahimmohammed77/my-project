<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{CreateUserRequest, UpdateUserRequest};
use App\Services\Admin\UserService;
use App\Models\{User, Role, LookupValue};
use App\Traits\{HasApiResponse, MapsRoleToType};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use HasApiResponse, MapsRoleToType;

    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->only(['search', 'status_id']);
        
        // Force filter to only show 'customer' role
        $customerRole = Role::where('name', 'customer')->first();
        if ($customerRole) {
            $filters['role_id'] = $customerRole->role_id;
        }

        $users = $this->userService->listUsers($filters, $request->get('per_page', 15));

        if ($request->wantsJson()) {
            return $this->paginatedResponse($users, 'users');
        }

        return view('spa.accounts.index', $this->getViewData($users, $filters));
    }

    /**
     * Search users for selection.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', $request->get('search'));
        
        $users = $this->userService->listUsers([
            'search' => $query,
            'is_active' => true
        ], 10);

        return $this->successResponse([
            'users' => $users->items()
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(CreateUserRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $data = $request->validated();

            if (isset($data['role_id'])) {
                $typeId = $this->getUserTypeIdFromRole($data['role_id']);
                if ($typeId) {
                    $data['user_type_id'] = $typeId;
                }
            }

            $user = $this->userService->createUser($data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['user' => $user->load(['roles', 'status', 'type'])],
                    'تم إنشاء المستخدم بنجاح',
                    201
                );
            }

            return redirect()->route('spa.accounts')->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء إنشاء المستخدم');
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View|JsonResponse
    {
        $data = [
            'user' => $user->load('roles'),
            'roles' => Role::all(),
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))->get()
        ];

        if (request()->wantsJson()) {
            return $this->successResponse($data);
        }

        return view('spa.accounts.edit', $data);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse|RedirectResponse
    {
        try {
            $data = $request->validated();

            if (isset($data['role_id'])) {
                $typeId = $this->getUserTypeIdFromRole($data['role_id']);
                if ($typeId) {
                    $data['user_type_id'] = $typeId;
                }
            }

            $updatedUser = $this->userService->updateUser($user, $data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['user' => $updatedUser->load(['roles', 'status', 'type'])],
                    'تم تحديث بيانات المستخدم بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء تحديث بيانات المستخدم');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse|RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);

            if (request()->wantsJson()) {
                return $this->successResponse(
                    null,
                    'تم حذف المستخدم بنجاح'
                );
            }

            return redirect()->route('spa.accounts')->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف المستخدم');
        }
    }

    /**
     * Get data for view.
     */
    private function getViewData(LengthAwarePaginator $users, array $filters): array
    {
        return [
            'users' => $users,
            'roles' => Role::where('name', '!=', 'super_admin')
                ->where('is_active', true)
                ->get(),
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))
                ->where('is_active', true)
                ->get(),
            'schoolTypes' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_TYPE'))
                ->where('is_active', true)
                ->get(),
            'schoolLevels' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_LEVEL'))
                ->where('is_active', true)
                ->get(),
            'filters' => $filters
        ];
    }

    /**
     * Handle errors consistently.
     */
    private function handleError(\Exception $e, Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return $this->errorResponse(
                $message,
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }

        return back()->with('error', $message)->withInput();
    }
}
