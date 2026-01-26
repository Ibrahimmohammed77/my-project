<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $roles = $this->roleService->getAll();
            $roles->load('permissions');

            return response()->json([
                'success' => true,
                'data' => ['roles' => $roles]
            ]);
        }

        return view('spa.roles.index');
    }

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

    public function show($id)
    {
        $role = $this->roleService->find($id);

        if (!$role) {
             return response()->json(['message' => 'Not found'], 404);
        }

        if (request()->wantsJson()) {
             $role->load('permissions');
             return response()->json(['success' => true, 'data' => ['role' => $role]]);
        }
        
        return abort(404);
    }

    public function update(Request $request, $id)
    {
        $role = $this->roleService->find($id);
        if (!$role) return response()->json(['message' => 'Not found'], 404);
        if ($role->is_system) return response()->json(['message' => 'System role cannot be modified'], 403);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:50|unique:roles,name,' . $id . ',role_id',
            'description' => 'sometimes|nullable|string',
        ]);

        $this->roleService->update($id, $validated);

        return response()->json(['success' => true, 'message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $role = $this->roleService->find($id);
        if (!$role) return response()->json(['message' => 'Not found'], 404);
        if ($role->is_system) return response()->json(['message' => 'System role cannot be deleted'], 403);

        $this->roleService->delete($id);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
