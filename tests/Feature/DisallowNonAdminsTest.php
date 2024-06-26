<?php

use Facades\Statamic\Auth\CorePermissions;
use Illuminate\Support\Facades\App;
use Statamic\Facades\Permission;
use Statamic\Facades\Role;
use Statamic\Facades\User;
use Trendyminds\Nerf\Policy\NerfUserPolicy;

use function Pest\Laravel\{actingAs};

test('A non super admin cannot edit a super admins account', function () {
    // Bind the NerfUserPolicy class to the Statamic UserPolicy class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Policies\UserPolicy::class, NerfUserPolicy::class);

    // Boot the default Statamic permissions
    CorePermissions::boot();

    // Create a super admin
    $super = User::make()->makeSuper()->email(fake()->email)->save();

    // Create a non-admin user with all permissions
    $role = Role::make('All Permissions')
        ->addPermission(Permission::all()->keys()->toArray())
        ->save();

    $user = User::make()->email(fake()->email)->assignRole($role)->save();

    $adminUrl = User::all()
        ->filter(fn ($user) => $user->isSuper())
        ->map(fn ($user) => $user->editUrl())
        ->first();

    try {
        actingAs($user)
            ->get($adminUrl)
            ->assertRedirectToRoute('statamic.cp.index')
            ->assertSessionHas(['error' => 'This action is unauthorized.']);
    } finally {
        $super->delete();
        $user->delete();
    }
});

test('A non super admin cannot edit a super admins password', function () {
    // Bind the NerfUserPolicy class to the Statamic UserPolicy class (what the Nerf add-on would do if enabled via config/env)
    App::bind(\Statamic\Policies\UserPolicy::class, NerfUserPolicy::class);

    // Boot the default Statamic permissions
    CorePermissions::boot();

    // Create a super admin
    $super = User::make()->makeSuper()->email(fake()->email)->save();

    // Create a non-admin user with all permissions
    $role = Role::make('All Permissions')
        ->addPermission(Permission::all()->keys()->toArray())
        ->save();

    $user = User::make()->email(fake()->email)->assignRole($role)->save();

    $adminUrl = User::all()
        ->filter(fn ($user) => $user->isSuper())
        ->map(fn ($user) => "/cp/users/{$user->id}")
        ->first();

    try {
        actingAs($user)
            ->patch($adminUrl, [
                'current_password' => 'fakepassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirectToRoute('statamic.cp.index')
            ->assertSessionHas(['error' => 'This action is unauthorized.']);
    } finally {
        $super->delete();
        $user->delete();
    }
});
