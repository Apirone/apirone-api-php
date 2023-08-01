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

namespace Apirone\API\Endpoints;

use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Exceptions\InternalServerErrorException;
use Apirone\API\Http\Request;
use stdClass;

/**
 * Authorization
 *
 * Some endpoints require Authorization. Our service provides two authorization options:
 * either JWT token or transfer key. 
 * You can use one of them to request some protected endpoints:
 * - process callbacks log,
 * - make transfers,
 * - check private invoice information
 * - change account/wallet settings, etc.
 *
 * @package Apirone\API\Endpoints
 */
class Authorization
{
    /**
     * Get token
     *
     * @param string $login 
     * @param string $password 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public static function login(string $login, string $password): \stdClass
    {
        $options = [
            'login' => $login,
            'password' => $password,
        ];

        return Request::post('v2/auth/login', $options);
    }

    /**
     * Refresh token
     *
     * @param string $refreshToken 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public static function refresh(string $refreshToken): \stdClass
    {
        $headers = [
            'Authorization' => "Bearer " . $refreshToken,
        ];

        return  Request::post('v2/auth/refresh-token', [], $headers);
    }

    /**
     * Destroy token
     *
     * @param string $accessToken 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public static function logout(string $accessToken): \stdClass
    {
        $headers = [
            'Authorization' => "Bearer " . $accessToken,
        ];

        return Request::post('v2/auth/logout', [], $headers);
    }
}



