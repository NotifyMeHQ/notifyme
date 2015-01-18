<?php

namespace NotifyMeHQ\NotifyMe;

interface GatewayInterface
{
    /**
     * Get gateway display name.
     *
     * @return string
     */
    public function getDisplayName()

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
