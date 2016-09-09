<?php

namespace Laravel\Socialite;

use InvalidArgumentException;
use Laravel\Socialite\DriverManager as Manager;
use Laravel\Socialite\One\Server;
use League\OAuth1\Client\Server\Twitter as TwitterServer;

class SocialiteManager extends Manager implements Contracts\Factory
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     *
     * @return mixed
     */
    public function with($driver, $config)
    {
        return $this->driver($driver, $config);
    }

    /**
     * @param string           $providerClass
     * @param null|string      $oauth1Server
     *
     * @return \Laravel\Socialite\One\AbstractProvider|\Laravel\Socialite\Two\AbstractProvider
     */
    protected function buildProvider($providerClass, $oauth1Server)
    {
        if ($this->isOAuth1($oauth1Server)) {
            $this->classExists($oauth1Server);
            return $this->buildOAuth1Provider($providerClass, $oauth1Server);
        }
        return $this->buildOAuth2Provider($providerClass);
    }

    /**
     * Build an OAuth 1 provider instance.
     *
     * @param  string  $providerClass must extend Laravel\Socialite\One\AbstractProvider
     * @param  string  $oauth1Server must extend League\OAuth1\Client\Server\Server
     * @param  array   $config
     *
     * @return \Laravel\Socialite\One\AbstractProvider
     */
    public function buildOAuth1Provider($providerClass, $oauth1Server, $config)
    {
        // check if parameters are extending the right classes
        $this->classExtends($providerClass, \Laravel\Socialite\One\AbstractProvider::class);
        $this->classExtends($oauth1Server, \League\OAuth1\Client\Server\Server::class);

        // not getting config because setting it dynamicly after we have created it
        // creating new provider with empty config
        $provider = new $providerClass($this->app['request'], new $oauth1Server($config));

        return $provider;
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param   string  $providerClass must extend Laravel\Socialite\Two\AbstractProvider
     * @param   array   $config
     *
     * @return  \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildOAuth2Provider($providerClass, $config)
    {
        // check if parameters are extending the right classes
        $this->classExtends($providerClass, \Laravel\Socialite\Two\AbstractProvider::class);

        $provider = new $providerClass($this->app['request'], $config);

        return $provider;
    }

    /**
     *
     * OAuth 2 providers
     *
     */

    /**
     * Create an instance of the specified driver.
     *
     * @param   array   $config
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGithubDriver($config)
    {
        return $this->buildOAuth2Provider(
            'Laravel\Socialite\Two\GithubProvider',
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @param   array   $config
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createExactOnlineDriver($config)
    {
        return $this->buildOAuth2Provider(
            'Laravel\Socialite\Two\ExactOnlineProvider',
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createFacebookDriver()
    {
        return $this->buildOAuth2Provider(
            'Laravel\Socialite\Two\FacebookProvider',
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGoogleDriver()
    {
        return $this->buildOAuth2Provider(
            'Laravel\Socialite\Two\GoogleProvider',
            $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createLinkedinDriver()
    {
        return $this->buildOAuth2Provider(
            'Laravel\Socialite\Two\LinkedInProvider',
            $config
        );
    }

    /**
     *
     * OAuth 1 providers
     *
     */

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\One\AbstractProvider
     */
    protected function createTwitterDriver($config)
    {
        return $this->buildOAuth1Provider(
            'Laravel\Socialite\One\Twitter\Provider',
            new TwitterServer($this->formatConfig($config)),
            $this->formatConfig($config)
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     *
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $config['redirect'],
        ], $config);
    }

    /**
     * Check if a server is given, which indicates that OAuth1 is used.
     *
     * @param string $oauth1Server
     *
     * @return bool
     */
    private function isOAuth1($oauth1Server)
    {
        return !empty($oauth1Server);
    }

    /**
     * @param string $class
     * @param string $baseClass
     *
     * @throws InvalidArgumentException
     */
    private function classExtends($class, $baseClass)
    {
        if (false === is_subclass_of($class, $baseClass)) {
            $message = $class.' does not extend '.$baseClass;
            throw new InvalidArgumentException($message);
        }
    }

    private function classExists($providerClass)
    {
        if (!class_exists($providerClass)) {
            throw new InvalidArgumentException("$providerClass doesn't exist");
        }
    }
}
