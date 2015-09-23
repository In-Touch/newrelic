[![Build Status](https://travis-ci.org/In-Touch/newrelic.svg?branch=master)](https://travis-ci.org/In-Touch/newrelic)

# NewRelic PHP Agent API Wrapper

This is simply a pass-through wrapper to the [NewRelic PHP Agent API](https://newrelic.com/docs/php/the-php-api) in a namespaced class available via composer.  No magic here.

### Installation

Run

```
$ composer require intouch/newrelic
```

### Basic Use

The most basic use is to simple include the class:

```php
use Intouch\Newrelic\Newrelic;

$newrelic = new Newrelic();
```

This will load the class and, if the NewRelic agent is installed, give you access to the API.  If the agent is not installed, it will simply act as a pass-through and return `false` from all methods.

If you want some notification if the NewRelic agent cannot be loaded, pass `true` to the constructor:

```php
use Intouch\Newrelic\Newrelic;

$newrelic = new Newrelic(true);
```

If the agent API is not found, this will now throw a `RuntimeException`.
