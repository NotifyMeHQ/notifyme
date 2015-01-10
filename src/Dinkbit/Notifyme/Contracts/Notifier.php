<?php

namespace Dinkbit\Notifyme\Contracts;

interface Notifier
{
    /**
     * Send notification using Gateway.
     * 
     * @param  string $message
     * @param  string[] $options
     * 
     * @return \Dinkbit\Notifyme\Response
     */
    public function notify($message, $options = []);
}
