<?php

dataset('dev_only_permissions', [
    fn () => [
        'configure fields',
        'configure addons',
        'access licensing utility',
        'view updates',
        'manage preferences',
        'configure collections',
        'resolve duplicate ids',
        'view graphql',
    ],
]);
