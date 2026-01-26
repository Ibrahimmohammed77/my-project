<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $permissions = $this->permissionService->getAll();
            if ($request->query('grouped')) {
                $permissions = $this->permissionService->getGroupedByResource();
            }

            return response()->json([
                'success' => true,
                'data' => ['permissions' => $permissions]
            ]);
        }

        return view('spa.permissions.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
            'resource_type' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $existing = $this->permissionService->findByResourceAndAction(
            $validated['resource_type'],
            $validated['action']
        );

        if ($existing) {
            return response()->json(['message' => 'Permission already exists'], 422);
        }

        $permission = $this->permissionService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => ['permission' => $permission]
        ], 201);
    }

    public function show($id)
    {
        $permission = $this->permissionService->find($id);
        if (!$permission) return response()->json(['message' => 'Not found'], 404);

        return response()->json(['success' => true, 'data' => ['permission' => $permission]]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:permissions,name,' . $id . ',permission_id',
            'resource_type' => 'sometimes|string|max:50',
            'action' => 'sometimes|string|max:50',
            'description' => 'sometimes|nullable|string',
        ]);

        $updated = $this->permissionService->update($id, $validated);

        if (!$updated) return response()->json(['message' => 'Update failed'], 400);

        return response()->json(['success' => true, 'message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $deleted = $this->permissionService->delete($id);
        if (!$deleted) return response()->json(['message' => 'Delete failed'], 400);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
