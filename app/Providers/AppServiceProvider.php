<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Identity Repositories
        $this->app->bind(
            \App\Domain\Identity\Repositories\Contracts\AccountRepositoryInterface::class,
            \App\Domain\Identity\Repositories\Eloquent\AccountRepository::class
        );
        
        $this->app->bind(
            \App\Domain\Identity\Repositories\Contracts\RoleRepositoryInterface::class,
            \App\Domain\Identity\Repositories\Eloquent\RoleRepository::class
        );
        
        $this->app->bind(
            \App\Domain\Identity\Repositories\Contracts\PermissionRepositoryInterface::class,
            \App\Domain\Identity\Repositories\Eloquent\PermissionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Domain\Access\Models\Card::observe(\App\Domain\Access\Observers\CardObserver::class);
        \App\Domain\Finance\Models\Invoice::observe(\App\Domain\Finance\Observers\InvoiceObserver::class);
        \App\Domain\Media\Models\Photo::observe(\App\Domain\Media\Observers\PhotoObserver::class);
        \App\Domain\Identity\Models\Account::observe(\App\Domain\Identity\Observers\AccountObserver::class);
        \App\Domain\Core\Models\Studio::observe(\App\Domain\Core\Observers\StudioObserver::class);
        \App\Domain\Core\Models\School::observe(\App\Domain\Core\Observers\SchoolObserver::class);
        \App\Domain\Core\Models\Subscriber::observe(\App\Domain\Core\Observers\SubscriberObserver::class);
        \App\Domain\Identity\Models\Permission::observe(\App\Domain\Identity\Observers\PermissionObserver::class);
        \App\Domain\Identity\Models\Role::observe(\App\Domain\Identity\Observers\RoleObserver::class);
    }
}

