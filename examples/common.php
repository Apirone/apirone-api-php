<?php

require_once('../vendor/autoload.php');

use Apirone\API\Log\LoggerWrapper;

function pa($mixed, $title = false)
{
    if ($title) {
        echo $title . ':';
    }
    echo '<pre>';
    if (gettype($mixed) == 'boolean') {
        print_r($mixed ? 'true' : 'false');
    } else {
        print_r($mixed !== null ? $mixed : 'NULL');
    }
    echo '</pre>';
}


$loggerCallback = static function ($level, $message, $context) {
    $log_file = '/var/www/html/log.txt';
    $data = [$level, $message, $context];
    file_put_contents($log_file, print_r($data, true) . "\r\n", FILE_APPEND);
};

LoggerWrapper::setLogger($loggerCallback);
