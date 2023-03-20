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

use Apirone\API\Endpoints\EndpointAuthTrait;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Helpers\AddressesHelper;
use Apirone\API\Helpers\CallbackHelper;
use Apirone\API\Helpers\DestinationsHelper;
use Apirone\API\Helpers\HistoryHelper;
use Apirone\API\Helpers\InvoiceHelper;
use Apirone\API\Helpers\PagerHelper;
use Apirone\API\Helpers\TransferHelper;
use Apirone\API\Http\Request;

/**
 * Account
 *
 * An Apirone account is a powerful multi-currency tool designed to:
 *
 * - create as many addresses as you wish in any cryptocurrency we support,
 * - accept and transfer crypto,
 * - generate invoices for your clients,
 * - estimate network fees.
 *
 * @see https://apirone.com/docs/account/#account
 * @package Apirone\API\Endpoints
 */
class Account
{
    use EndpointAuthTrait;

    private ?string $account;

    private function __construct(string $account, ?string $transferKey = null)
    {
        $this->account     = $account;
        $this->transferKey = $transferKey;
    }

    /**
     * Account create
     *
     * Create new account.
     *
     * IMPORTANT!
     * When you create new account get details and save account ID & transfer-key
     *
     * @see https://apirone.com/docs/account/#create-account
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
    public static function create()
    {
        return Request::post('v2/accounts');
    }

    /**
     * Init existing account from JSON object
     *
     * @param string|json $json string or json object (stdClass)
     *
     * @return Account
     */
    public static function fromJson($json)
    {
        $data = gettype($json) == 'string' ? json_decode($json) : $json;

        $account = property_exists($data, 'account') ? $data->account : null;
        $transferKey = property_exists($data, 'transfer-key') ? $data->{'transfer-key'} : null;

        $new = new static($account, $transferKey);

        return $new;
    }

    /**
     * Init existing account by params
     *
     * @param string $account 
     * @param null|string $transferKey
     *
     * @return Account
     */
    public static function init(string $account, ?string $transferKey = null)
    {
        $new = new static($account, $transferKey);

        return $new;
    }

    /**
     * Account Info
     * Gets information about the account.
     *
     * @see https://apirone.com/docs/account/#account-info
     *
     * @param null|string $currency
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function info(?string $currency = null): \stdClass
    {
        $url    = sprintf('v2/accounts/%s', $this->account);
        $options = $currency !== null ? [] : ['currency' => $currency];

        return Request::get($url, $options);
    }

    /**
     * Account Balance
     *
     * Checks account balance.
     *
     * @see https://apirone.com/docs/account/#account-balance
     *
     * @param string $account 
     * @param string|null $currency 
     * @param string|array $addresses String or array of addresses
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function balance( string $currency  = null, $addresses = null): \stdClass
    {
        $url    = sprintf('v2/accounts/%s/balance', $this->account);
        $options = [];

        if ($currency !== null) {
            $options['currency'] = $currency;
        }

        if ($addresses !== null) {
            if (gettype(($addresses) == 'string')) {
                $options['addresses'] = $addresses;
            }
            if (gettype($addresses) == 'array' && !empty($addresses)) {
                $options['addresses'] = implode(',', $addresses);
            }
        }

        return Request::get($url, $options);
    }

    /**
     * Generate Address
     *
     * Creates unique crypto addresses in the account using provided currency.
     *
     * @see https://apirone.com/docs/account/#generate-address 
     *
     * @param string $account 
     * @param string $currency 
     * @param string|null $addrType 
     * @param object|null $callback
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function generateAddress(string $currency, ?string $addrType = null, ?object $callback = null): \stdClass
    {
        $url    = sprintf('v2/accounts/%s/addresses', $this->account);
        $options = [];
        $options['currency'] = $currency;

        if ($addrType !== null) {
            $options['addr-type'] = $addrType;
        }

        if ($callback !== null) {
            if ($callback instanceof CallbackHelper) {
                $options['callback'] = $callback->toJson();
            }
            else {
                $options['callback'] = $callback;
            }
        }

        return Request::post($url, $options);
    }

    /**
     * Address Info
     *
     * Gets information about an address.
     *
     * @see https://apirone.com/docs/account/#address-info
     *
     * @param string $address
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addressInfo(string $address): \stdClass
    {
        $url = sprintf('v2/accounts/%s/address/%s', [$this->account, $address]);

        return Request::get($url, $options);
    }

    /**
     * Address Balance
     *
     * Gets balance of a specified address.
     *
     * @see https://apirone.com/docs/account/#address-balance
     *
     * @param string $address
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addressBalance(string $address): \stdClass
    {
        $url = sprintf('v2/accounts/%s/address/%s/balance', [$this->account, $address]);

        return Request::get($url, $options);
    }

    /**
     * Account Addresses
     *
     * Shows a list of all the account addresses, depending on the provided currency. Contains short information about each address.
     * https://apirone.com/docs/account/#account-addresses
     *
     * @param string $currency 
     * @param array  $options
     *
     * @return stdClass
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     */
    public function addresses(string $currency, $options = []): \stdClass
    {
        $url    = sprintf('v2/accounts/%s/addresses', $this->account);

        if ($options instanceof AddressesHelper) {
            $options = $options->toArray();
        }

        $options['currency'] = $currency;

        return Request::get($url, $options);
    }


