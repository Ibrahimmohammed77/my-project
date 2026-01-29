<?php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard المسؤول.
     */
    public function accessAdminDashboard(User $user): bool
    {
        return $user->hasPermission('access_admin_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard الاستوديو.
     */
    public function accessStudioDashboard(User $user): bool
    {
        return $user->hasPermission('access_studio_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard المدرسة.
     */
    public function accessSchoolDashboard(User $user): bool
    {
        return $user->hasPermission('access_school_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard العميل.
     */
    public function accessCustomerDashboard(User $user): bool
    {
        return $user->hasPermission('access_customer_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard الموظف.
     */
    public function accessEmployeeDashboard(User $user): bool
    {
        return $user->hasPermission('access_employee_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard المحرر.
     */
    public function accessEditorDashboard(User $user): bool
    {
        return $user->hasPermission('access_editor_dashboard');
    }

    /**
     * التحقق مما إذا كان المستخدم يمكنه الوصول إلى Dashboard الزائر.
     */
    public function accessGuestDashboard(User $user): bool
    {
        // يمكن للجميع الوصول إلى Dashboard الزائر
        return true;
    }
}
