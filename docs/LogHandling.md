# Log handling

For logging you can use a callback function or logger implementation Psr/Log/LoggerInterface

Log callback example:

```php
$logger = static function($level, $message,  $context) {
    // process log message example
    print_r([$level, $message, $context]);
};
```

Psr/Log example:

```php
$logger = new /Psr/Log/LoggerInterface();

```

Set logger to the library:

```php
use Apirone\API\Log\LogWrapper;

LogWrapper::setLogger($logger);

```
