<?php

namespace Laravel\Socialite\One\Twitter;

use Laravel\Socialite\One\User;
use Laravel\Socialite\One\AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'TWITTER';

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user['extra'])->map([
             'id' => $user['id'],
             'nickname' => $user['nickname'],
             'name' => $user['name'],
             'email' => $user['email'],
             'avatar' => $user['avatar'],
        ]);
    }
}

