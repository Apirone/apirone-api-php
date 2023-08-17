<?php

require_once('common.php');

use Apirone\API\Endpoints\Service;

try {
    $accountsInfo = Service::account();
    pa($accountsInfo);
} catch (Exception $e) {
    pa($e);
}
