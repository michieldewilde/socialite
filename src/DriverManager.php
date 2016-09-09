<?php

namespace Laravel\Socialite;

use Illuminate\Support\Manager;
use Illuminate\Support\Str;

class DriverManager extends Manager
{

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @param  \Laravel\Socialite\Config  $config
     * @return mixed
     */
    public function driver($driver = null, $config = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        // If the given driver has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a driver created by this name, we'll just return that instance.
        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver, $config);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @param  \Laravel\Socialite\Config  $config
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver, $config = null)
    {
        $method = 'create'.Str::studly($driver).'Driver';

        // We'll check to see if a creator method exists for the given driver. If not we
        // will check for a custom driver creator, which allows developers to create
        // drivers using their own customized driver creator Closure to create it.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (method_exists($this, $method)) {
            return $this->$method($config);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
