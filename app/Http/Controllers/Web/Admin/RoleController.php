<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\UseCases\Admin\Role\ManageRoleUseCase;
use Illuminate\Support\Facades\Gate;
use App\Traits\HasApiResponse;

class RoleController extends Controller
{
    use HasApiResponse;

    protected $manageRoleUseCase;

    public function __construct(ManageRoleUseCase $manageRoleUseCase)
    {
        $this->manageRoleUseCase = $manageRoleUseCase;
    }

    public function index()
    {
        Gate::authorize('manage_roles');

        $roles = $this->manageRoleUseCase->listRoles();

        if (request()->wantsJson()) {
            return $this->successResponse(['roles' => $roles], 'تم استرجاع الأدوار بنجاح');
        }

        return view('spa.roles.index', compact('roles'));
    }

    public function store(RoleRequest $request)
    {
        Gate::authorize('manage_roles');

        $this->manageRoleUseCase->createRole($request->validated());

        return redirect()->back()->with('success', 'تم إضافة الدور بنجاح');
    }

    public function update(RoleRequest $request, Role $role)
    {
        Gate::authorize('manage_roles');

        if ($role->is_system && $request->has('name') && $request->name !== $role->name) {
             return redirect()->back()->with('error', 'لا يمكن تغيير اسم دور النظام.');
        }

        $this->manageRoleUseCase->updateRole($role, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('manage_roles');

        try {
            $this->manageRoleUseCase->deleteRole($role);
            return redirect()->back()->with('success', 'تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
