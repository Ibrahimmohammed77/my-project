<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
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
     * Display a listing of schools.
     */
    public function index(Request $request): View|JsonResponse
    {
        $schools = School::with('user.status', 'type', 'level')
            ->filter($request->only('search', 'status_id'))
            ->paginate($request->get('per_page', 15));

        $statuses = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        $types = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_type');
        })->get();

        $levels = \App\Models\LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'school_level');
        })->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($schools, 'schools', 'تم استرجاع المدارس بنجاح');
        }

        return view('spa.schools.index', compact('schools', 'statuses', 'types', 'levels'));
    }

    /**
     * Store a newly created school.
     */
    public function store(\App\Http\Requests\Admin\StoreSchoolRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Map common fields
            $data['full_name'] = $data['name'];
            $data['status_id'] = $data['school_status_id'];
            
            // Generate username/password if not provided
            if (empty($data['username'])) {
                $data['username'] = 'school_' . str_replace(' ', '_', strtolower($data['name'])) . '_' . rand(100, 999);
            }
            if (empty($data['password'])) {
                $data['password'] = 'School@123';
            }

            // Set Role
            $role = \App\Models\Role::where('name', 'school_owner')->first();
            if ($role) {
                $data['role_id'] = $role->role_id;
                $data['user_type_id'] = $this->getUserTypeIdFromRole($role->role_id);
            }

            $user = $this->createUserUseCase->execute($data);
            
            return $this->successResponse(
                ['school' => $user->school->load('user.status', 'type', 'level')],
                'تم إنشاء المدرسة بنجاح'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating school: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء المدرسة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified school.
     */
    public function update(\App\Http\Requests\Admin\UpdateSchoolRequest $request, School $school): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $school->user;

            if (!$user) {
                return $this->errorResponse('مستخدم المدرسة غير موجود', 404);
            }

            // Map common fields
            $data['full_name'] = $data['name'];
            $data['status_id'] = $data['school_status_id'];

            $this->updateUserUseCase->execute($user, $data);
            
            return $this->successResponse(
                ['school' => $school->load('user.status', 'type', 'level')],
                'تم تحديث بيانات المدرسة بنجاح'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating school: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث المدرسة: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school): JsonResponse
    {
        try {
            $user = $school->user;
            if ($user) {
                $this->deleteUserUseCase->execute($user);
            } else {
                $school->delete();
            }

            return $this->successResponse([], 'تم حذف المدرسة بنجاح');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting school: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف المدرسة: ' . $e->getMessage(), 500);
        }
    }
}
