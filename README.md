# NotifyMe

[![Build Status](https://img.shields.io/travis/notifymehq/notifyme.svg?style=flat-square)](https://travis-ci.org/notifymehq/notifyme)
[![StyleCI](https://styleci.io/repos/29053236/shield)](https://styleci.io/repos/29053236)


Common interface for notification services.

Supported Gateways:
* [Campfire](https://github.com/notifymehq/campfire)
* [GitHub](https://github.com/notifymehq/github) (soon)
* [Gitter](https://github.com/notifymehq/gitter)
* [Hipchat](https://github.com/notifymehq/hipchat)
* [Intercom](https://github.com/notifymehq/intercom) (soon)
* [IRC](https://github.com/notifymehq/irc) (soon)
* [Mail](https://github.com/notifymehq/mail) (soon)
* [Pagerduty](https://github.com/notifymehq/pagerduty)
* [Pebble Timeline](https://github.com/notifymehq/pebbletimeline) (soon)
* [Pushover](https://github.com/notifymehq/pushover)
* [SES](https://github.com/notifymehq/ses) (soon)
* [Slack](https://github.com/notifymehq/slack)
* [SNS](https://github.com/notifymehq/sns) (soon)
* [Twilio](https://github.com/notifymehq/twilio)
* [Twitter](https://github.com/notifymehq/twitter) (soon)
* [Webhook](https://github.com/notifymehq/webhook)
* [XMPP](https://github.com/notifymehq/xmpp) (soon)
* [Yo](https://github.com/notifymehq/yo)

Supported Bridges:
* [Laravel 4](https://github.com/notifymehq/laravel4)
* [Laravel 5](https://github.com/notifymehq/laravel5)

## Usage

* Create a factory : `$factory = new NotifyMeHQ\NotifyMe\NotifyMeFactory();`
* Make a notifier : `$notifier = $factory->make($config);`
* Notify : `$response = $notifier->notify($to, $message, $options);`
* Check the response : `$response->isSent();`

### Example

Here is an example of a notification with Slack:

```php
<?php

// Create a factory for notifications
$notifierFactory = new NotifyMeHQ\NotifyMe\NotifyMeFactory();

// Create the new notification for slack
$slackNotifier = $notifierFactory->make([
  // Specify that we will use slack
  'driver' => 'slack',
  // Add api token to get access to slack API
  'token'  => '',
  // Who send this message, here is a bot called 'Super Bot'
  'from'   => 'Super Bot',
]);

/* @var \NotifyMeHQ\NotifyMe\Response $response */
$response =  $slackNotifier->notify('#sandbox', 'test message');

echo $response->isSent() ? 'Message sent' : 'Message going nowhere';
```

## Todo

- [ ] Add docs
- [ ] Add tests

## License

NotifyMe is licensed under [The MIT License (MIT)](LICENSE).
