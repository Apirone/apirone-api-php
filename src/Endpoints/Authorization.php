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

namespace Apirone\API\Endpoints;

use Apirone\API\Http\Request;

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
    public static function login(string $login, string $password): \stdClass
    {
        $options = [
            'login' => $login,
            'password' => $password,
        ];

        return Request::post('v2/auth/login', $options);
    }

    public static function refresh(string $refreshToken): \stdClass
    {
        $headers = [
            'Authorization' => "Bearer " . $refreshToken,
        ];

        return  Request::post('v2/auth/refresh-token', [], $headers);
    }

    public static function logout(string $accessToken): \stdClass
    {
        $headers = [
            'Authorization' => "Bearer " . $accessToken,
        ];

        return Request::post('v2/auth/logout', [], $headers);
    }
}



