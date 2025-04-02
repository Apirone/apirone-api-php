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

namespace Apirone\API\Http;

// use Apirone\API\Http\Error;
use Apirone\API\Log\LoggerWrapper;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\JsonException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\InternalServerErrorException;
use ReflectionException;

final class Request
{
    private static string $baseURI = 'https://apirone.com/api/';

    private static string $userAgent = 'apirone-api-php/1.0';

    private static ?string $proxy = null;

    public static function setBaseUri($uri)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $class->setStaticPropertyValue('baseURI', $uri);
    }

    /**
     * Set additional UserAgent info
     *
     * @param mixed $value
     * @return void
     * @throws ReflectionException
     */
    public static function setUserAgent($value)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $baseUserAgent = $class->getProperty('userAgent');
        $class->setStaticPropertyValue('baseURI', $baseUserAgent . ' ' . $value);
    }

    /**
     * Set cUPL proxy option
     *
     * @param mixed $proxy
     * @return void
     */
    public static function setProxy($proxy)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $class->setStaticPropertyValue('proxy', $proxy);
    }

    /**
     * GET Request
     *
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws JsonException
     */
    public static function get(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('get', $path, $options, $headers);

        return static::decodeData($result);
    }

    /**
     * POST Request
     *
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws JsonException
     */
    public static function post(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('post', $path, $options, $headers);

        return static::decodeData($result);
    }

    /**
     * PATCH Request
     *
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws JsonException
     */
    public static function patch(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('patch', $path, $options, $headers);

        return static::decodeData($result);
    }

    /**
     * OPTIONS Request
     *
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return mixed
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws JsonException
     */
    public static function options(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('options', $path, $options, $headers);

        return static::decodeData($result);
    }

    /**
     * Execute Request
     *
     * @param string $method
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return Response
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     */
    public static function execute(string $method, string $path, array $options = [], array $headers = [])
    {
        $curlHandle = curl_init();
        $curlOpt = static::prepareCurlOptions($method, $path, $options, $headers);
        curl_setopt_array($curlHandle, $curlOpt);

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);

        $result     = curl_exec($curlHandle);
        $headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
        $headers    = static::parseResponseHeaders(substr((string)$result, 0, $headerSize));
        $body       = substr((string)$result, $headerSize);
        $info       = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        if ($result === false) {
            static::handleCurlError(curl_error($curlHandle), curl_errno($curlHandle), $info);
        }

        $response = new Response($info['http_code'], $headers, $body);

        static::log($info, $method, $options, $curlOpt, $response);

        if ($response->hasError()) {
            static::handleResponseError($response);
        }

        return $response;
    }

    public static function log($info, $method, $options, $curlOpt, $response)
    {
        if (LoggerWrapper::$handler !== null) {
            $context = array();

            if (isset($curlOpt[CURLOPT_HTTPHEADER])) {
                $context['REQUEST']['headers'] = $curlOpt[CURLOPT_HTTPHEADER];
            }
            if (!empty($options)) {
                $context['REQUEST']['params'] = self::maskCredentials($options);
            }
            if (isset($curlOpt[CURLOPT_POSTFIELDS])) {
                $context['REQUEST']['body'] = self::maskCredentials($curlOpt[CURLOPT_POSTFIELDS]);
            }
            $context['RESPONSE']['code'] = $response->code;
            $context['RESPONSE']['body'] = self::maskCredentials($response->body);
            $errorText = ($response->hasError()) ? $response->error . ' ' : '';

            $message = strtoupper($method) . ' ' . $response->code . ' ' . $errorText . $info['url'];

            $level = $response->hasError() ? 'error' : 'info';

            LoggerWrapper::log($level, $message, $context);
        }
    }


    /**
     * cURL options prepare
     *
     * @param string $method
     * @param string $path
     * @param array $options
     * @param array $headers
     * @return array
     */
    protected static function prepareCurlOptions(string $method, string $path, array $options = [], array $headers = []): array
    {
        // Set options
        $curlopt = array(
            CURLOPT_URL => static::$baseURI . $path,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_INFILESIZE => null,
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
        );

        if (static::$proxy) {
            $curlopt[CURLOPT_PROXY] = static::$proxy;
            $curlopt[CURLOPT_HTTPPROXYTUNNEL] = true;
        }

        $curlopt[CURLOPT_HTTPHEADER][] = 'User-Agent: ' . static::$userAgent;
        $curlopt[CURLOPT_HTTPHEADER][] = 'Accept-Charset: utf-8';
        $curlopt[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

        foreach ($headers as $key => $value) {
            $curlopt[CURLOPT_HTTPHEADER][] = $key . ': ' . $value;
        }
        switch (strtolower(trim($method))) {
            case 'get':
                $curlopt[CURLOPT_HTTPGET] = true;
                $curlopt[CURLOPT_URL] .= '?' . static::preparePost($options, false);
                break;

            case 'post':
                if (!empty($options)) {
                    $curlopt[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                }
                $curlopt[CURLOPT_POST] = true;
                $curlopt[CURLOPT_POSTFIELDS] = static::preparePost($options, true);
                break;

            case 'patch':
                if (!empty($options)) {
                    $curlopt[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                }
                $curlopt[CURLOPT_POST] = true;
                $curlopt[CURLOPT_POSTFIELDS] = static::preparePost($options, true);
                $curlopt[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                break;

            default:
                $curlopt[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        }

        return $curlopt;
    }

    /**
     * Prepare POST data
     *
     * @param mixed $options
     * @param mixed $json
     * @return string|false
     */
    protected static function preparePost($options, $json)
    {
        if (is_string($options)) {
            return $options;
        }
        if ($json) {
            return empty($options) ? '' : json_encode($options);
        } else {
            return http_build_query($options);
        }
    }

    /**
     * Handle cURL error
     *
     * @param mixed $error
     * @param mixed $errno
     * @return never
     * @throws RuntimeException
     */
    protected static function handleCurlError($error, $errno, $info)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $message = 'Could not connect to Apirone API.';
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $message = 'Could not verify SSL certificate.';
                break;
            default:
                $message = 'Unexpected error communicating.';
        }
        $message .= sprintf(' Network error(%s): %s. Request to %s', $errno, $error, $info['url']);

        if (LoggerWrapper::$handler !== null) {
            LoggerWrapper::error($message);
        }

        throw new RuntimeException($message);
    }

    /**
     * Response error handler
     *
     * @param Response $response
     * @return never
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws RuntimeException
     */
    protected static function handleResponseError(Response $response)
    {
        $code = $response->code;
        $message = $response->error;

        switch($code) {
            case 400:
                throw new ValidationFailedException($message, $code);
                break;
            case 401:
                throw new UnauthorizedException($message, $code);
                break;
            case 403:
                throw new ForbiddenException($message, $code);
                break;
            case 404:
                throw new NotFoundException($message, $code);
                break;
            case 405:
                throw new MethodNotAllowedException($message, $code);
                break;
            case 500:
                throw new InternalServerErrorException($message, $code);
                break;
            default:
                throw new RuntimeException($message, $code);
        }
    }

    /**
     * Parse response headers to array
     *
     * @param mixed $rawHeaders
     * @return array
     */
    protected static function parseResponseHeaders($rawHeaders)
    {
        $headers = array();
        $key = '';

        foreach (explode("\n", $rawHeaders) as $headerRow) {
            if (trim($headerRow) === '') {
                break;
            }
            $headerArray = explode(':', $headerRow, 2);

            if (isset($headerArray[1])) {
                if (!isset($headers[$headerArray[0]])) {
                    $headers[trim($headerArray[0])] = trim($headerArray[1]);
                } elseif (is_array($headers[$headerArray[0]])) {
                    $headers[trim($headerArray[0])] = array_merge($headers[trim($headerArray[0])], array(trim($headerArray[1])));
                } else {
                    $headers[trim($headerArray[0])] = array_merge(array($headers[trim($headerArray[0])]), array(trim($headerArray[1])));
                }

                $key = $headerArray[0];
            } else {
                if (substr($headerArray[0], 0, 1) === "\t") {
                    $headers[$key] .= "\r\n\t" . trim($headerArray[0]);
                } elseif (!$key) {
                    $headers[0] = trim($headerArray[0]);
                }
            }
        }

        return $headers;
    }

    /**
     * Decode response body to JSON
     *
     * @param Response $response
     * @return mixed
     * @throws JsonException
     */
    protected static function decodeData(Response $response)
    {
        $result = json_decode($response->getBody());
        if ($result === null) {
            throw new JsonException('Failed to decode JSON', json_last_error());
        }

        return $result;
    }

    protected static function maskCredentials($data)
    {
        $key = 'transfer-key';
        $mask = '****';
        $type = gettype($data);

        switch (gettype($data)) {
            case 'array':
                if (array_key_exists($key, $data)) {
                    $data[$key] = $mask;
                }
                $data = json_decode(json_encode($data));
                break;
            case 'string':
                $data = json_decode($data);
                if (json_last_error() === JSON_ERROR_NONE && !empty($data) && property_exists($data, $key)) {
                    $data->{$key} = $mask;
                }
                break;
        }
        return $data;
    }
}
