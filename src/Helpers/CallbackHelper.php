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

class CallbackHelper
{
    /**
     * Callback url
     *
     * @var null|string
     */
    private ?string $url;

    /**
     * Callback method
     *
     * @var null|string
     */
    private ?string $method;

    /**
     * Callback data
     *
     * @var null|array
     */
    private ?array $data;

    /**
     * Callback class constructor
     *
     * @param null|string $url
     * @param null|string $method
     * @param null|array $data
     * @return void
     */
    private function __construct(?string $url = null, ?string $method = null, ?array $data = null)
    {
        $this->url = $url;

        $this->method = $method;

        $this->setData($data);
    }

    /**
     * Create callback helper
     *
     * @param null|string $url
     * @param null|string $method
     * @param null|array $data
     * @return static
     */
    public static function create(?string $url = null, ?string $method = null, ?array $data = null)
    {
        $callback = new static($url, $method, $data);

        return $callback;
    }

    /**
     * Create callback from JSON
     *
     * @param mixed $json
     * @return static
     */
    public static function fromJson($json)
    {
        $json = gettype($json) == 'string' ? json_decode($json) : $json;

        $url = (property_exists($json, 'url')) ? $json->url : null;
        $method = (property_exists($json, 'method')) ? $json->method : null;
        $data = (property_exists($json, 'data')) ? $json->data : null;

        $callback = new static($url, $method, $data);

        return $callback;

    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Set callback url
     *
     * @param null|string $url
     * @return $this
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set callback method
     *
     * @param null|string $method
     * @return $this
     */
    public function setMethod(?string $method)
    {
        $this->method = $method !== null ? strtoupper($method) : $method;

        return $this;
    }

    /**
     * Set callback data
     *
     * @param null|array $data
     * @return CallbackHelper
     */
    public function setData(?array $data): self
    {
        $this->data = null;
        if ($data === null) {
            return $this;
        }
        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                $this->dataItemAdd($key, $value);
            }
        }

        return $this;
    }

    /**
     * Add callback data item
     *
     * @param mixed $key
     * @param mixed $value
     * @return CallbackHelper
     */
    public function dataItemAdd($key, $value): self
    {
        $item = new \stdClass();
        $item->{$key} = $value;
        $this->data[] = $item;

        return $this;
    }

    /**
     * Remove data item by item key
     *
     * @param mixed $key
     * @return CallbackHelper
     */
    public function dataItemRemove($key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Build to JSON
     *
     * @return stdClass|null
     */
    public function toJson()
    {
        $callback = new \stdClass();

        if (!is_null($this->url)) {
            $callback->url = $this->url;
        }

        if (!is_null($this->method)) {
            $callback->method = $this->method;
        }

        if (!empty($this->data)) {
            $callback->data = json_decode(json_encode($this->data));
        }

        return $callback;
    }

    /**
     * Build to array
     *
     * @return array
     */
    public function toArray()
    {
        return (array)$this->toJson();
    }
}
