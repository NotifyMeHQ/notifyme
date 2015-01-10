# Dinkbit Notifyme
[![Build Status](https://img.shields.io/travis/dinkbit/notifyme.svg?style=flat-square)](https://travis-ci.org/dinkbit/notifyme)


Provides a common interface for notification services.

Supported Gateways:
* Slack
* HipChat
* Twilio (soon)

### Add Configuration

First, you should configure the authentication providers you would like to use in your `config/services.php` file.

	'slack' => [
		'token' => 'your-token',
	],

### Examples

```php

	// Inject the interface

	use Dinkbit\Notifyme\Contracts\Factory as Notifyme;

	public function __construct(Notifyme $notifyme)
	{
	    $this->notifyme = $notifyme;
	}

```
Use

```php

	$notifyme->driver('slack')->notify('You did it!', ['channel' => '#everybody']);

	$notifyme->driver('hipchat')->notify('You did it!', ['channel' => 'everybody', 'notify' => true]);

```

### Todo

- [ ] Add tests
- [ ] Add docs
- [ ] Add more gateways