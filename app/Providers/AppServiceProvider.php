<?php

namespace App\Providers;

use App\Models\Correction;
use App\Policies\CorrectionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', fn ($user) => $user->isAdmin());
        Gate::policy(Correction::class, CorrectionPolicy::class);
    }
}
