# NotifyMe

[![Build Status](https://img.shields.io/travis/notifymehq/notifyme.svg?style=flat-square)](https://travis-ci.org/notifymehq/notifyme)

Common interface for notification services.

Supported Gateways:
* [Campfire](https://github.com/notifymehq/campfire)
* [GitHub](https://github.com/notifymehq/github) (soon)
* [Gitter](https://github.com/notifymehq/gitter)
* [Hipchat](https://github.com/notifymehq/hipchat)
* [Intercom](https://github.com/notifymehq/intercom) (soon)
* [Mail](https://github.com/notifymehq/mail) (soon)
* [Pagerduty](https://github.com/notifymehq/pagerduty)
* [Pushover](https://github.com/notifymehq/pushover)
* [SES](https://github.com/notifymehq/ses) (soon)
* [Slack](https://github.com/notifymehq/slack)
* [SNS](https://github.com/notifymehq/sns) (soon)
* [Twilio](https://github.com/notifymehq/twilio)
* [Twitter](https://github.com/notifymehq/twitter) (soon)
* [Webhook](https://github.com/notifymehq/webhook)
* [Yo](https://github.com/notifymehq/yo) (soon)

Supported Bridges:
* [Laravel 4](https://github.com/notifymehq/laravel4)
* [Laravel 5](https://github.com/notifymehq/laravel5)

## Usage

* Create a factory : ```$factory = new NotifyMeHQ\NotifyMe\NotifyMeFactory();```
* Make a notifier : ```$notifier = $factory->make([.]);```
* Notify : ```$response = $notifier->notify($message, [.]);```
* Check the response : ```$response->isSent();```

### Example

Here is an exemple of a notification with Slack

```PHP
<?php

// include autoloader if needed
// include __DIR__ . '/vendor/autoload.php';

// create a factory for notifications
$notifierFactory = new NotifyMeHQ\NotifyMe\NotifyMeFactory();

// create the new notification for slack
$slackNotifier = $notifierFactory->make([
  // specify that we will use slack
  'driver'  => 'slack',
  // add api token to get access to slack API
  'token'   => ''
]);

/* @var \NotifyMeHQ\NotifyMe\Response $response */
$response =  $slackNotifier->notify('test message', [
  // Who send this message, here is a bot called 'Super Bot'
  'from' => 'Super Bot',
  // Set the channel to received the message
  'to'   => '#sandbox'
]);

echo $response->isSent() ? 'Message sent' : 'Message going nowhere';
```

## Todo

- [ ] Add docs
- [ ] Add tests

## License

NotifyMe is licensed under [The MIT License (MIT)](LICENSE).
