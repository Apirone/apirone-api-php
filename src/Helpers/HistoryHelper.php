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

namespace Apirone\API\Helpers;

use stdClass;

class HistoryHelper
{
    /**
     * Limit
     * @var null|int
     */
    private ?int $limit;

    /**
     * Offset
     * @var null|int
     */
    private ?int $offset;

    /**
     * Currency
     * @var null|string
     */
    private ?string $currency;

    /**
     * Address
     * @var null|string
     */
    private ?string $address;

    /**
     * Date From
     * @var null|string
     */
    private ?string $dateFrom;
    
    /**
     * Date to
     * @var null|string
     */
    private ?string $dateTo;

    /**
     * Item to
     * @var null|string
     */
    private ?string $itemType;

    /**
     * Class constructor
     *
     * @param mixed $offset 
     * @param mixed $limit 
     * @param mixed $currency 
     * @param mixed $address 
     * @param mixed $dateFrom 
     * @param mixed $dateTo 
     * @param mixed $itemType 
     * @return void 
     */
    private function __construct($offset, $limit, $currency, $address, $dateFrom, $dateTo, $itemType)
    {
        $this->offset   = $offset;
        $this->limit    = $limit;
        $this->currency = $currency;
        $this->address  = $address;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->itemType = $itemType;
    }

    /**
     * Create history helper
     *
     * @param null|int $offset Sequential number of the element from which the counting starts. Default value: 0
     * @param null|int $limit The maximum number of transactions displayed on the page. Default value: 10
     * @param null|string $currency Currency type
     * @param null|string $address The whole or the part of a crypto address
     * @param null|string $dateFrom The start date of the calendar period in which the transfer occurred.
     * @param null|string $dateTo The end date of the calendar period. It is the full date in ISO-8601
     * @param null|string $itemType Item type: payment or receipt	
     * @return static 
     */
    public static function create(
        ?int $offset = null,
        ?int $limit = null,
        ?string $currency = null,
        ?string $address = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $itemType = null
    ) {
        $options = new static($offset, $limit, $currency, $address, $dateFrom, $dateTo, $itemType);

        return $options;
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

    public function setOffset (?int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set limit
     *
     * @param null|int $limit 
     * @return $this 
     */
    public function setLimit (?int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set currency
     *
     * @param null|string $currency 
     * @return $this 
     */
    public function setCurrency (?string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set address
     *
     * @param null|string $address 
     * @return $this 
     */
    public function setAddress (?string $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Set dateFrom
     *
     * @param null|string $dateFrom 
     * @return $this 
     */
    public function setDateFrom (?string $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Set dateTo
     *
     * @param null|string $dateTo 
     * @return $this 
     */
    public function setDateTo (?string $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Set itemType to 'payment'
     *
     * @return $this 
     */
    public function itemTypePayment ()
    {
        $this->itemType = 'payment';

        return $this;
    }

    /**
     * Set itemType to 'receipt'
     * @return $this 
     */
    public function itemTypeReceipt ()
    {
        $this->itemType = 'receipt';

        return $this;
    }

    /**
     * Set/unset item type manually
     *
     * @param null|string $itemType 
     * @return $this 
     */
    public function itemType (?string $itemType = null)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Build to JSON
     *
     * @return stdClass 
     */
    public function toJson()
    {
        $options = new \stdClass();

        if ($this->limit !== null) {
            $options->limit = $this->limit;
        }

        if ($this->offset !== null) {
            $options->offset = $this->offset;
        }

        if ($this->currency !== null) {
            $options->currency = $this->currency;
        }

        $q = [];

        if ($this->address !== null) {
            $q[] = 'address:' . $this->address;
        }
        if ($this->dateFrom !== null) {
            $q[] = 'date_from:' . $this->dateFrom;
        }

        if ($this->dateTo !== null) {
            $q[] = 'date_to:' . $this->dateTo;
        }

        if ($this->itemType !== null) {
            $q[] = 'item_type:' . $this->itemType;
        }

        if (!empty($q)) {
            $options->q = implode(',', $q);
        }

        return $options;
    }

    /**
     * Build to array
     *
     * @return array 
     */
    public function toArray() {
        return (array)$this->toJson();
    }
}
