<?php

namespace Laravel\Socialite\Two;

use Laravel\Socialite\Two\ProviderInterface;
use GuzzleHttp\Psr7;

class ExactOnlineProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'EXACTONLINE';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getInstanceUri().'oauth2/auth', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getInstanceUri().'oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getInstanceUri().'v1/current/Me?$select=UserName', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        // parse response (has to be extracted into function)
        Psr7\rewind_body($response);
        $json = json_decode($response->getBody()->getContents(), true);
        if (array_key_exists('d', $json)) {
            if (array_key_exists('results', $json['d'])) {
                if (count($json['d']['results']) == 1) {
                    return $json['d']['results'][0];
                }
                if (array_key_exists('__next', $json['d'])) {
                    $this->nextUrl = $json['d']['__next'];
                }
                else {
                    $this->nextUrl = null;
                }
                return $json['d']['results'];
            }
            return $json['d'];
        }

        return $json;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'name' => $user['UserName'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstanceUri()
    {
        return 'https://start.exactonline.nl/api/';
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['instance_uri'];
    }

}
