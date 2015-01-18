<?php

namespace NotifyMeHQ\NotifyMe\Contracts;

interface Notifier
{
    /**
     * Send a notification.
     *
     * @param string   $message
     * @param string[] $options
     *
     * @return \NotifyMeHQ\NotifyMe\Response
     */
    public function notify($message, array $options = []);
}
