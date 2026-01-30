<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    use \App\Traits\HasApiResponse;
    use \App\Traits\MapsRoleToType;

    protected $createUserUseCase;
    protected $updateUserUseCase;
    protected $deleteUserUseCase;

    public function __construct(
        \App\UseCases\Admin\CreateUserUseCase $createUserUseCase,
        \App\UseCases\Admin\UpdateUserUseCase $updateUserUseCase,
        \App\UseCases\Admin\DeleteUserUseCase $deleteUserUseCase
    ) {
        $this->createUserUseCase = $createUserUseCase;
        $this->updateUserUseCase = $updateUserUseCase;
        $this->deleteUserUseCase = $deleteUserUseCase;
    }

    /**
     * Display a listing of studios.
     */
    public function index(Request $request): View|JsonResponse
    {
        $studios = Studio::with('user.status')
            ->filter($request->only('search', 'status_id'))
            ->paginate($request->get('per_page', 15));

        $statuses = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($studios, 'studios');
        }

        return view('spa.studios.index', compact('studios', 'statuses'));
    }

    /**
     * Store a newly created studio.
     */
    public function store(\App\Http\Requests\Admin\StoreStudioRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Map common fields to user model fields if needed
            $data['full_name'] = $data['name'];
            $data['status_id'] = $data['studio_status_id'];
            
            // Generate username/password if not provided
            if (empty($data['username'])) {
                $data['username'] = 'studio_' . str_replace(' ', '_', strtolower($data['name'])) . '_' . rand(100, 999);
            }
            if (empty($data['password'])) {
                $data['password'] = 'Studio@123'; // Default password
            }

            // Set Role
            $role = \App\Models\Role::where('name', 'studio_owner')->first();
            if ($role) {
                $data['role_id'] = $role->role_id;
                $data['user_type_id'] = $this->getUserTypeIdFromRole($role->role_id);
            }

            $user = $this->createUserUseCase->execute($data);
            
            return $this->successResponse(
                ['studio' => $user->studio->load('user.status')],
                'تم إنشاء الاستوديو بنجاح'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating studio: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء الاستوديو: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified studio.
     */
    public function update(\App\Http\Requests\Admin\UpdateStudioRequest $request, Studio $studio): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $studio->user;

            if (!$user) {
                return $this->errorResponse('مستخدم الاستوديو غير موجود', 404);
            }

            // Map common fields
            $data['full_name'] = $data['name'];
            $data['status_id'] = $data['studio_status_id'];

            $this->updateUserUseCase->execute($user, $data);
            
            return $this->successResponse(
                ['studio' => $studio->load('user.status')],
                'تم تحديث بيانات الاستوديو بنجاح'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating studio: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الاستوديو: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified studio.
     */
    public function destroy(Studio $studio): JsonResponse
    {
        try {
            $user = $studio->user;
            if ($user) {
                $this->deleteUserUseCase->execute($user);
            } else {
                $studio->delete();
            }

            return $this->successResponse([], 'تم حذف الاستوديو بنجاح');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting studio: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الاستوديو: ' . $e->getMessage(), 500);
        }
    }
}
