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

use RuntimeException;

trait EndpointAuthTrait
{
    private ?string $transferKey = null;
    
    private ?string $token = null;

    public function getTransferKey()
    {
        return $this->transferKey;
    }

    public function setTransferKey($transferKey)
    {
        $this->transferKey = $transferKey;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
    public function getToken()
    {
        return $this->token;
    }

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