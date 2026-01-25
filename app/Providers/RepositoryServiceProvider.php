<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $domains = [
            'Identity' => ['Account', 'Role'],
            'Core' => ['Studio', 'School', 'Subscriber', 'Customer', 'Office'],
            'Finance' => ['Plan', 'Subscription', 'Invoice', 'Payment', 'Commission'],
            'Media' => ['Album', 'Photo', 'StorageAccount'],
            'Access' => ['Card', 'CardGroup'],
            'Communications' => ['Notification'],
            'Shared' => ['LookupValue']
        ];

        foreach ($domains as $domain => $models) {
            foreach ($models as $model) {
                $interface = "App\\Domain\\$domain\\Repositories\\Contracts\\{$model}RepositoryInterface";
                $implementation = "App\\Domain\\$domain\\Repositories\\Eloquent\\{$model}Repository";

                $this->app->bind($interface, $implementation);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
