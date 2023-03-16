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

namespace Apirone\API\Helpers;

use stdClass;

/**
 * Invoices
 *
 * With the Apirone API, you can issue invoices that allow for seamless payments
 * made by customers in crypto Accounts. In the requests, a customer can see all
 * the invoice details, as well as check its status, callback information and history.
 * The invoice can contain any data parameters which the customer would like to see in the invoice.
 * These are predefined fields that the Apirone service added to the invoice,
 * for example, merchant, URL, and price.
 *
 * @see https://apirone.com/docs/invoices/#invoices
 *
 */
class InvoiceHelper
{
    private string $currency;

    private ?int $amount = null;

    private ?int $lifetime = null;

    private ?string $expire = null;
    
    private ?string $callbackUrl = null;

    private ?string $linkback = null;
    
    private ?string $merchant = null;

    private ?string $url = null;
    
    private ?object $price = null;

    private function __construct(string $currency, ?int $amount = null)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    /**
     * Create an Invoice object
     *
     * @param string   $currency - Currency type (any cryptocurrency supported by service)
     * @param null|int $amount   - Amount for the checkout in the selected currency of the invoice object.
     *                             Also you may create invoices without fixed amount.
     *                             The amount is indicated in minor units
     * @return void 
     */
    public static function create(string $currency, ?int $amount = null)
    {
        $invoice = new static($currency, $amount);

        return $invoice;
    }

    /**
     * @param string $name 
     * @return mixed 
     */
    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Change currency.
     *
     * @param string $currency 
     * @return $this 
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Change amount
     *
     * @param null|int $amount 
     * @return $this 
     */
    public function setAmount(?int $amount = null)
    {
        $this->amount = $amount;

        return $this;
    }
    
    /**
     * Set lifetime
     *
     * Duration of invoice validity (indicated in seconds)
     *
     * @param null|int $lifetime 
     * @return $this 
     */
    public function setLifetime(?int $lifetime = null)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Set expire
     * 
     * Invoice expiration time in ISO-8601 format, for example, 2022-02-22T09:00:30.
     * If both parameters are specified: lifetime and expire, then the parameter
     * expire will take precedence
     *
     * @param null|string $expire 
     * @return $this 
     */
    public function setExpire(?string $expire = null)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Callback URL
     *
     * Callback URL used for invoice status updates notifications. 
     * More information about invoice callback 
     * @see https://apirone.com/docs/receiving-callbacks/#invoices
     *
     */
    public function setCallbackUrl(?string $url = null)
    {
        $this->callbackUrl = $url;

        return $this;
    }

    /**
     * Linkback
     * 
     * The customer will be redirected to this URL after the payment is completed
     * 
     * @param null|string $linkback 
     * @return $this 
     */
    public function setLinkback(?string $linkback = null)
    {
        $this->linkback = $linkback;

        return $this;
    }

    /**
     * Merchant url
     *
     * @param null|string $userDataUrl 
     * @return $this 
     */
    public function setUrl(?string $userDataUrl = null)
    {
        $this->url = $userDataUrl;

        return $this;
    }
    /**
     * Merchant name. Used as the invoice title
     * 
     * @param null|string $userDataMerchant 
     * @return $this 
     */
    public function setMerchant (?string $userDataMerchant = null)
    {
        $this->merchant = $userDataMerchant;

        return $this;
    }

    /**
     * Used in the invoice to display currency and amount in fiat
     *
     * @param null|string $currency 
     * @param null|int $amount 
     * @return $this 
     */
    public function setPrice (?string $currency = null, ?int $amount = null)
    {
        if ($currency == null || $amount == null) {
            $this->price = null;
        }
        else {
            $price = new \stdClass;
            $price->currency = $currency;
            $price->amount   = $amount;

            $this->price = $price;
        }

        return $this;
    }

    /**
     * Return invoice data as JSON
     *
     * @return stdClass 
     */
    public function toJson()
    {
        $invoice = new \stdClass;
        
        if($this->amount !== null) {
            $invoice->amount = $this->amount;
        }

        if($this->lifetime !== null) {
            $invoice->lifetime = $this->lifetime;
        }

        if($this->expire !== null) {
            $invoice->expire = $this->expire;
        }

        if($this->currency !== null) {
            $invoice->currency = $this->currency;
        }

        if($this->callbackUrl !== null) {
            $invoice->{'callback-url'} = $this->callbackUrl;
        }

        if($this->linkback !== null) {
            $invoice->linkback = $this->linkback;
        }

        if($this->merchant !== null || $this->url !== null || $this->price !== null) {
            $userData = new \stdClass;

            if($this->merchant !== null) {
                $userData->merchant = $this->merchant;
            }

            if($this->url !== null) {
                $userData->url = $this->url;
            }

            if($this->price !== null) {
                $userData->price = $this->price;
            }
        }

        if (!empty($userData)) {
            $invoice->{'user-data'} = $userData;
        }

        return $invoice;
    }

    /**
     * Return invoice data as ARRAY
     *
     * @return stdClass 
     */
    public function toArray() {
        return (array) $this->toJson();
    }
}
