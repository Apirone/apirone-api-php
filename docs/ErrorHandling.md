# Error handling

For response logging you can use a callback function and process a message as you want.

```php

// callback function
$log_handler = static function($message) {
    // process a message
};

// Set callback to Error Dispatcher
Apirone\API\Http\ErrorDispatcher::setCallback($log_handler);

```

All request errors generate different exceptions and you need to wrap all requests into ```try...catch```.
