<?php

namespace NotifyMeHQ\NotifyMe;

use InvalidArgumentException;

class NotifyMeFactory implements FactoryInterface
{
    /**
     * Create a new gateway instance.
     *
     * @param string[] $config
     *
     * @return \NotifyMeHQ\NotifyMe\GatewayInterface
     */
    public function make(array $config)
    {
        return $this->createFactory($config)->make($config);
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \NotifyMeHQ\NotifyMe\FactoryInterface
     */
    public function createFactory(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException("A driver must be specified.");
        }

        $class = "NotifyMeHQ\NotifyMe\{$config['driver']}\{$config['driver']}Factory";

        if (class_exists($class)) {
        	return new $class();
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
}
