<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;

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

        Gate::define('is-admin', function($user) {
            return $user->is_admin == true;
        });

        Gate::define('can-sign', function($user) {
            return $user->can_sign == true || Gate::allows('is-admin');
        });

        Gate::define('owns-cert', function($user, $certificate) {
            return $user->id == $certificate->owner_id || Gate::allows('is-admin');
        });

        Gate::define('has-permission', function($user, $certificate) {
            return Permission::where('user_id', $user->id)->where('certificate_id', $certificate->id)->exists() || Gate::allows('owns-cert', $certificate);
        });
    }
}
