<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\User;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Card;
use App\Policies\AlbumPolicy;
use App\Policies\PhotoPolicy;
use App\Policies\CardPolicy;
use App\Policies\DashboardPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Album::class => AlbumPolicy::class,
        Photo::class => PhotoPolicy::class,
        Card::class => CardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all permissions to super_admin
        Gate::before(function (User $user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Register all database permissions as Gates
        $this->registerDynamicPermissions();

        // Additional Gates
        $this->defineGates();
    }

    /**
     * Register all database permissions as dynamic Gates.
     */
    protected function registerDynamicPermissions(): void
    {
        try {
            if (!Schema::hasTable('permissions')) {
                return;
            }

            $permissions = Cache::remember('active_permissions_list', 3600, function () {
                return Permission::active()->get(['name', 'description']);
            });

            foreach ($permissions as $permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error registering dynamic permissions: ' . $e->getMessage());
        }
    }

    /**
     * Define authorization gates.
     */
    protected function defineGates(): void
    {
        // التحقق مما إذا كان المستخدم مدير
        Gate::define('is-admin', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('super_admin');
        });

        // التحقق مما إذا كان المستخدم صاحب استوديو
        Gate::define('is-studio-owner', function (User $user) {
            return $user->hasRole('studio_owner') || $user->studio()->exists();
        });

        // التحقق مما إذا كان المستخدم صاحب مدرسة
        Gate::define('is-school-owner', function (User $user) {
            return $user->hasRole('school_owner') || $user->school()->exists();
        });

        // التحقق مما إذا كان المستخدم عميل
        Gate::define('is-customer', function (User $user) {
            return $user->hasRole('customer') || $user->customer()->exists();
        });

        // التحقق مما إذا كان المستخدم لديه اشتراك نشط
        Gate::define('has-active-subscription', function (User $user) {
            return $user->activeSubscription()->exists();
        });

        // التحقق مما إذا كان المستخدم هو مالك المورد
        Gate::define('owns-resource', function (User $user, $resource) {
            if (!$resource) {
                return false;
            }

            // تحقق من العلاقة المباشرة
            if (isset($resource->user_id) && $resource->user_id === $user->id) {
                return true;
            }

            // تحقق من العلاقات المتعددة الأشكال
            if (isset($resource->owner_type) && isset($resource->owner_id)) {
                return $resource->owner_type === User::class && $resource->owner_id === $user->id;
            }

            // تحقق من العلاقة عبر وسيط
            if (method_exists($resource, 'user') && $resource->user) {
                return $resource->user->id === $user->id;
            }

            return false;
        });

        // التحقق مما إذا كان المستخدم يمكنه إدارة المورد
        Gate::define('manage-resource', function (User $user, $resource) {
            // المدير يمكنه إدارة كل شيء
            if ($user->hasRole('admin')) {
                return true;
            }

            // المالك يمكنه إدارة موارده
            if (Gate::allows('owns-resource', $resource)) {
                return true;
            }

            // صاحب الاستوديو يمكنه إدارة موارد استوديوه
            if ($user->hasRole('studio_owner') && $user->studio) {
                // إضافة منطق التحقق من موارد الاستوديو
                return false;
            }

            return false;
        });

        // التحقق مما إذا كان المستخدم يمكنه مشاهدة المورد
        Gate::define('view-resource', function (User $user, $resource) {
            // يمكن للمالك رؤية موارده
            if (Gate::allows('owns-resource', $resource)) {
                return true;
            }

            // يمكن للبطاقة الصالحة الوصول إلى الألبوم
            if ($resource instanceof Album) {
                return $resource->is_visible && $resource->cards()
                    ->whereHas('holder', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    })
                    ->exists();
            }

            return false;
        });
    }
}
