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
        //
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
    }
}

