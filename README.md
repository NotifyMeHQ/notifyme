# NotifyMe
[![Build Status](https://img.shields.io/travis/notifymehq/notifyme.svg?style=flat-square)](https://travis-ci.org/notifymehq/notifyme)

Common interface for notification services.

Supported Gateways:
* Slack
* HipChat
* Twilio
* Campfire
* Gitter
* PagerDuty
* Webhook
* Pushover
* Intercom (soon)
* Amazon SES (soon)
* Amazon SNS (soon)
* Twitter (soon)
* Mail (soon)
* Yo (soon)

## Installation

### Laravel 4.1, 4.2, 5.0

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `notifymehq/notifyme`.

	"require": {
		"notifymehq/notifyme": "dev-master"
	}

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'NotifyMeHQ\NotifyMe\NotifyMeServiceProvider'

### Add Configuration

First, you should configure the authentication providers you would like to use in your `config/services.php` file.

	return [
		'slack' => [
			'from' 	=> 'notifyme',
			'token' => '',
		],
		'hipchat' => [
			'from' 	=> 'notifyme',
			'token' => '',
		],
		'twilio' => [
			'from'	 => '', // Phone
			'client' => '', // Sid
			'token'  => '',
		],
		'campfire' => [
			'from' 	=> 'notifyme', // Domain account
			'token' => '',
		],
		'gitter' => [
			'token' => '',
		],
		'pagerduty' => [
			'token' => '',
		],
		'pushover' => [
			'token' => '',
		],
	];

### Examples

```php
// Inject the interface

use NotifyMeHQ\NotifyMe\Contracts\Factory as NotifyMe;

protected $notifyme;

public function __construct(NotifyMe $notifyme)
{
    $this->notifyme = $notifyme;
}

public function storePost()
{
    $post = Post::create(Input::all());

    $response = $notifyme->driver('slack')->notify($post->title, ['to' => '#everybody']);

    if (! $response->isSent()) {
    	return ':(';
    }

    return 'Hurray!';
}

```

You can override the service configuration and set specific service options on the second array.

**Interface example**

```php
$notifyme->driver($diver)->notify($message, $params);

```

**Gateway example**

```php
$notifyme->driver('slack')->notify('You did it!', ['to' => '#everybody']);

$notifyme->driver('hipchat')->notify('You did it!', ['to' => 'everybody', 'notify' => true]);

$notifyme->driver('twilio')->notify('You did it!', ['to' => '+15005550001']);

$notifyme->driver('campfire')->notify('You did it!', ['to' => '1234']);

$notifyme->driver('gitter')->notify('You did it!', ['to' => ':roomId']);

$notifyme->driver('pagerduty')->notify('This is working awesome!', ['to' => ':incident_key']);

$notifyme->driver('webhook')->notify(['message' => 'This is working awesome!'], ['to' => 'http://example.com']);

$notifyme->driver('pushover')->notify(['message' => 'This is working awesome!'], ['to' => ':pushover_user']);

```

### Todo

- [ ] Add tests
- [ ] Add docs

## License

NotifyMe is licensed under [The MIT License (MIT)](LICENSE).
