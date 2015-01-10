# Notifyme
[![Build Status](https://img.shields.io/travis/dinkbit/notifyme.svg?style=flat-square)](https://travis-ci.org/dinkbit/notifyme)

Common interface for notification services.

Supported Gateways:
* Slack
* HipChat
* Twilio
* Campfire
* Gitter (soon)
* PagerDuty (soon)
* Webhook (soon)

## Installation

### Laravel 4.2 and Below

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `dinkbit/notifyme`.

	"require": {
		"dinkbit/notifyme": "dev-master"
	}

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Dinkbit\Notifyme\NotifymeServiceProvider'

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

	];

### Examples

```php

// Inject the interface

use Dinkbit\Notifyme\Contracts\Factory as Notifyme;

protected $notifyme;

public function __construct(Notifyme $notifyme)
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

```php

// Interface
$notifyme->driver($diver)->notify($message, array $params);

$notifyme->driver('slack')->notify('You did it!', ['to' => '#everybody']);

$notifyme->driver('hipchat')->notify('You did it!', ['to' => 'everybody', 'notify' => true]);

$notifyme->driver('twilio')->notify('You did it!', ['to' => '+15005550001']);

```

### Todo

- [ ] Add tests
- [ ] Add docs
- [ ] Add more gateways