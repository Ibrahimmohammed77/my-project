<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'resource_type' => 'users', 'action' => 'manage', 'description' => 'إدارة المستخدمين'],
            ['name' => 'view_users', 'resource_type' => 'users', 'action' => 'view', 'description' => 'عرض المستخدمين'],
            
            // Role & Permission Management
            ['name' => 'manage_roles', 'resource_type' => 'roles', 'action' => 'manage', 'description' => 'إدارة الأدوار'],
            ['name' => 'manage_permissions', 'resource_type' => 'permissions', 'action' => 'manage', 'description' => 'إدارة الصلاحيات'],
            
            // Plan Management
            ['name' => 'manage_plans', 'resource_type' => 'plans', 'action' => 'manage', 'description' => 'إدارة الباقات'],
            ['name' => 'view_plans', 'resource_type' => 'plans', 'action' => 'view', 'description' => 'عرض الباقات'],
            
            // Lookup Management
            ['name' => 'manage_lookups', 'resource_type' => 'lookups', 'action' => 'manage', 'description' => 'إدارة الثوابت'],
            
            // Card Management
            ['name' => 'manage_cards', 'resource_type' => 'cards', 'action' => 'manage', 'description' => 'إدارة الكروت'],
            ['name' => 'view_cards', 'resource_type' => 'cards', 'action' => 'view', 'description' => 'عرض الكروت'],
            
            // Album Management
            ['name' => 'manage_albums', 'resource_type' => 'albums', 'action' => 'manage', 'description' => 'إدارة الألبومات'],
            ['name' => 'view_albums', 'resource_type' => 'albums', 'action' => 'view', 'description' => 'عرض الألبومات'],

            // Photo Management
            ['name' => 'manage_photos', 'resource_type' => 'photos', 'action' => 'manage', 'description' => 'إدارة الصور'],
            ['name' => 'view_photos', 'resource_type' => 'photos', 'action' => 'view', 'description' => 'عرض الصور'],
            
            // Studio Management
            ['name' => 'manage_studios', 'resource_type' => 'studios', 'action' => 'manage', 'description' => 'إدارة الاستوديوهات'],
            
            // School Management
            ['name' => 'manage_schools', 'resource_type' => 'schools', 'action' => 'manage', 'description' => 'إدارة المدارس'],
            
            // Customer Management
            ['name' => 'manage_customers', 'resource_type' => 'customers', 'action' => 'manage', 'description' => 'إدارة العملاء'],

            // Financial & Subscriptions
            ['name' => 'manage_subscriptions', 'resource_type' => 'subscriptions', 'action' => 'manage', 'description' => 'إدارة الاشتراكات'],
            ['name' => 'manage_invoices', 'resource_type' => 'invoices', 'action' => 'manage', 'description' => 'إدارة الفواتير'],
            ['name' => 'view_payments', 'resource_type' => 'payments', 'action' => 'view', 'description' => 'عرض المدفوعات'],
            ['name' => 'manage_orders', 'resource_type' => 'orders', 'action' => 'manage', 'description' => 'إدارة الطلبات'], // New
            ['name' => 'view_orders', 'resource_type' => 'orders', 'action' => 'view', 'description' => 'عرض الطلبات'], // New

            // Reports & Stats
            ['name' => 'view_reports', 'resource_type' => 'reports', 'action' => 'view', 'description' => 'عرض التقارير'],
            ['name' => 'view_stats', 'resource_type' => 'stats', 'action' => 'view', 'description' => 'عرض الإحصائيات'],

            // Settings & System
            ['name' => 'manage_settings', 'resource_type' => 'settings', 'action' => 'manage', 'description' => 'إدارة الإعدادات'],
            ['name' => 'view_logs', 'resource_type' => 'logs', 'action' => 'view', 'description' => 'عرض السجلات'],

            // Dashboard Access
            ['name' => 'access-admin-dashboard', 'resource_type' => 'dashboard_admin', 'action' => 'access', 'description' => 'دخول لوحة تحكم المدير'],
            ['name' => 'access-studio-dashboard', 'resource_type' => 'dashboard_studio', 'action' => 'access', 'description' => 'دخول لوحة تحكم الاستوديو'],
            ['name' => 'access-school-dashboard', 'resource_type' => 'dashboard_school', 'action' => 'access', 'description' => 'دخول لوحة تحكم المدرسة'],
            ['name' => 'access-customer-dashboard', 'resource_type' => 'dashboard_customer', 'action' => 'access', 'description' => 'دخول لوحة تحكم العميل'],
            ['name' => 'access-final-user-dashboard', 'resource_type' => 'dashboard_employee', 'action' => 'access', 'description' => 'دخول لوحة تحكم الموظف'],
            ['name' => 'access-editor-dashboard', 'resource_type' => 'dashboard_editor', 'action' => 'access', 'description' => 'دخول لوحة تحكم المحرر'],
            ['name' => 'access-guest-dashboard', 'resource_type' => 'dashboard_guest', 'action' => 'access', 'description' => 'دخول لوحة تحكم الزائر'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permData) {
            $permData['is_active'] = true;
            $permission = Permission::updateOrCreate(
                ['name' => $permData['name']],
                $permData
            );
            $permissionIds[$permData['name']] = $permission->permission_id;
        }

        // 2. Create Roles
        $roles = [
            'super_admin' => [
                'description' => 'مدير النظام الخارق',
                'permissions' => array_keys($permissionIds)
            ],
            'admin' => [
                'description' => 'مدير نظام',
                'permissions' => [
                    'manage_users', 'view_users', 'manage_plans', 'view_plans', 
                    'manage_lookups', 'manage_cards', 'view_cards', 'manage_studios', 
                    'manage_schools', 'view_albums', 'manage_settings', 'view_reports',
                    'view_stats', 'manage_orders', 'view_orders', 'manage_subscriptions', 'access-admin-dashboard', 'access-guest-dashboard'
                ]
            ],
            'studio_owner' => [
                'description' => 'صاحب استوديو',
                'permissions' => [
                    'manage_albums', 'view_albums', 'manage_photos', 'view_photos',
                    'manage_customers', 'manage_cards', 'view_cards', 'manage_orders', 'view_orders',
                    'access-studio-dashboard', 'access-guest-dashboard'
                ]
            ],
            'school_owner' => [
                'description' => 'صاحب مدرسة',
                'permissions' => [
                    'view_albums', 'view_photos', 'access-school-dashboard', 'access-guest-dashboard'
                ]
            ],
            'customer' => [
                'description' => 'عميل',
                'permissions' => [
                    'view_albums', 'view_photos', 'view_orders', 'access-customer-dashboard', 'access-guest-dashboard'
                ]
            ],
            'final_user' => [
                'description' => 'موظف',
                'permissions' => [
                    'view_users', 'view_cards', 'view_albums', 'view_photos', 'view_orders', 'access-final-user-dashboard', 'access-guest-dashboard'
                ]
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['description' => $roleData['description'], 'is_active' => true]
            );

            $attachIds = [];
            foreach ($roleData['permissions'] as $pName) {
                if (isset($permissionIds[$pName])) {
                    $attachIds[] = $permissionIds[$pName];
                }
            }
            
            if (!empty($attachIds)) {
                $role->permissions()->sync($attachIds);
            }
        }
    }
}
