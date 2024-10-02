# Sentry.io for Craft CMS 3.x

[Sentry.io](https://sentry.io/) integration for Craft CMS. Inspired by [born05/craft-sentry](https://github.com/born05/craft-sentry), but with our own twist.

## Installation

To install the plugin, follow these instructions in your terminal.
```shell script
cd /path/to/project
composer require statikbe/craft-sentry
./craft plugin/install craft-sentry
```
## Configration

Create a config/craft-sentry.php config file with the following contents:
```php
<?php

return [
    'enabled'       => true,
    'anonymous'     => true,
    'clientDsn'     => getenv('SENTRY_DSN') ?: 'https://example@sentry.io/123456789',
    'excludedCodes' => ['400', '404', '429'],
    'excludedExceptions' => [],
    'release'       => getenv('SENTRY_RELEASE') ?: null,
];
```

## Usage

To let Sentry log your exception, you don't really need to do anything. Install the plugin and add your DSN and you're set.

When you're writing your own custom code and throwing your own exception, you'll also want to catch those and send them to Sentry. That can be done like this:
```php
<?php

use statikbe\sentry\Sentry;
use yii\base\InvalidConfigException; // Don't copy this line, it's just here to make the example theoractically correct ;) 

try {
    throw new InvalidConfigException("Something went wrong here...");
} catch (Exception $e) {
    Sentry::handleException($e);
}
```

The plugin works for exceptions thrown in web requests as well as console requests. For web requests, the url where the error happened is included.


### Excluding specific exceptions
Using the ``excludedExceptions``, you can stop specific types of exceptions from being logged to Sentry, for example:
````php
'excludedExceptions' => [
    \craft\errors\ImageTransformException::class,
],
````
---
 
Brought to you by [Statik.be](https://www.statik.be)
