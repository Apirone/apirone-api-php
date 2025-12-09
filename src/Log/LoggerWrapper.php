<?php
/*
 * This file is part of the Apirone API library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\API\Log;

use Apirone\API\Log\LogLevel;

class LoggerWrapper
{
    static $loggerCallback;

    static $loggerInstance;

    static bool $handler = false;

    /**
     * Set Logger
     *
     * @param mixed $logger
     * @throws \InvalidArgumentException
     */
    public static function setLogger($logger)
    {
        if (is_object($logger) && method_exists($logger, 'log')) {
            self::$loggerInstance = $logger;
            self::$handler = true;
        }
        elseif (is_callable($logger)) {
            self::$loggerCallback = $logger;
            self::$handler = true;
        }
        else {
            throw new \InvalidArgumentException('Invalid logger');
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function emergency($message, array $context = array())
    {
        self::log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function alert($message, array $context = array())
    {
        self::log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function critical($message, array $context = array())
    {
        self::log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function error($message, array $context = array())
    {
        self::log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function warning($message, array $context = array())
    {
        self::log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function notice($message, array $context = array())
    {
        self::log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function info($message, array $context = array())
    {
        self::log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function debug($message, array $context = array())
    {
        self::log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public static function log($level, $message, array $context = array())
    {
        if (self::$loggerInstance !== null) {
            self::$loggerInstance->log($level, $message, $context);
        } elseif (self::$loggerCallback !== null) {
            call_user_func_array(self::$loggerCallback, array($level, $message, $context));
        }
    }
}
