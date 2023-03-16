<?php

namespace Apirone\API\Http;

class Error {

    public $error = [];

    public function __invoke($body, $data = '') {
        $error = array(
            'body' => $body,
            'details' => json_decode($data),
        );
        $this->error = $error;
    }

    public function get() {
        return $this->error;
    }
}