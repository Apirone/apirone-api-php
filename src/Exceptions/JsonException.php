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

namespace Apirone\API\Exceptions;

class JsonException extends \UnexpectedValueException
{
    public static $errorMessages = [
        JSON_ERROR_NONE	          => 'No error has occurred',
        JSON_ERROR_DEPTH          => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR      => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX         => 'Syntax error',
        JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        JSON_ERROR_UTF16          => 'Malformed UTF-16 characters, possibly incorrectly encoded',
    ];

    public function __construct($message = "", $code = 0, $previous = null)
    {
        $errorMsg = static::$errorMessages[$code] ?? 'Unknown JSON-decode error';
        $message = sprintf('%s %s', $message, $errorMsg);
        parent::__construct($message, $code, $previous);
    }
}