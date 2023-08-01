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

namespace Apirone\API\Endpoints;

use RuntimeException;

trait EndpointAuthTrait
{
    private ?string $transferKey = null;
    
    private ?string $token = null;

    /**
     * Get account transfer key
     *
     * @return null|string 
     */
    public function getTransferKey()
    {
        return $this->transferKey;
    }

    /**
     * Set account transfer key
     *
     * @param string|null $transferKey 
     * @return void 
     */
    public function setTransferKey(?string $transferKey = null): void
    {
        $this->transferKey = $transferKey;
    }

    /**
     * Get account token
     *
     * @return null|string 
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * 
     * @param null|string $token 
     * @return void 
     */
    public function setToken(?string $token = null): void
    {
        $this->token = $token;
    }

    /**
     * Set auth into header or options for request
     *
     * @param array $options 
     * @param array $headers 
     * @return void 
     * @throws RuntimeException 
     */
    public function setRequestAuth(&$options, &$headers) {
        if ($this->token !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
            return;
        }
        if ($this->transferKey !== null) {
            $options['transfer-key'] = $this->transferKey;
            return;
        }
        throw new RuntimeException('Credentials not set');
    }
}