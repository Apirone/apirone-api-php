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

use Apirone\API\Helpers\CallbackHelper;
use Apirone\API\Helpers\DestinationsHelper;
use Apirone\API\Endpoints\EndpointAuthTrait;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Exceptions\InternalServerErrorException;
use Apirone\API\Http\Request;
use stdClass;

use function GuzzleHttp\describe_type;

class Wallet
{
    use EndpointAuthTrait;

    private string $wallet;

    private function __construct(string $wallet, ?string $transferKey = null)
    {
        $this->wallet     = $wallet;
        $this->transferKey = $transferKey;
    }

    /**
     * Create wallet
     *
     * @see https://apirone.com/docs/wallet/#create-wallet
     *
     * @param string $currency 
     * @param null|object $callback 
     * @param null|array $destinations 
     * @param null|string $fee 
     * @param null|int $feeRate 
     * @return Wallet 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public static function create(
        string  $currency,
        ?object $callback = null,
        ?array  $destinations = null,
        ?string $fee = null,
        ?int    $feeRate = null
    ): Wallet {

        $options['currency'] = $currency;

        if($callback !== null) {
            $options['callback'] = $callback instanceof CallbackHelper ? $callback->toJson() : $callback;
        }

        if($destinations !== null) {
            $options['destinations'] = $destinations instanceof DestinationsHelper ? $destinations->toArray() : $destinations;
        }

        if($fee !== null) {
            $options['fee'] = $fee;
        }

        if ($fee == 'custom' && $feeRate !== null) {
            $options['fee-rate'] = $feeRate;
        }

        $wallet = Request::post('v2/wallets', $options);

        return $wallet;    
    }

    /**
     * Init existing wallet by params
     *
     * @param string $wallet 
     * @param null|string $transferKey 
     * @return Wallet
     */
    public static function init(string $wallet, ?string $transferKey = null): Wallet
    {
        $new = new static($wallet, $transferKey);

        return $new;
    }

    /**
     * Wallet Info
     *
     * Gets information about the wallet.
     *
     * @see https://apirone.com/docs/wallet/#wallet-info
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public function info(): \stdClass
    {
        $url    = sprintf('v2/wallets/%s', $this->wallet);

        return Request::get($url);
    }

    /**
     * Wallet Balance
     *
     * @see https://apirone.com/docs/wallet/#wallet-balance
     *
     * @param null|string $address 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public function balance(?string $address = null): \stdClass
    {
        $url    = sprintf('v2/wallets/%s/balance', $this->wallet);
        $options = [];

        if(!is_null($address)) {
            $options['address'] = $address;
        }

        return Request::get($url, $options);
    }

    /**
     * Generate new wallet address
     *
     * This function generates unique crypto addresses to use.
     * There are no expiration date and no transaction limit for addresses.
     *
     * @see https://apirone.com/docs/wallet/#generate-address
     *
     * @param string|null $addrType 
     * @param mixed $callback 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public function generateAddress(string $addrType = null, $callback = null): \stdClass
    {
        $url    = sprintf('v2/wallets/%s/addresses', $this->wallet);
        $options = [];
    
        if ($addrType !== null) {
            $options['addr-type'] = $addrType;
        }

        if ($callback !== null) {
            if ($callback instanceof CallbackHelper) {
                $options['callback'] = $callback->toJson();
            }
            else {
                $options['callback'] = (gettype($callback) == 'string') ? json_decode($callback) : $callback;
            }

        }

        return Request::post($url, $options);
    }

    /**
     * Address info
     *
     * Some information can be requested about an address in a selected wallet: address type, creation date etc.
     *
     * @see https://apirone.com/docs/wallet/#address-info
     *
     * @param string $address 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public function addressInfo(string $address): \stdClass
    {
        $url = sprintf('v2/wallets/%s/addresses/%s', $this->wallet, $address);

        return Request::get($url);
    }

    /**
     * Address balance
     *
     * Gets the balance of a specified address from wallet.
     *
     * @see https://apirone.com/docs/wallet/#address-balance
     *
     * @param string $address 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws InternalServerErrorException 
     */
    public function addressBalance(string $address): \stdClass
    {
        $url = sprintf('v2/wallets/%s/addresses/%s/balance', $this->wallet, $address);

        return Request::get($url);
    }

