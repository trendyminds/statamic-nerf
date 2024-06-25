<?php

namespace Trendyminds\Nerf\Policy;

use Statamic\Facades\User;
use Statamic\Policies\UserPolicy;

class NerfUserPolicy extends UserPolicy
{
    public function edit($authed, $user)
    {
        $user = User::fromUser($user);
        $authed = User::fromUser($authed);

        // Disallow the authorized user from editing a super account
        if (! $authed->isSuper() && $user->isSuper()) {
            return false;
        }

        return parent::edit($authed, $user);
    }

    public function editPassword($authed, $user)
    {
        $user = User::fromUser($user);
        $authed = User::fromUser($authed);

        // Disallow the authorized user from resetting a super account
        if (! $authed->isSuper() && $user->isSuper()) {
            return false;
        }

        return parent::editPassword($authed, $user);
    }
}
