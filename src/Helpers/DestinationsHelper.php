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

use Apirone\API\Exceptions\RuntimeException;

class DestinationsHelper
{
    /**
     * Destination array where address as key, amount as value
     *
     * @var null|array
     */
    private ?array $destinations;

    private function __construct(?array $destinations = [])
    {
        foreach ($destinations as $item) {
            $this->itemAdd($item->address, $item->amount);
        }
    }

    /**
     * Create empty destinations object
     *
     * @return static 
     */
    public static function create()
    {
        $class = new static();

        return $class;
    }

    /**
     * Create destinations object from destinations array
     * @see https://apirone.com/docs/account/#destinations-array
     *
     * @return static 
     */
    public static function fromArray(array $destinations = [])
    {
        $class = new static($destinations);

        return $class;
    }

    /**
     * Create destinations object from destinations array
     * @see https://apirone.com/docs/account/#estimation
     *
     * @return static 
     */
    public static function fromString(string $destinations = '') {

        $class = new static(self::parseString($destinations));

        return $class;
    }

    /**
     * Add destination into object
     *
     * @param string $address 
     * @param int|string $amount 
     * @return DestinationsHelper 
     * @throws RuntimeException 
     */
    public function itemAdd(string $address, $amount): self
    {
        $this->destinations[$address] = self::parseAmount($amount);

        return $this;
    }

    /**
     * Remove destination by address
     *
     * @param mixed $address 
     * @return DestinationsHelper 
     */
    public function itemRemove(string $address): self
    {
        unset($this->destinations[$address]);

        return $this;
    }

    /**
     * Check is destination exist
     *
     * @param string $address 
     * @return bool 
     */
    public function itemExist(string $address): bool
    {
        return (array_key_exists($address, $this->destinations)) ? true : false;
    }

    /**
     * Return destinations as array
     *
     * @return array 
     */
    public function toArray() {
        $destinations = [];

        foreach ($this->destinations as $address => $amount) {
            $item = new \stdClass;
            $item->address = $address;
            $item->amount = $amount;

            $destinations[] = $item;
        }

        return $destinations;
    }

    /**
     * Return destinations as string
     *
     * @return string 
     */
    public function toString() {
        $destinations = [];
        foreach ($this->destinations as $key => $value) {
            $destinations[] = $key . ':' . $value;
        }

        return implode(',', $destinations);
    }

    /**
     * Convert string of destinations to objects array
     *
     * @param array $destinations 
     * @return array
     */
    public static function parseString ($destinations) {
        $rawItems = explode(',', $destinations);
        foreach ($rawItems as $value) {
            $parts = explode(':', $value);
            $item = new \stdClass;
            $item->address = $parts[0];
            $item->amount = $parts[1];

            $items[] = $item;
        }
        return $items;
    }

    /**
     * Parse amount to integer or percent value
     *
     * @param mixed $amount 
     * @return string 
     * @throws RuntimeException 
     */
    public static function parseAmount($amount)
    {
        $amount = str_replace(' ', '', $amount);
        preg_match('/^([0-9]*)(%?)$/', $amount, $_amount);
        if (empty($_amount) || $_amount[1] <= 0 || ($_amount[2] == '%' && $_amount[1] > 100)) {
            throw new RuntimeException('Incorrect amount value: ' . $amount);
        }

        return $_amount[0];    
    }
}