    /**
     * Wallet Addresses
     *
     * Shows a list of all the wallet addresses, depending on the provided currency. Contains short information about each address.
     *
     * @see https://apirone.com/docs/wallet/#wallet-addresses
     *
     * @param string $currency 
     * @param array  $options
     *
     * @return \stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addresses($options = []): \stdClass
    {
        $url    = sprintf('v2/wallets/%s/addresses', $this->wallet);

        $options = $options instanceof AddressesOptionsBuilder ? $options->toArray() : $options;

        return Request::get($url, $options);
    }

    /**
     * Estimation
     * 
     * Estimates a transaction before sending. It allows finding out the amounts of network
     * and processing fees and checks the destinations of transfer in advance.
     *
     * @see https://apirone.com/docs/wallet/#estimation
     *
     * @param $options array|TransferOptionsBuilder
     *
     * @return object
     *
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function estimation($options): \stdClass
    {
        $url = sprintf('v2/wallets/%s/transfer', $this->wallet);

        if ($options instanceof TransferOptionsBuilder) {
            $options = (array)$options->toJson();
            /** 
            * Destinations to string format
            * @see https://apirone.com/docs/wallet/#estimation
            */            
            $items = [];
            foreach($options['destinations'] as $item) {
                $items[] = $item->address . ':' . $item->amount;
            }
            $options['destinations'] = implode(',', $items);
        }
        
        return Request::get($url, $options);
    }

    /**
     * Transfer
     *
     * Sends currency amount or percentage from the balance in the provided currency.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/wallet/#transfer
     *
     * @param $options array|TransferOptionsBuilder
     *
     * @return stdClass
     * @throws GlobalRuntimeException 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function transfer ($options): \stdClass
    {
        $url = sprintf('v2/wallets/%s/transfer', $this->wallet);

        if ($options instanceof TransferOptionsBuilder) {
            $options = (array)$options->toJson();
        }

        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::post($url, $options, $headers);
    }

    /**
     * Wallet History
     *
     * Request wallet transaction history, including opportunities to filter by address,
     * transfer date (calendar period) and type (payment or receipt).
     * A payment is an element created as a result of transferring funds from a wallet.
     * It combines all blockchain transactions of the payment and contains detailed
     * information about fees and recipients. A receipt is an incoming transfer,
     * which shows a new arrival of funds to a wallet.
     *
     * @see https://apirone.com/docs/wallet/#wallet-history
     *
     * @param array|HistoryOptionsBuilder $options 
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function history ($options = []): \stdClass
    {
        $url = sprintf('v2/wallets/%s/history', $this->wallet);

        if($options instanceof HistoryOptionsBuilder) {
            $options = $options->toArray();
        }

        return Request::get($url, $options);
    }

    /**
     * Wallet History Item
     *
     * The detailed information of the wallet history item contains the list of
     * addresses, the fees, and the list of incoming/outgoing transactions.
     * 
     * @see https://apirone.com/docs/wallet/#wallet-history-item
     * 
     * @param string $HistoryItemID
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function historyItem (string $HistoryItemID): \stdClass
    {
        $url = sprintf('v2/wallets/%s/history/%s', $this->wallet, $HistoryItemID);

        return Request::get($url);
    }

    /**
     * Wallet Address History
     *
     * Outputs a list of operations of a specified wallet address.
     *
     * @see https://apirone.com/docs/wallet/#address-history
     *
     * @param string $address 
     * @param array $options 
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addressHistory (string $address, array $options = []): \stdClass
    {
        $url = sprintf('v2/wallets/%s/addresses/%s/history', $this->wallet, $address);

        return Request::get($url, $options);
    }

    /**
     * Wallet Callback Info
     *
     * Outputs information on the saved callback for the wallet.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/wallet/#wallet-callback-info
     *
     * @return stdClass
     * @throws GlobalRuntimeException 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function callbackInfo (): \stdClass
    {
        $url = sprintf('v2/wallets/%s/callback', $this->wallet);
        
        $options = [];
        $headers = [];
        $this->setRequestAuth($options, $headers);
        
        return Request::get($url, $options, $headers);
    }
    /**
     * Address Callback Info
     *
     * Outputs information on the saved callback for a specified address.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/wallet/#address-callback-info
     *
     * @param string $address
     *
     * @return stdClass
     * @throws GlobalRuntimeException 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addressCallbackInfo (string $address): \stdClass
    {
        $url = sprintf('v2/wallets/%s/addresses/%s/callback', $this->wallet, $address);

        $headers = [];
        $options = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }

    /**
     * Address Callback Log
     * 
     * Information on the Callback log in the wallet: 
     * which requests the service sent to you and which responses you, as a client, gave to them. 
     * This is a basic tool for debugging. Authorization is required.
     *
     * @see https://apirone.com/docs/wallets/#address-callback-log
     *
     * @param string $address 
     * @param array $options 
     * @return mixed 
     * @throws GlobalRuntimeException 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addressCallbackLog (string $address, $options = []): \stdClass
    {
        $url = sprintf('v2/wallets/%s/addresses/%s/callback-log', $this->wallet, $address);

        if($options instanceof PagerOptionsBuilder) {
            $options = $options->toArray();
        }

        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }

    /**
     * Settings
     *
     * Allow adding or changing the callback data of wallets and setting destinations for forwarding
     * Authorization is required.
     *
     * @see https://apirone.com/docs/settings/#settings
     *
     * @param string $currency 
     * @param null|object $destinations 
     * @param null|object $callback 
     * @return mixed 
     * @throws GlobalRuntimeException 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function settings(array $options): \stdClass
    {
        $url = sprintf('v2/wallets/%s', $this->wallets);

        if (array_key_exists('callback', $options)) {
            $options['callback'] = $callback instanceof CallbackHelper ? $callback->toArray() : $callback;
        }
        if (array_key_exists('destinations', $options)) {
            $options['destinations'] = $destinations instanceof DestinationsHelper ? $destinations->toArray() : $destinations;
        }

        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::patch($url, $options, $headers);
    }
}
