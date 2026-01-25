<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Identity\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * قائمة جميع الصلاحيات
     */
    public function index(Request $request)
    {
        $permissions = $this->permissionService->getAll();

        // إذا طلب المستخدم تجميع حسب resource_type
        if ($request->query('grouped')) {
            $permissions = $this->permissionService->getGroupedByResource();
        }

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
        ]);
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
            'resource_type' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        // التحقق من عدم تكرار resource_type + action
        $existing = $this->permissionService->findByResourceAndAction(
            $validated['resource_type'],
            $validated['action']
        );

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'الصلاحية موجودة مسبقاً لهذا المورد والإجراء'
            ], 422);
        }

        $permission = $this->permissionService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الصلاحية بنجاح',
            'data' => ['permission' => $permission]
        ], 201);
    }

    /**
     * عرض صلاحية محددة
     */
    public function show($id)
    {
        $permission = $this->permissionService->find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'الصلاحية غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['permission' => $permission]
        ]);
    }

    /**
     * تحديث صلاحية
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:permissions,name,' . $id . ',permission_id',
            'resource_type' => 'sometimes|string|max:50',
            'action' => 'sometimes|string|max:50',
            'description' => 'sometimes|nullable|string',
        ]);

        $updated = $this->permissionService->update($id, $validated);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الصلاحية'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الصلاحية بنجاح'
        ]);
    }

    /**
     * حذف صلاحية
     */
    public function destroy($id)
    {
        $deleted = $this->permissionService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف الصلاحية'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصلاحية بنجاح'
        ]);
    }
}
