<?php

namespace App\Providers;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Gate::define('super', function (User $user) {
            return in_array($user->role, ['super']);
        });
        Gate::define('admin', function (User $user) {
            return in_array($user->role, ['admin', 'super']);
        });

        Gate::define('moderator', function (User $user) {
            return in_array($user->role, ['super', 'admin', 'moderator']);
        });

        Gate::define('user', function (User $user) {
            return in_array($user->role, ['user']);
        });

        Gate::define('auditor', function (User $user) {
            return in_array($user->role, ['auditor']);
        });

        Gate::define('auditor-user', function (User $user) {
            return in_array($user->role, ['auditor', 'user']);
        });

        Gate::define('all', function (User $user) {
            return in_array($user->role, ['super', 'admin', 'moderator', 'user', 'auditor']);
        });

        Gate::define('super-auditor', function (User $user) {
            return $user->role == 'auditor' && $user->organisation->organisation_id === null;
        });
    }
}
