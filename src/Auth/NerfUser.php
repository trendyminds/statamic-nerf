<?php

namespace Trendyminds\Nerf\Auth;

use Statamic\Auth\File\User;

class NerfUser extends User
{
    /**
     * Disallow users from having super admin permissions
     */
    public function isSuper(): bool
    {
        return false;
    }

    /**
     * Forcibly disable permissions as they're requested
     *
     * @param  string  $permission
     */
    public function hasPermission($permission): bool
    {
        // Set up exceptions for permissions that start with the word "configure"
        $configureException = in_array($permission, ['configure forms', 'configure form fields']);

        // Exit if the permission starts with the word "configure", but allow some exceptions
        if (preg_match('/^configure\W/', $permission) && ! $configureException) {
            return false;
        }

        // Exit if the permissions is an create, edit, delete request for collections, fields, etc
        if (preg_match('/^(create|edit|delete)$/', $permission)) {
            return false;
        }

        // Disable general control panel permissions
        if (in_array($permission, ['access licensing utility', 'view updates', 'manage preferences'])) {
            return false;
        }

        // Disallow miscellaneous permissions
        if (in_array($permission, ['view graphql', 'resolve duplicate ids'])) {
            return false;
        }

        // Allow customization of additional permissions
        if (in_array($permission, config('statamic.nerf.disallowed_permissions'))) {
            return false;
        }

        // Permit super admins access after initial checks
        if ($this->get('super')) {
            return true;
        }

        // Allow the parent function to determine the remaining permissions
        return parent::hasPermission($permission);
    }
}
