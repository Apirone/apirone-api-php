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


$loggerCallback = static function ($level, $message, $context = []) {
    $log_file = '/var/www/html/log.txt';
    $dt = new \DateTime();
    $context = ($context) ? ' CONTEXT: ' . json_encode($context) : '';
    $logdata = sprintf('%s %s %s%s', $dt->format("Y-m-d\TH:i:sP"), strtoupper($level), $message, $context);
    file_put_contents($log_file, $logdata. "\r\n", FILE_APPEND);
};

LoggerWrapper::setLogger($loggerCallback);
