<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Looks at the STATAMIC_ALLOW_ADMIN_CHANGES environment variable to
    | determine if the add-on should be enabled or not. When the env
    | variable is set to false, the add-on will be activated.
    |
    */
    'enabled' => ! env('STATAMIC_ALLOW_ADMIN_CHANGES', true),

    /*
    |--------------------------------------------------------------------------
    | Disallowed Permissions
    |--------------------------------------------------------------------------
    |
    | This is a list of any and all permissions you wish to disable on
    | top of the permissions already accounted for within Nerf.
    | You can add any permission you wish to disable here.
    |
    */
    'disallowed_permissions' => [
        // 'access cp',
    ],

];
