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

final class Request {

    private static string $baseURI = 'https://apirone.com/api/';

    private static string $userAgent = 'apirone-api-php/1.0';
    
    private static ?string $proxy = null;

    public static function setBaseUri($uri)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $class->setStaticPropertyValue('baseURI', $uri);
    }

    public static function setUserAgent($value)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $baseUserAgent = $class->getProperty('userAgent');
        $class->setStaticPropertyValue('baseURI', $baseUserAgent . ' ' . $value);
    }

    public static function setProxy($proxy)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $class->setStaticPropertyValue('proxy', $proxy);
    }

    public static function get(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('get', $path, $options, $headers);

        return static::decodeData($result);
    }

    public static function post(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('post', $path, $options, $headers);

        return static::decodeData($result);
    }

    public static function patch(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('patch', $path, $options, $headers);

        return static::decodeData($result);
    }

    public static function options(string $path, array $options = [], array $headers = [])
    {
        $result = static::execute('options', $path, $options, $headers);

        return static::decodeData($result);
    }

    public static function execute(string $method, string $path, array $options = [], array $headers = [])
    {
        $curlHandle = curl_init();
        $curlOpt = static::prepareCurlOptions($method, $path, $options, $headers);
        curl_setopt_array($curlHandle, $curlOpt);

        static::logRequest($method, $path, $options, $curlOpt);

        $result       = curl_exec($curlHandle);        
        $httpHeaderSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
        $httpHeaders    = static::parseResponseHeaders(substr((string)$result, 0, $httpHeaderSize));
        $httpBody       = substr((string)$result, $httpHeaderSize);
        $responseInfo   = curl_getinfo($curlHandle);
        $curlError      = curl_error($curlHandle);
        $curlErrno      = curl_errno($curlHandle);
        curl_close($curlHandle);

        if ($result === false) {
            static::handleCurlError($curlError, $curlErrno);
        }
        
        $response = new Response(array(
            'code'    => $responseInfo['http_code'],
            'headers' => $httpHeaders,
            'body'    => $httpBody
        ));

        if ($response->hasError()) {
            static::handleResponseError($response);
        }
        static::logResponse($response);

        return $response;
    }

    protected static function prepareCurlOptions(string $method, string $path, array $options = [], array $headers = []): array
    {
        // Set options
        $curlopt = array(
            CURLOPT_URL => static::$baseURI . $path,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_INFILESIZE => Null,
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
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

    protected static function preparePost($options, $json ) 
    {
        if (is_string($options)) {
            return $options;
        }
        if ($json) {
            return json_encode($options);
        }
        else {
            return http_build_query($options);
        }
    }

    public static function logRequest(string $method, $path, $options, $curlOpt)
    {
        if (LoggerWrapper::$handler !== null) {
            $message = 'Send request: ' . $method . ' ' . $path;
            $context = array();
            if (!empty($options)) {
                $context['_params'] = $options;
            }
            if (isset($curlOpt[CURLOPT_POSTFIELDS])) {
                $context['_body'] = $curlOpt[CURLOPT_POSTFIELDS];
            }
            if (isset($curlOpt[CURLOPT_HTTPHEADER])) {
                $context['_headers'] = $curlOpt[CURLOPT_HTTPHEADER];
            }

            LoggerWrapper::info($message, $context);
        }
    }

    public static function logResponse(Response $response)
    {
        if (LoggerWrapper::$handler !== null) {
            $message = 'Response with code ' . $response->getCode() . ' received.';
            $context = array();
            $httpBody = $response->getBody();
            if (!empty($httpBody)) {
                $data = json_decode($httpBody, true);
                if (JSON_ERROR_NONE !== json_last_error()) {
                    $data = $httpBody;
                }
                $context['_body'] = $data;
            }
            $httpHeaders = $response->getHeaders();
            if (!empty($httpHeaders)) {
                $context['_headers'] = $httpHeaders;
            }
            LoggerWrapper::info($message, $context);
        }
    }

    protected static function handleCurlError($error, $errno)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $message = 'Could not connect to Apirone API. Please check your internet connection and try again.';
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $message = 'Could not verify SSL certificate.';
                break;
            default:
                $message = 'Unexpected error communicating.';
        }
        $message .= "\n\n(Network error [errno $errno]: $error)";

        throw new RuntimeException($message);
    }

    protected static function handleResponseError(Response $response)
    {
            $code = $response->getCode();
            $httpBody = $response->getBody();

            $message = json_decode($httpBody);
            if (JSON_ERROR_NONE !== json_last_error()) {
                $message = $httpBody;
            }
            if (is_object($message)) {
                $message = $message->message;
            }
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

    protected static function decodeData(Response $response)
    {
        $result = json_decode($response->getBody());
        if ($result === null) {
            throw new JsonException('Failed to decode JSON', json_last_error());
        }

        return $result;
    }
}
