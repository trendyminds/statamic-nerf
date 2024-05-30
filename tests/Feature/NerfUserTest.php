<?php

use Facades\Statamic\Auth\CorePermissions;
use Illuminate\Support\Facades\App;
use Statamic\Facades\Permission;
use Statamic\Facades\Role;
use Statamic\Facades\User;
use Trendyminds\Nerf\Auth\NerfUser;

test('super admin permissions are uneffected when disabled', function () {
    $user = User::make()->makeSuper()->email(fake()->email)->save();

    try {
        expect($user->isSuper())->toBeTrue();
    } finally {
        $user->delete();
    }
});

test('super admin permissions are reduced when enabled', function () {
    // Bind the NerfUser class to the Statamic User class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Auth\File\User::class, NerfUser::class);

    // Create a user with super admin permissions
    $user = User::make()->makeSuper()->email(fake()->email)->save();

    try {
        expect($user->isSuper())->toBeFalse();
    } finally {
        $user->delete();
    }
});

test('elevated config permissions are allowed when disabled', function (array $permissions) {
    // Boot the default Statamic permissions
    CorePermissions::boot();

    // Create a role with all permissions
    $role = Role::make('All Permissions')
        ->addPermission(Permission::all()->keys()->toArray())
        ->save();

    // Create a user with the role
    $user = User::make()->email(fake()->email)->assignRole($role)->save();

    try {
        foreach ($permissions as $permission) {
            $hasPermission = $user->hasPermission($permission);

            if (! $hasPermission) {
                throw new Exception("Permission not assigned to user: {$permission}");
            }

            expect($hasPermission)->toBeTrue();
        }
    } finally {
        // Clean up
        $role->delete();
        $user->delete();
    }
})->with('dev_only_permissions');

test('elevated config permissions are disallowed when enabled', function (array $permissions) {
    // Bind the NerfUser class to the Statamic User class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Auth\File\User::class, NerfUser::class);

    // Boot the default Statamic permissions
    CorePermissions::boot();

    // Create a role with all permissions
    $role = Role::make('All Permissions')
        ->addPermission(Permission::all()->keys()->toArray())
        ->save();

    // Create a user with the role
    $user = User::make()->email(fake()->email)->assignRole($role)->save();

    try {
        foreach ($permissions as $permission) {
            $hasPermission = $user->hasPermission($permission);

            if ($hasPermission) {
                throw new Exception("Permission still assigned to user: {$permission}");
            }

            expect($hasPermission)->toBeFalse();
        }
    } finally {
        // Clean up
        $role->delete();
        $user->delete();
    }
})->with('dev_only_permissions');

test('disable configure-type permissions when enabled', function () {
    // Bind the NerfUser class to the Statamic User class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Auth\File\User::class, NerfUser::class);

    // Create a user with super admin permissions
    $user = User::make()->makeSuper()->email(fake()->email)->save();

    try {
        expect($user->hasPermission('configure foo'))->toBeFalse();
    } finally {
        $user->delete();
    }
});

test('allow exceptions to the configure-type rules', function () {
    // Bind the NerfUser class to the Statamic User class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Auth\File\User::class, NerfUser::class);

    // Create a user with super admin permissions
    $user = User::make()->makeSuper()->email(fake()->email)->save();

    // Ensure a standard config exists
    $this->app['config']->set('statamic.nerf.disallowed_permissions', []);

    try {
        expect($user->hasPermission('configure forms'))->toBeTrue();
        expect($user->hasPermission('configure form fields'))->toBeTrue();
    } finally {
        $user->delete();
    }
});
