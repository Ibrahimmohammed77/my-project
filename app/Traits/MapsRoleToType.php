<?php

namespace App\Traits;

use App\Models\LookupValue;
use App\Models\Role;

trait MapsRoleToType
{
    /**
     * Determine the User Type ID based on the Role ID.
     */
    protected function getUserTypeIdFromRole(int $roleId): ?int
    {
        $role = Role::find($roleId);
        if (!$role) {
            return null;
        }

        // 1. Try direct mapping with strtoupper (studio_owner -> STUDIO_OWNER)
        $potentialCode = strtoupper($role->name);
        
        $type = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'USER_TYPE');
        })->where('code', $potentialCode)->first();

        if ($type) {
            return $type->lookup_value_id;
        }

        // 2. Handle specific exceptions where role name doesn't match lookup code
        $mappedCode = match ($role->name) {
            'final_user' => 'EMPLOYEE',
            'admin', 'super_admin' => 'SUPER_ADMIN',
            'customer' => 'CUSTOMER',
            default => null,
        };

        if ($mappedCode) {
            return LookupValue::whereHas('master', function ($q) {
                $q->where('code', 'USER_TYPE');
            })->where('code', $mappedCode)->value('lookup_value_id');
        }

        return null;
    }
}
