<?php

namespace Trendyminds\Nerf;

use Statamic\Auth\File\User;
use Statamic\Policies\UserPolicy;
use Statamic\Providers\AddonServiceProvider;
use Trendyminds\Nerf\Auth\NerfUser;
use Trendyminds\Nerf\Policy\NerfUserPolicy;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic/nerf.php', 'statamic.nerf');

        $this->publishes([
            __DIR__.'/../config/statamic/nerf.php' => config_path('statamic/nerf.php'),
        ], 'nerf-config');

        // Exit out if the add-on is disabled
        if (! config('statamic.nerf.enabled')) {
            return;
        }

        // If we don't allow admin changes, we need to replace the User class
        // with our own and remove the permissions we don't want to allow
        $this->app->bind(User::class, NerfUser::class);

        // We need to also bind a new policy disallowing super admins from being edited
        $this->app->bind(UserPolicy::class, NerfUserPolicy::class);
    }
}
