<?php

namespace App\UseCases\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Album;
use App\Models\StorageAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteUserUseCase
{
    /**
     * Execute the use case.
     */
    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            try {
                $userId = $user->id;

                // Log activity before deletion (while user still exists for foreign keys if any)
                ActivityLog::logCurrent(
                    'user_deleted',
                    User::class,
                    $userId,
                    [
                        'username' => $user->username,
                        'email' => $user->email,
                    ]
                );

                // Clean up polymorphic relations manually
                // StorageAccount
                StorageAccount::where('owner_type', User::class)
                    ->where('owner_id', $userId)
                    ->delete();

                // Albums (Users can own albums)
                Album::where('owner_type', User::class)
                    ->where('owner_id', $userId)
                    ->delete();

                // Detach Roles
                $user->roles()->detach();

                // Finally delete the user
                // Related profiles with DB-level cascade (Studio, School, Customer) will be deleted automatically
                $user->delete();

            } catch (\Exception $e) {
                Log::error('Error in DeleteUserUseCase: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}
