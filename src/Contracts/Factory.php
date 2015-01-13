<?php

namespace NotifyMeHQ\NotifyMe\Contracts;

interface Factory
{
    /**
     * Get a Gateway implementation.
     *
     * @param string|null $driver
     *
     * @return \NotifyMeHQ\NotifyMe\Contracts\Gateway
     */
    public function driver($driver = null);
}
