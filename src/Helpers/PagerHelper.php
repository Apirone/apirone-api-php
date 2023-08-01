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

class PagerHelper
{
    private ?int $limit;

    private ?int $offset;

    private function __construct(?int $offset = null, ?int $limit = null)
    {
        $this->offset   = $offset;
        $this->limit    = $limit;
    }

    public static function create(
        ?int $offset = null,
        ?int $limit = null
    ) {
        $options = new static($offset, $limit);

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
     * Set result offset
     * @param null|int $offset 
     * @return $this 
     */
    public function setOffset (?int $offset = null)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set result limit
     *
     * @param null|int $limit 
     * @return $this 
     */
    public function setLimit (?int $limit = null)
    {
        $this->limit = $limit;

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
