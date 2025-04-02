<?php

namespace Apirone\API\Http;

// use function Apirone\Api\Polyfill\JsonValidate;
use function Apipone\API\Polyfill\JsonValidate;

use Exception;

class Response
{
    /**
     * HTTP response code
     *
     * @var mixed
     */
    protected $code;

    /**
     * HTTP response headers
     *
     * @var mixed
     */
    protected $headers;

    /**
     * HTTP response body
     *
     * @var mixed
     */
    protected $body;

    public function __construct($code = null, $headers = null, $body = null )
    {
        $this->code    = (int) $code;
        $this->headers = $headers;
        $this->body    = $body;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'error') {
            return $this->getError();
        }
        if (\property_exists($this, $name)) {

            $class = new \ReflectionClass(static::class);

            $property = $class->getProperty($name);
            $property->setAccessible(true);

            if(!$property->isInitialized($this)) {
                return null;
            }

            return $property->getValue($this);
        }

        $trace = \debug_backtrace();
        \trigger_error(
            'Undefined property ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            \E_USER_NOTICE
        );

        return null;
    }
    /**
     * Get HTTP response headers
     *
     * @return  mixed
     * @deprecated Just get by property name
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get HTTP response code
     *
     * @return  mixed
     * @deprecated Just get by property name
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get HTTP response body
     *
     * @return  mixed
     * @deprecated Just get by property name
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Is response has error
     * @return bool
     */
    public function hasError()
    {
        return ($this->code !== null && $this->code >= 400) ? true : false;
    }

    /**
     
     */
    public function getError()
    {
        if ($this->code < 400) {
            return '';
        }
        $message = json_decode($this->body);
        if (json_last_error() === JSON_ERROR_NONE && is_object($message)) {
            return property_exists($message, 'message') ? $message->message : $message;
        }
        if ($this->body == strip_tags($this->body)) {
            return $this->body;
        }
        switch ($this->code) {
            case 400:
                $text = 'Bad Request';
                break;
            case 401:
                $text = 'Unauthorized';
                break;
            case 403:
                $text = 'Forbidden';
                break;
            case 404:
                $text = 'Not Found';
                break;
            case 405:
                $text = 'Method Not Allowed';
                break;
            case 500:
                $text = 'Internal Server Error';
                break;
            default:
                $text = 'Unknown Runtime Error';
        }
        return $text;
    }
}
