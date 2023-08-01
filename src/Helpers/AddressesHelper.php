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

// use Apirone\API\Helpers\AbstractPagerOptions;

// class AddressesOptionsBuilder extends AbstractPagerOptions
class AddressesHelper
{
    private ?int $limit;

    private ?int $offset;

    private ?string $address;

    private ?bool $empty;

    private function __construct($offset, $limit, $address, $empty)
    {
        $this->offset  = $offset;
        $this->limit   = $limit;
        $this->address = $address;
        $this->empty   = $empty;
    }

    /**
     * @param null|int $offset 
     * @param null|int $limit 
     * @param null|string $address 
     * @param null|bool $empty 
     * @return static 
     */
    public static function create(
        ?int $offset = null,
        ?int $limit = null,
        ?string $address = null,
        ?bool $empty = null
    ) {
        $options = new static($offset, $limit, $address, $empty);

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

    /**
     * Set limit
     *
     * @param int $limit 
     * @return $this 
     */
    public function setLimit (int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set offset
     *
     * @param int $offset 
     * @return $this 
     */
    public function setOffset (int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set address
     *
     * @param string $address 
     * @return $this 
     */
    public function setAddress (string $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Show empty
     *
     * @param mixed $empty 
     * @return $this 
     */
    public function setEmpty ($empty)
    {
        $this->empty = (bool) $empty;

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
        if ($this->empty !== null) {
            $q[] = 'empty:' . $this->empty;
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
        return (array) $this->toJson();
    }
}