    /**
     * Estimation
     * 
     * Estimates a transaction before sending. It allows finding out the amounts of network
     * and processing fees and checks the destinations of transfer in advance.
     *
     * @see https://apirone.com/docs/account/#estimation
     *
     * @param string $currency 
     * @param array|TransferOptionsBuilder $options
     *
     * @return stdClass
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function estimation(string $currency, $options): \stdClass
    {
        $url = sprintf('v2/accounts/%s/transfer', $this->account);

        if ($options instanceof TransferHelper) {
            $options = (array)$options->toJson();
            /** 
            * Destinations to string format
            * @see https://apirone.com/docs/account/#estimation
            */            
            $items = [];
            foreach($options['destinations'] as $item) {
                $items[] = $item->address . ':' . $item->amount;
            }
            $options['destinations'] = implode(',', $items);
        }
        
        $options['currency'] = $currency;

        return Request::get($url, $options);
    }

    /**
     * Transfer
     *
     * Sends currency amount or percentage from the balance in the provided currency.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/account/#transfer
     *
     * @param string $currency 
     * @param mixed $options
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
    public function transfer (string $currency, $options): \stdClass
    {
        $url = sprintf('v2/accounts/%s/transfer', $this->account);

        if ($options instanceof TransferHelper) {
            $options = (array)$options->toJson();
        }

        $headers = [];
        $options['currency'] = $currency;
        $this->setRequestAuth($options, $headers);

        return Request::post($url, $options, $headers);
    }


    /**
     * Account History
     *
     * Allows viewing the account's transaction history, including the opportunity to filter by 
     * transfer type, address, and transfer date.
     *
     * @see https://apirone.com/docs/account/#account-history
     *
     * @param array|HistoryHelper $options 
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
        $url = sprintf('v2/accounts/%s/history', $this->account);

        if($options instanceof HistoryHelper) {
            $options = $options->toArray();
        }

        return Request::get($url, $options);
    }

    /**
     * Account History Item
     *
     * The detailed information on the history item contains a list of transactions, fees, 
     * and a list of incoming/outgoing addresses. There are two kinds of history items: 
     * payments and receipts. 
     * Payment is outgoing transaction.Receipt is incoming transaction.
     * 
     * @see https://apirone.com/docs/account/#account-history-item
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
        $url = sprintf('v2/accounts/%s/history/%s', $this->account, $HistoryItemID);

        return Request::get($url);
    }

    /**
     * Account Address History
     *
     * Outputs a list of operations of a specified account address.
     *
     * @see https://apirone.com/docs/account/#account-address-history
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
        $url = sprintf('v2/accounts/%s/addresses/%s/history', $this->account, $address);

        return Request::get($url, $options);
    }

    /**
     * Account Callback Info
     *
     * Outputs information on the saved callback for the account.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/account/#account-callback-info
     *
     * @param string $currency
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
    public function callbackInfo (string $currency): \stdClass
    {
        $url = sprintf('v2/accounts/%s/callback', $this->account);
        
        $headers = [];
        $options['currency'] = $currency;
        $this->setRequestAuth($options, $headers);
        
        return Request::get($url, $options, $headers);
    }
    
    /**
     * Address Callback Info
     *
     * Outputs information on the saved callback for a specified address.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/account/#address-callback-info
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
        $url = sprintf('v2/accounts/%s/addresses/%s/callback', $this->account, $address);

        $headers = [];
        $options = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }

    /**
     * Address Callback Log
     * 
     * Information on the Callback log in the account: 
     * which requests the service sent to you and which responses you, as a client, gave to them. 
     * This is a basic tool for debugging.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/account/#address-callback-log
     *
     * @param string $address 
     * @param array $options 
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
    public function addressCallbackLog (string $address, $options = []): \stdClass
    {
        $url = sprintf('v2/accounts/%s/addresses/%s/callback-log', $this->account, $address);

        if($options instanceof PagerHelper) {
            $options = $options->toArray();
        }

        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }

    /**
     * Settings
     *
     * Allow adding or changing the callback data of accounts and setting destinations for forwarding.
     * Authorization is required.
     * 
     * @see https://apirone.com/docs/settings/#settings
     *
     * @param string $currency 
     * @param array $options 
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
    public function settings(string $currency, array $options): \stdClass
    {
        $url = sprintf('v2/accounts/%s', $this->account);
        $options['currency'] = $currency;

        $headers = [];
        $this->setRequestAuth($options, $headers);

        if (array_key_exists('callback', $options)) {
            $options['callback'] = $callback instanceof CallbackHelper ? $callback->toArray() : $callback;
        }
        if (array_key_exists('destinations', $options)) {
            $options['destinations'] = $destinations instanceof DestinationsHelper ? $destinations->toArray() : $destinations;
        }

        return Request::patch($url, $options, $headers);
    }

    /**
     * Create Invoice
     *
     * Generates an invoice. You should have the Apirone account to create invoice.
     * @see https://apirone.com/docs/invoices/#create-invoice
     *
     * @param array|InvoiceHelper $options 
     * @return stdClass 
     */
    public function invoiceCreate($options): \stdClass
    {
        $url = sprintf('v2/accounts/%s/invoices', $this->account);

        $options = $options instanceof InvoiceHelper ? (array) $options->toJson() : $options;

        return Request::post($url, $options);
    }

    /**
     * Public/Private Invoice Info
     *
     * Public - Checks the invoice information and its status.
     * @see https://apirone.com/docs/invoices/#public-invoice-info
     *
     * Private - The response contains information about the invoice including security-sensitive data (e.g. callback info).
     * Authorization is required.
     * @see https://apirone.com/docs/invoices/#private-invoice-info
     *
     * @param string $invoice - Invoice ID
     * @param bool $private
     *
     * @return stdClass 
     * @throws RuntimeException 
     * @throws ValidationFailedException 
     * @throws UnauthorizedException 
     * @throws ForbiddenException 
     * @throws NotFoundException 
     * @throws MethodNotAllowedException 
     * @throws GlobalRuntimeException 
     */
    public function invoiceInfo(string $invoice, $private = false): \stdClass
    {
        if ($private === false) {
            return Request::get(sprintf('v2/invoices/%s', $invoice));
        }
        $url = sprintf('v2/accounts/%s/invoices/%s', $this->account, $invoice);

        $options = [];
        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }

    /**
     * Invoices List
     *
     * Shows a list of all invoices in the account. Contains information about each invoice.
     * Authorization is required.
     *
     * @see https://apirone.com/docs/invoices/#invoices-list
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
    public function invoicesList ($options = []): \stdClass
    {
        $url = sprintf('v2/accounts/%s/invoices', $this->account);

        if ($options instanceof PagerHelper) {
            $options = $options->toArray();
        }

        $headers = [];
        $this->setRequestAuth($options, $headers);

        return Request::get($url, $options, $headers);
    }
}
