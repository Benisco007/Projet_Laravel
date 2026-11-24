<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memoire;
use App\Policies\MemoirePolicy;
use Illuminate\Support\Facades\Gate;

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
        Gate::policy(Memoire::class, MemoirePolicy::class);
    }
}
