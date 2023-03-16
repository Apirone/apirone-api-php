<?php
/*
 * This file is part of the Apirone SDK library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\API\Http;

use Apirone\API\Http\Error;
use Apirone\API\Http\ErrorDispatcher;
use Apirone\API\Exeptions\RuntimeException;
use Apirone\API\Exeptions\ValidationFailedException;
use Apirone\API\Exeptions\ForbiddenException;
use Apirone\API\Exeptions\NotFoundException;
use Apirone\API\Exeptions\MethodNotAllowedExeption;
use Apirone\API\Exeptions\UnauthorizedException;
use Apirone\API\Exeptions\InternalServerErrorExeption;

final class Request {

    private static string $baseURI = 'https://apirone.com/api/';

    private static string $userAgent = 'apirone-sdk-php/1.0';

    public static function setBaseUri($uri)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $class->setStaticPropertyValue('baseURI', $uri);
    }

    public static function setUserAgent($value)
    {
        $class = new \ReflectionClass('\Apirone\API\Http\Request');
        $baseUserArent = $class->getProperty('userAgent');
        $class->setStaticPropertyValue('baseURI', $baseUserArent . ' ' . $value);
    }

    public static function get(string $url, array $options = [], array $headers = [])
    {
        $result = self::execute('get', $url, $options, $headers);

        return json_decode($result);
    }

    public static function post(string $url, array $options = [], array $headers = [])
    {
        $result = self::execute('post', $url, $options, $headers);

        return json_decode($result);
    }

    public static function patch(string $url, array $options = [], array $headers = [])
    {
        $result = self::execute('patch', $url, $options, $headers);

        return json_decode($result);
    }

    public static function options(string $url, array $options = [], array $headers = [])
    {
        $result = self::execute('options', $url, $options, $headers);

        return json_decode($result);
    }

    public static function execute(string $method, string $url, array $options = [], array $headers = [])
    {
        $error = new Error();

        if ($method && $url) {
            $curl_options = array(
                CURLOPT_URL => static::$baseURI . $url,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_INFILESIZE => Null,
                CURLOPT_HTTPHEADER => array(),
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10,
            );

            $curl_options[CURLOPT_HTTPHEADER][] = 'User-Agent: ' . self::$userAgent;
            $curl_options[CURLOPT_HTTPHEADER][] = 'Accept-Charset: utf-8';
            $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';

            foreach ($headers as $key => $value) {
                $curl_options[CURLOPT_HTTPHEADER][] = $key . ': ' . $value;
            }
            switch (strtolower(trim($method))) {
                case 'get':
                    $curl_options[CURLOPT_HTTPGET] = true;
                    $curl_options[CURLOPT_URL] .= '?' . self::prepare($options, false);
                    break;

                case 'post':
                    if (!empty($options))
                        $curl_options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                    $curl_options[CURLOPT_POST] = true;
                    $curl_options[CURLOPT_POSTFIELDS] = self::prepare($options, true);
                    break;

                case 'patch':
                    if (!empty($options))
                        $curl_options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
                    $curl_options[CURLOPT_POST] = true;
                    $curl_options[CURLOPT_POSTFIELDS] = self::prepare($options, true);
                    $curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                    break;
                    
                default:
                    $curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            }
            $ch = curl_init();
            curl_setopt_array($ch, $curl_options);
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);

            if (curl_errno($ch)) {
                $error('CURL ERROR: ' .  curl_strerror(curl_errno($ch)), json_encode($info));
                ErrorDispatcher::dispatch($error->get());

                throw new RuntimeException('CURL ERROR: ' . curl_strerror(curl_errno($ch)));
            }
            curl_close($ch);

            $body = '';
            $parts = explode("\r\n\r\n", (string) $result, 3);

            if (isset($parts[0]) && isset($parts[1])) {
                $body = (($parts[0] == 'HTTP/1.1 100 Continue') && isset($parts[2])) ? $parts[2] : $parts[1];
            }
            if ($info['http_code'] >= 400) {
                $error($body, json_encode($info));
                ErrorDispatcher::dispatch($error->get());

                $exception = $info['http_code'] .': ' . $body;

                switch($info['http_code']) {
                    case 400:
                        throw new ValidationFailedException($exception);
                    case 401:
                        throw new UnauthorizedException($exeption);
                    case 403:
                        throw new ForbiddenException($exception);
                    case 404:
                        throw new NotFoundException($exception);
                    case 405:
                        throw new MethodNotAllowedExeption($exception);
                    case 500:
                        throw new InternalServerErrorExeption($exeption);
                    default:
                        throw new RuntimeException($exception);
                }

            }

            return $body;
        }
    }


    public static function prepare($options, $json ) 
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

    public static function processError() {
    
    }
}
