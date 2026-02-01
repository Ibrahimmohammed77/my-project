<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\LookupValue;
use App\Models\Role;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create the user
            $user = $this->model->create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'user_type_id' => $this->getCustomerTypeId(),
                'user_status_id' => $this->getDefaultStatusId(),
                'email_verified' => false,
                'phone_verified' => false,
                'is_active' => true,
                'verification_code' => rand(100000, 999999),
                'verification_expiry' => now()->addHours(24),
            ]);

            // Assign customer role
            $this->assignRole($user, 'customer');

            // Create customer profile
            if (isset($data['date_of_birth']) || isset($data['gender_id'])) {
                $user->customer()->create([
                    'first_name' => explode(' ', $data['name'])[0] ?? $data['name'],
                    'last_name' => explode(' ', $data['name'])[1] ?? '',
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender_id' => $data['gender_id'] ?? null,
                ]);
            }

            return $user->load(['customer', 'roles']);
        });
    }

    /**
     * Find user by ID.
     */
    public function find(int $id): ?User
    {
        return $this->model->with(['status', 'type', 'roles', 'customer'])
            ->find($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', strtolower($email))
            ->first();
    }

    /**
     * Find user by phone.
     */
    public function findByPhone(string $phone): ?User
    {
        return $this->model->where('phone', $phone)
            ->first();
    }

    /**
     * Find user by username.
     */
    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', strtolower($username))
            ->first();
    }

    /**
     * Find user by email, phone, or username.
     */
    public function findByLogin(string $login): ?User
    {
        $login = strtolower(trim($login));

        return $this->model->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('phone', $login)
                    ->orWhereRaw('LOWER(username) = ?', [$login]);
            })
            ->first();
    }

    /**
     * Update user.
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, string $role): void
    {
        $roleModel = Role::where('name', $role)->first();

        if ($roleModel) {
            $user->roles()->syncWithoutDetaching([$roleModel->role_id]);
        }
    }

    /**
     * Check if user has role.
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * List users with filters for admin.
     */
    public function listByAdmin(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->with(['status', 'type', 'roles']);

        // Search by keyword
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role ID
        if (!empty($filters['role_id'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('roles.role_id', $filters['role_id']);
            });
        }

        // Filter by role names (array)
        if (!empty($filters['roles']) && is_array($filters['roles'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->whereIn('roles.name', $filters['roles']);
            });
        }

        // Filter by status
        if (!empty($filters['status_id'])) {
            $query->where('user_status_id', $filters['status_id']);
        }

        // Filter by type
        if (!empty($filters['type_id'])) {
            $query->where('user_type_id', $filters['type_id']);
        }

        // Default sorting
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Store a new user by an admin.
     */
    public function storeByAdmin(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->model->create([
                'name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'user_type_id' => $data['user_type_id'] ?? $this->getCustomerTypeId(),
                'user_status_id' => $data['user_status_id'] ?? $this->getDefaultStatusId(),
                'email_verified' => true,
                'phone_verified' => true,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['role_id'])) {
                $user->roles()->sync([$data['role_id']]);
            }

            return $user->load(['roles', 'type']);
        });
    }

    /**
     * Update an existing user by an admin.
     */
    public function updateByAdmin(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? $user->phone,
                'user_type_id' => $data['user_type_id'] ?? $user->user_type_id,
                'user_status_id' => $data['user_status_id'] ?? $user->user_status_id,
                'is_active' => $data['is_active'] ?? $user->is_active,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (isset($data['role_id'])) {
                $user->roles()->sync([$data['role_id']]);
            }

            return $user->load(['roles', 'type']);
        });
    }

    /**
     * Get customer type ID.
     */
    public function getCustomerTypeId(): int
    {
        return LookupValue::where('code', 'CUSTOMER')
            ->first()
            ->lookup_value_id;
    }

    /**
     * Get default status ID.
     */
    public function getDefaultStatusId(): int
    {
        return LookupValue::where('code', 'ACTIVE')
            ->first()
            ->lookup_value_id;
    }
}
