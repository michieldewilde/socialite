<?php

namespace Laravel\Socialite\One;

use Laravel\Socialite\AbstractUser as BaseUser;

class User extends BaseUser
{
    /**
     * The user's access token.
     *
     * @var string
     */
    public $token;

    /**
     * The user's access token secret.
     *
     * @var string
     */
    public $tokenSecret;

    /**
     * The User Credentials.
     *
     * e.g. access_token, refresh_token, etc.
     *
     * @var array
     */
    public $accessTokenResponseBody;

    /**
     * Set the token on the user.
     *
     * @param  string  $token
     * @param  string  $tokenSecret
     * @return $this
     */
    public function setToken($token, $tokenSecret)
    {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        return $this;
    }

    /**
     * Set the credentials on the user.
     *
     * Might include things such as the token and refresh token
     *
     * @param array $accessTokenResponseBody
     *
     * @return $this
     */
    public function setAccessTokenResponseBody(array $accessTokenResponseBody)
    {
        $this->accessTokenResponseBody = $accessTokenResponseBody;
        return $this;
    }
}
