<?php
/*
 * This file is part of the Apirone SDK library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Apirone\API\Http;

class Error {

    public $error = [];

    public function __invoke($body, $data = '')
    {
        $error = array(
            'body' => $body,
            'details' => json_decode($data),
        );
        $this->error = $error;
    }

    public function get()
    {
        return $this->error;
    }

    public function __toString()
    {
        return json_encode($this->error);
    }
}