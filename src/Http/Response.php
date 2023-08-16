<?php

namespace Apirone\API\Http;

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

    public function __construct(?array $data = null)
    {
        $this->code    = $data['code'] ?? null;
        $this->headers = $data['headers'] ?? null;
        $this->body    = $data['body'] ?? null;
    }

    /**
     * Get HTTP response headers
     *
     * @return  mixed
     */ 
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get HTTP response code
     *
     * @return  mixed
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get HTTP response body
     *
     * @return  mixed
     */ 
    public function getBody()
    {
        return $this->body;
    }

    public function hasError()
    {
        return ($this->code !== null && (int) $this->code >= 400) ? true : false;
    }
}
