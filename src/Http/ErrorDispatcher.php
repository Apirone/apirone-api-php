<?php

namespace Apirone\API\Http;

class ErrorDispatcher {
    static $handler = false;

    public static function dispatch($message)
    {
        if (!self::$handler) {
            return;
        }

        call_user_func(self::$handler, $message);
    }

    public static function setCallback($callback)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\ErrorDispatcher');
        $class->setStaticPropertyValue('handler', $callback);
    }

}