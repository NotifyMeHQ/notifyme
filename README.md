# NotifyMe

[![StyleCI](https://styleci.io/repos/29053236/shield)](https://styleci.io/repos/29053236)
[![Build Status](https://img.shields.io/travis/notifymehq/notifyme.svg?style=flat-square)](https://travis-ci.org/notifymehq/notifyme)


Common interface for notification services.


## Installation

Either [PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.6+ are required.

To get the latest version of NotifyMe, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require notifymehq/notifyme
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "notifymehq/notifyme": "^1.0"
    }
}
```

If you want, you can require only a single adapter. This works rather like the component system of laravel or symfony. We currently have the following adapters:

* Ballou (`notifymehq/ballou`)
* Campfire (`notifymehq/campfire`)
* Gitter (`notifymehq/gitter`)
* Hipchat (`notifymehq/hipchat`)
* Pagerduty (`notifymehq/pagerduty`)
* Pushover (`notifymehq/pushover`)
* Slack (`notifymehq/slack`)
* Twilio (`notifymehq/twilio`)
* Webhook (`notifymehq/webhook`)
* Yo (`notifymehq/yo`)

Also, note, that our other components are:

* Contracts (`notifymehq/contracts`)
* Factory (`notifymehq/factory`)
* Http (`notifymehq/http`)
* Manager (`notifymehq/manager`)
* Support (`notifymehq/support`)

Finally, we have a totally seperate Laravel bridge available for use by installing `notifyme/laravel`, then adding our service provider: `NotifyMeHQ\Laravel\NotifyMeServiceProvider`.


## Usage

* Create a factory : `$factory = new NotifyMeHQ\Factory\NotifyMeFactory();`
* Make a notifier : `$notifier = $factory->make($config);`
* Notify : `$response = $notifier->notify($to, $message);`
* Check the response : `$response->isSent();`


## Example

Here is an example of a notification with Slack:

```php
<?php

// Create a factory for notifications
$notifierFactory = new NotifyMeHQ\Factory\NotifyMeFactory();

// Create the new notification for slack
$slackNotifier = $notifierFactory->make([
  // Specify that we will use slack
  'driver' => 'slack',
  // Add api token to get access to slack API
  'token'  => '',
  // Who send this message, here is a bot called 'Super Bot'
  'from'   => 'Super Bot',
]);

/* @var \NotifyMeHQ\Contracts\ResponseInterface $response */
$response =  $slackNotifier->notify('#sandbox', 'test message');

echo $response->isSent() ? 'Message sent' : 'Message going nowhere';
```


## License

NotifyMe is licensed under [The MIT License (MIT)](LICENSE).
