<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Identity\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * قائمة جميع الحسابات
     */
    public function index(Request $request)
    {
        $accounts = $this->accountService->getAll();
        
        // تحميل العلاقات
        $accounts->load(['status', 'roles']);

        return response()->json([
            'success' => true,
            'data' => ['accounts' => $accounts]
        ]);
    }

    /**
     * إنشاء حساب جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:accounts,username',
            'email' => 'nullable|email|max:100|unique:accounts,email',
            'phone' => 'required|string|max:20|unique:accounts,phone',
            'full_name' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'account_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'profile_image' => 'nullable|string|max:255',
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);

        $account = $this->accountService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => ['account' => $account]
        ], 201);
    }

    /**
     * عرض حساب محدد
     */
    public function show($id)
    {
        $account = $this->accountService->find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب غير موجود'
            ], 404);
        }

        $account->load(['status', 'roles.permissions']);

        return response()->json([
            'success' => true,
            'data' => ['account' => $account]
        ]);
    }

    /**
     * تحديث حساب
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'sometimes|string|max:50|unique:accounts,username,' . $id . ',account_id',
            'email' => 'sometimes|nullable|email|max:100|unique:accounts,email,' . $id . ',account_id',
            'phone' => 'sometimes|string|max:20|unique:accounts,phone,' . $id . ',account_id',
            'full_name' => 'sometimes|string|max:100',
            'password' => 'sometimes|nullable|string|min:6',
            'account_status_id' => 'sometimes|exists:lookup_values,lookup_value_id',
            'profile_image' => 'sometimes|nullable|string|max:255',
        ]);

        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
        }
        unset($validated['password']);

        $updated = $this->accountService->update($id, $validated);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تحديث الحساب'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحساب بنجاح'
        ]);
    }

    /**
     * حذف حساب
     */
    public function destroy($id)
    {
        $deleted = $this->accountService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف الحساب'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الحساب بنجاح'
        ]);
    }

    /**
     * الحصول على أدوار الحساب
     */
    public function getRoles($id)
    {
        $roles = $this->accountService->getRoles($id);

        return response()->json([
            'success' => true,
            'data' => ['roles' => $roles]
        ]);
    }

    /**
     * تعيين دور لحساب
     */
    public function assignRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,role_id'
        ]);

        $assigned = $this->accountService->assignRole($id, $validated['role_id']);

        if (!$assigned) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تعيين الدور'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الدور بنجاح'
        ]);
    }

    /**
     * إزالة دور من حساب
     */
    public function removeRole($accountId, $roleId)
    {
        $removed = $this->accountService->removeRole($accountId, $roleId);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إزالة الدور'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة الدور بنجاح'
        ]);
    }

    /**
     * الحصول على جميع صلاحيات الحساب
     */
    public function getPermissions($id)
    {
        $permissions = $this->accountService->getAllPermissions($id);

        return response()->json([
            'success' => true,
            'data' => ['permissions' => $permissions]
        ]);
    }
}
