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

use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Helpers\DestinationsHelper;
use stdClass;

class TransferHelper
{
    private ?array $destinations;

    private ?array $addresses;

    private ?bool $subtractFeeFromAmount;

    private ?string $feeType;

    private ?int $feeRate;

    private function __construct(
        ?array  $destinations = null,
        ?array  $addresses = null,
        ?bool   $subtractFeeFromAmount = null,
        ?string $feeType = null,
        ?int    $feeRate = null
    ) {
        if ($destinations !== null) {
            foreach ($destinations as $item) {
                $this->addDestination($item->address, $item->amount);
            }
        }
        else
            $this->destinations = null;
        
        
        $this->addresses = ($addresses !== null) ? implode(',', $addresses) : $addresses;
        $this->subtractFeeFromAmount = $subtractFeeFromAmount;
        $this->feeType = $feeType;
        if ($feeType = 'custom') {
            $this->feeRate = $feeRate;
        }
    }
    
    /**
     * Create transfer helper
     *
     * @param null|array $destinations 
     * @param null|array $addresses 
     * @param null|bool $subtractFeeFromAmount 
     * @param null|string $feeType 
     * @param null|int $feeRate 
     * @return static 
     */
    public static function create(
        ?array $destinations = null,
        ?array $addresses = null,
        ?bool $subtractFeeFromAmount = null,
        ?string $feeType = null,
        ?int $feeRate = null
    )
    {
        $transfer = new static(
            $destinations,
            $addresses,
            $subtractFeeFromAmount,
            $feeType,
            $feeRate
        );

        return $transfer;
    }

    /**
     * Add destination
     *
     * @param string $address 
     * @param mixed $amount 
     * @return $this 
     * @throws RuntimeException 
     */
    public function addDestination(string $address, $amount)
    {   
        $item = new \stdClass;
        $item->address = $address;
        $item->amount = DestinationsHelper::parseAmount($amount);

        $this->destinations[] = $item;

        return $this;
    }

    /**
     * Add transfer address
     *
     * @param string $address 
     * @return $this 
     */
    public function addAddress(string $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Set fee to 'normal'
     *
     * @return $this 
     */
    public function setFeeNormal() {
        $this->feeType = 'normal';

        return $this;
    }

    /**
     * Set fee to 'priority'
     *
     * @return $this 
     */
    public function setFeePriority() {
        $this->feeType = 'priority';

        return $this;
    }

    /**
     * Set custom fee
     *
     * @param int $rate 
     * @return $this 
     */
    public function setFeeCustom(int $rate) {
        $this->feeType = 'custom';
        $this->feeRate = $rate;

        return $this;
    }

    /**
     * Subtract fee from amount
     *
     * @param bool $subtractFeeFromAmount 
     * @return $this 
     */
    public function subtractFeeFromAmount(bool $subtractFeeFromAmount) {
        $this->subtractFeeFromAmount = $subtractFeeFromAmount;

        return $this;
    }

    /**
     * Build to JSON
     *
     * @return stdClass 
     */
    public function toJson()
    {
        $transfer = new \stdClass();

        $transfer->destinations = $this->destinations;

        if($this->addresses !== null) {
            $transfer->addresses = implode(',', $this->addresses);
        }

        if ($this->subtractFeeFromAmount !== null) {
            $transfer->{'subtract-fee-from-amount'} = $this->subtractFeeFromAmount;
        }

        if ($this->feeType !== null) {
            $transfer->fee = $this->feeType;
        }

        if ($this->feeType == 'custom') {
            $transfer->{'fee-rate'} = $this->feeRate;
        }

        return $transfer;
    }

    /**
     * Build to array
     *
     * @return array 
     */
    public function toArray()
    {
        return (array) $this->toJson();
    }
}
