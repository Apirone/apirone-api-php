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

// use Apirone\API\Helpers\AbstractPagerOptions;

// class AddressesOptionsBuilder extends AbstractPagerOptions
class HistoryHelper
{
    private ?int $limit;

    private ?int $offset;

    private ?string $address;

    private ?string $dateFrom;
    
    private ?string $dateTo;

    private ?string $itemType;

    private function __construct($offset, $limit, $address, $dateFrom, $dateTo, $itemType)
    {
        $this->offset   = $offset;
        $this->limit    = $limit;
        $this->address  = $address;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->itemType = $itemType;
    }

    public static function create(
        ?int $offset = null,
        ?int $limit = null,
        ?string $address = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $itemType = null
    ) {
        $options = new static($offset, $limit, $address, $dateFrom, $dateTo, $itemType);

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

    public function setLimit (?int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function setAddress (?string $address)
    {
        $this->address = $address;

        return $this;
    }

    public function setDateFrom (?string $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function setDateTo (?string $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function itemTypePayment ()
    {
        $this->itemType = 'payment';

        return $this;
    }

    public function itemTypeInvoice ()
    {
        $this->itemType = 'invoice';

        return $this;
    }

    public function itemTypeClear ()
    {
        $this->itemType = null;

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
