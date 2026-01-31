<?php

namespace App\Traits;

use App\Models\LookupValue;
use App\Models\Role;

trait MapsRoleToType
{
    /**
     * Determine the User Type ID based on the Role ID.
     */
    public function getUserTypeIdFromRole(int $roleId): ?int
    {
        $role = Role::find($roleId);
        if (!$role) {
            return null;
        }

        // Mapping strategy for different role patterns
        $typeCode = $this->mapRoleToTypeCode($role->name);

        if (!$typeCode) {
            return null;
        }

        return LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'USER_TYPE');
        })->where('code', $typeCode)->value('lookup_value_id');
    }

    /**
     * Map role name to user type code.
     */
    protected function mapRoleToTypeCode(string $roleName): ?string
    {
        $mappings = [
            // Direct mappings (role_name => type_code)
            'studio_owner' => 'STUDIO_OWNER',
            'school_owner' => 'SCHOOL_OWNER',
            'customer' => 'CUSTOMER',
            'admin' => 'ADMIN',
            'super_admin' => 'SUPER_ADMIN',

            // Special cases
            'final_user' => 'EMPLOYEE',
            'employee' => 'EMPLOYEE',
            'user' => 'CUSTOMER',
        ];

        // Try exact match first
        if (isset($mappings[$roleName])) {
            return $mappings[$roleName];
        }

        // Try case-insensitive match
        $lowercaseRole = strtolower($roleName);
        foreach ($mappings as $key => $value) {
            if (strtolower($key) === $lowercaseRole) {
                return $value;
            }
        }

        // Try converting role name to uppercase with underscores
        $converted = strtoupper(str_replace('-', '_', $roleName));
        if ($this->typeCodeExists($converted)) {
            return $converted;
        }

        return null;
    }

    /**
     * Check if type code exists in lookup values.
     */
    protected function typeCodeExists(string $typeCode): bool
    {
        return LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'USER_TYPE');
        })->where('code', $typeCode)->exists();
    }

    /**
     * Get role ID from user type ID.
     */
    protected function getRoleIdFromUserType(int $userTypeId): ?int
    {
        $type = LookupValue::find($userTypeId);
        if (!$type || !$type->master || $type->master->code !== 'USER_TYPE') {
            return null;
        }

        $roleMappings = [
            'STUDIO_OWNER' => 'studio_owner',
            'SCHOOL_OWNER' => 'school_owner',
            'CUSTOMER' => 'customer',
            'ADMIN' => 'admin',
            'SUPER_ADMIN' => 'super_admin',
            'EMPLOYEE' => 'employee',
        ];

        $roleName = $roleMappings[$type->code] ?? strtolower(str_replace('_', ' ', $type->code));

        return Role::where('name', $roleName)->value('role_id');
    }
}
