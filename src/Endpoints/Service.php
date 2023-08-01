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
use Apirone\API\Http\Request;
use stdClass;

/**
 * Info
 *
 * Service information methods bundle
 *
 * @see https://apirone.com/docs/service/#service-info
 * @package Apirone\API\Endpoints
 */
class Service
{
    /**
     * Account
     *
     * Get general information about account
     * @see https://apirone.com/docs/service/#service-info
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public static function account(): \stdClass {
        return Request::options('v2/accounts');
    }

    /**
     * Wallets
     *
     * Get general information about wallet
     * @see https://apirone.com/docs/service/#service-info
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public static function wallet(): \stdClass {
        return Request::options('v2/wallets');
    }

    /**
     * Network Fee
     * 
     * A network fee is included in a transaction in order to have 
     * the transaction processed by a miner and confirmed by the network.
     * @see https://apirone.com/docs/fee/#network-fee
     *
     * @param string $currency 
     * @return array
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public static function fee(string $currency): array
    {
        $uri = sprintf('v2/networks/%s/fee', $currency);

        return Request::get($uri);
    }

    /**
     * Exchange Rate
     *
     * Exchange rates are taken from several sources such as:
     * - Coinmarketcap <https://coinmarketcap.com/>
     * - Coinpaprika <https://coinpaprika.com/>
     * - Coingecko <https://www.coingecko.com/>
     *
     * Ticker 
     * contains current Market Prices and exchanges rate API.
     * @see https://apirone.com/docs/rate/#exchange-rate
     *
     * @param string $currency 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public static function ticker(string $currency): \stdClass
    {
        return Request::get('v2/ticker', ['currency' => $currency]);
    }

    public static function fiat2crypto($value, $from='usd', $to = 'btc'): float
    {
        if ($from == 'btc') {
            return $value;
        }
        $url = sprintf('v1/to%s', $to);
        $options = [];
        $options['currency'] = $from;
        $options['value']    = $value;

        return (float) Request::get($url, $options);
    }
}

