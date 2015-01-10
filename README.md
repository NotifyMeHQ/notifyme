# Notifyme
[![Build Status](https://img.shields.io/travis/dinkbit/notifyme.svg?style=flat-square)](https://travis-ci.org/dinkbit/notifyme)


Provides a common interface for notification services.

Supported Gateways:
* Slack
* HipChat
* Twilio

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
		]
	];

### Examples

```php

// Inject the interface

use Dinkbit\Notifyme\Contracts\Factory as Notifyme;

public function __construct(Notifyme $notifyme)
{
    $this->notifyme = $notifyme;
}

public function storePost()
{
    $post = Post::create(Input::all());

    $notifyme->driver('slack')->notify($post->title, ['to' => '#everybody']);
}

```

You can override the service configuration and set specific service options on the secondarray.

```php

$notifyme->driver('slack')->notify('You did it!', ['to' => '#everybody']);

$notifyme->driver('hipchat')->notify('You did it!', ['to' => 'everybody', 'notify' => true]);

$notifyme->driver('twilio')->notify('You did it!', ['to' => '+15005550001']);

```

### Todo

- [ ] Add tests
- [ ] Add docs
- [ ] Add more gateways