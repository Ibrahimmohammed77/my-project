<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Identity\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * قائمة جميع الأدوار
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getAll();
        $roles->load('permissions');

        return response()->json([
            'success' => true,
            'data' => ['roles' => $roles]
        ]);
    }

    /**
     * إنشاء دور جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string',
            'is_system' => 'sometimes|boolean',
        ]);

        $role = $this->roleService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الدور بنجاح',
            'data' => ['role' => $role]
        ], 201);
    }

    /**
     * عرض دور محدد
     */
    public function show($id)
    {
        $role = $this->roleService->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'الدور غير موجود'
            ], 404);
        }

        $role->load('permissions');

        return response()->json([
            'success' => true,
            'data' => ['role' => $role]
        ]);
    }

    /**
     * تحديث دور
     */
    public function update(Request $request, $id)
    {
        $role = $this->roleService->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'الدور غير موجود'
            ], 404);
        }

        // منع تعديل الأدوار النظامية
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل الأدوار النظامية'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:50|unique:roles,name,' . $id . ',role_id',
            'description' => 'sometimes|nullable|string',
        ]);

        $updated = $this->roleService->update($id, $validated);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الدور'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدور بنجاح'
        ]);
    }

    /**
     * حذف دور
     */
    public function destroy($id)
    {
        $role = $this->roleService->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'الدور غير موجود'
            ], 404);
        }

        // منع حذف الأدوار النظامية
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الأدوار النظامية'
            ], 403);
        }

        $deleted = $this->roleService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف الدور'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدور بنجاح'
        ]);
    }

    /**
     * الحصول على صلاحيات الدور
     */
    public function getPermissions($id)
    {
        $permissions = $this->roleService->getPermissions($id);

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
        ]);
    }

    /**
     * تعيين صلاحية لدور
     */
    public function assignPermission(Request $request, $id)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,permission_id'
        ]);

        $assigned = $this->roleService->assignPermission($id, $validated['permission_id']);

        if (!$assigned) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تعيين الصلاحية'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الصلاحية بنجاح'
        ]);
    }

    /**
     * إزالة صلاحية من دور
     */
    public function removePermission($roleId, $permissionId)
    {
        $removed = $this->roleService->removePermission($roleId, $permissionId);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إزالة الصلاحية'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة الصلاحية بنجاح'
        ]);
    }

    /**
     * مزامنة صلاحيات الدور
     */
    public function syncPermissions(Request $request, $id)
    {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,permission_id'
        ]);

        $synced = $this->roleService->syncPermissions($id, $validated['permission_ids']);

        if (!$synced) {
            return response()->json([
                'success' => false,
                'message' => 'فشل مزامنة الصلاحيات'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم مزامنة الصلاحيات بنجاح'
        ]);
    }
}
