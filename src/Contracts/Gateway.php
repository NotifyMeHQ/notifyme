<?php

namespace NotifyMeHQ\NotifyMe\Contracts;

interface Gateway
{
    /**
     * Get gateway display name.
     *
     * @return string
     */
    public function getDisplayName()
}
