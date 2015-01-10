<?php

namespace Dinkbit\Notifyme\Contracts;

interface Factory
{
    /**
     * Get a Gateway implementation.
     *
     * @param string $driver
     *
     * @return \Dinkbit\Notifyme\Contracts\Gateway
     */
    public function driver($driver = null);
}
