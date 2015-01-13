<?php

namespace NotifyMeHQ\NotifyMe\Contracts;

interface Notifier
{
    /**
     * Send notification using Gateway.
     *
     * @param string   $message
     * @param string[] $options
     *
     * @return \NotifyMeHQ\NotifyMe\Response
     */
    public function notify($message, $options = []);
}
