# Apirone PHP (v2 API Binding for PHP 7)

## Apirone API version 2

The Apirone DOCS can be found [here](https://apirone.com/docs)

## Installation

```bash
composer require apirone/apirone-api-php
```


## Getting starting

### Obtaining general service information

```php
use Apirone\API\Endpoints\Service;

// General account|wallet info
$accounts_info = Service::account();
$wallets_info = Service::wallet();

// Get Network Fee for supported crypto currencies
$fee_btc = Service::fee('btc');

// Currency exchange rates
$rate_btc = Service::ticker('btc');

```


### Accounts

#### Create new account

```php
use Apirone\API\Endpoints\Account;

$new_account_data = Account::create();
```


#### Init existing account

```php
use Apirone\API\Endpoints\Account;

// Init with params;
$account = "apr-f9e1211f4b52a50bcf3c36819fdc4ad3";
$transferKey = "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7"; // Optional. Can set later

$my_account = Account::init($account, $transferKey);

// Init from json|stdClass object
$json = '
{
    "account": "apr-f9e1211f4b52a50bcf3c36819fdc4ad3",
    "created": "2021-11-03T11:03:55.083199",
    "transfer-key": "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7"
}';

$my_account = Account::fromJson($json);

```

#### Account info

You can use optional parameter currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

// All account currencies
$account_info = Account::fromJson($json)->info();

// Only one currency
$account_info_btc = $my_account->info('btc');
```

#### Account balance

You can use optional parameters:

- currency (btc, ltc, etc...)
- addresses (comma separated string)

```php
use Apirone\API\Endpoints\Account;

// All account balance
$account_info = Account::fromJson($json)->balance();

// Only one currency
$account_info_btc = $my_account->balance('btc');

$currency = 'btc';
$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';

$account_info_addresses = $my_account->balance($currency, $addresses);

```

#### Generate address

- currency (btc, ltc, etc...)

Optional

- addrType - String (p2pkh, p2pkh(c), etc... ) Each currency have own supported types list
- callback - JSON

```php
use Apirone\API\Endpoints\Account;

$address_default_params = Account::fromJson($json)->generateAddress('btc');

// With type and callback
$currency = 'btc';
$type = 'p2pkh';
$callback = '
{
    "method":"POST",
    "url":"https://example.com/callback_url",
    "data": {
        "optional_key1":"value1",
        "optional_key2":"value2"
    }
}';


$address_full_params = $my_account->generateAddress($currency, $type, $callback);

```

Also you can use [CallbackHelper](src/Helpers/CallbackHelper.php) for data generation:

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\CallbackHelper;

$url = 'https://example.com/callback_url';
$method = 'POST'; // Optional
$data = ["key" => "key_value", "other" => "other_value"]; //Optional

// Init with params (optional)
$callback = CallbackHelper::create($url, $method, $data);

// Change/add url
$callback->setUrl('https://example.com/other_callback_url')
    ->setMethod('GET')
    ->dataItemAdd('another_key', 'another_value')
    ->dataItemRemove('key');

$new_address = $my_account->generateAddress('btc', null, $callback);

```

#### Address info

```php
use Apirone\API\Endpoints\Account;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = Account::fromJson($json)->addressInfo($address);

```

#### Address balance

```php
use Apirone\API\Endpoints\Account;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = Account::fromJson($json)->addressBalance($address);

```

#### Account Addresses

- currency (btc, ltc, etc..)
- options array (Optional)

```php
// options example
$options = [
    'offset' => 0,
    'limit' => 10,
    'q' => 'address:3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF,empty:false'
]

$addresses = Account::fromJson($json)->addresses('btc', $options)
```

Also you can use [AddressesHelper](src/Helpers/AddressesHelper.php) for data generation:

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\AddressesHelper;

// Set all params in constructor
$offset = 10;
$limit  = 5;
$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$empty = false;

$helper = AddressesHelper::create($offset, $limit, $address, $empty);

// Or use set methods
$helper = AddressesHelper::create();

$helper->setOffset(10);
    ->setLimit(5);
    ->setAddress('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');
    ->setEmpty(true);


$addresses = Account::fromJson($json)->addresses('btc', $helper);
```

#### Estimation and Transfer

Both methods have same params with difference for destinations - string or objects array.

- currency (btc, ltc, etc...)
- options

Options is an array with next params:

- destinations (required) - Comma separated address and colon separated amount pairs or objects array.
- subtract-fee-from-amount - ```true``` or ```false```
- fee - ```normal``` | ```priority``` | ```custom```
- fee-rate - 1, 2, 3, etc... use when fee set to 'custom'

```php
use Apirone\API\Endpoints\Account;

$opt_estimations = [
    'destinations' => '3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ:10000,3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF:50%',
    'subtract-fee-from-amount' => false,
    'fee' => 'custom',
    'fee-rate' => 2
];

$opt_transfer = [
    'destinations' => '[
        {
            "address":"3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ",
            "amount":10000
        },
        {
            "address":"3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF",
            "amount":"50%"
        }
    ]',
];

$estimation = Account::fromJson($json)->estimation('btc', $opt_estimations);
$transfer = Account::fromJson($json)->transfer('btc', $opt_transfer);

```

Also you can use one [TransferHelper](src/Helpers/TransferHelper.php) for both methods:

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\TransferHelper;

$helper = TransferHelper::create();

$helper->addDestination('3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ', 10000)
    ->addDestination('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', '50%')
    ->subtractFeeFromAmount(true);

// $helper->setFeeNormal();
// $helper->setFeePriority();
$helper->setFeeCustom(3);

$estimation = Account::fromJson($json)->estimation('btc', $helper);
$transfer = Account::fromJson($json)->transfer('btc', $helper);

```

#### Account History

options - array. All params is optional

```php
use Apirone\API\Endpoints\Account;

$options = [
    'limit' => 5,
    'offset' => 0,
    'currency' => 'btc',
    'q' => 'item_type:receipt,address:35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv,date_from:2021-02-01T00:00:01+01:00,date_to:2021-12-01T23:59:59+01:00'
]

$account_history = Account::fromJson($json)->history($options);
```

Also you can use [HistoryHelper](src/Helpers/HistoryHelper.php) for manage options:

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\HistoryHelper;

$helper = HistoryHelper::create();

$helper->setOffset(2)
    ->setLimit(25)
    ->setCurrency('btc');

// Set 'q' parameters
$helper->address('35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv')
    ->setDateFrom('2021-02-01T00:00:01+01:00')
    ->setDateTo('2021-12-01T23:59:59+01:00');


$helper->itemTypePayment(); // Set item type to 'payment'
$helper->itemTypeReceipt(); // Set item type to 'receipt'
$helper->itemType(); // Clear item type or you can set as string value: 'payment' or 'receipt'

$account_history = Account::fromJson($json)->history($helper);

```

#### Account History Item

HistoryItemID - You can obtain from Account History

```php
use Apirone\API\Endpoints\Account;

$item_id = '5fe79fb866bb3b9b297f3935f6ab1b029f74d59629a19a8c028812d26642b108';
$history_item = Account::fromJson($json)->historyItem($item_id);

```

#### Account Address History

- address - string. Required.
- options - array with 'limit', 'offset' or both. Optional

```php
use Apirone\API\Endpoints\Account;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$options = [
    'offset' => 10,
    'limit' => 5,
};

$address_history = Account::fromJson($json)->addressHistory($address, $option);

```

Also you can use [PagerHelper](src/Helpers/PagerHelper.php) for manage options:

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\PagerHelper;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$helper = PagerHelper::create(10, 5); // offset & limit in constructor

// Change values
$helper->setLimit(25)
    ->setOffset(0);

$address_history = Account::fromJson($json)->addressHistory($address, $helper);

```

#### Account Callback Info

Authorization required.
You can use ```Account::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Account::setToken($accessToken)```

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

Account::callbackInfo('btc');

```

#### Address Callback Info

Authorization required.
You can use ```Account::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Account::setToken($accessToken)```

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

Account::addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');

```

#### Address Callback Log

Authorization required.
You can use ```Account::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Account::setToken($accessToken)```

- address - string
- options - array of 'offset' and 'limit' keys. Optional.

```php
use Apirone\API\Endpoints\Account;

$options = [
    'offset' => '1',
    'limit' => 5,
];

$address_callback_log = Account::addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', $options);

```

#### Account settings

currency - (btc, ltc, etc...) Required.

options - array

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\CallbackHelper;
use Apirone\API\Helpers\DestinationsHelper;

$account = Account::init('apr-e729d9982f079fa86b10a0e3aa6ff37b', '82ookirnTwWNXXqFwdOQMVZIamt8s1uT');
$options = [];

$json_callback = $account->callbackInfo('btc')->callback;

$callback = CallbackHelper::fromJson($json_callback);
$callback->setUrl('https://another-host.com/callback_handler');
$callback->setData(null); // Unset data

$options['callback'] = $callback;

// Destinations not set yet
$destinations = DestinationsHelper::create();
$destinations->itemAdd('3N1nvw5pTR9wEZSq1GcJJjt7dXi2AUtp1Rn', "100%");

$options['destinations'] = $destinations;

$saved_data = $account->settings('btc', $options);

```

### Invoices

#### Create Invoice

```php
use Apirone\API\Endpoints\Account;

$invoice_json = '
{
    "amount": 25000,
    "currency": "btc",
    "lifetime": 3600,
    "callback_url": "http://example.com",
    "user-data": {
        "merchant": "SHOP",
        "url": "https://exampleshop.com",
        "price": {
            "currency": "usd",
            "amount": 100
        }
    },
    "linkback": "http://linkback.com"
}';

$created_invoice = Account::invoiceCreate($invoice_json);

```

Create new invoice with [InvoiceHelper](src/Helpers/InvoiceHelper.php)

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\InvoiceHelper;

$helper = InvoiceHelper::create('btc');
$helper->setAmount(25000)
    ->setLifetime(3600)
    ->setCallbackUrl('https://example.com/callbach-handler?id=123&order_secret=qwerty')
    ->setMerchant('SHOP')
    ->setPrice('usd', 100);

$created_invoice = Account::invoiceCreate($helper);

```

#### Public invoice info

```php
use Apirone\API\Endpoints\Account;

$invoice_info = $account->invoiceInfo('amr94MKUQCYAzR6c'); //Invoice ID

```

#### Private invoice info

Authorization required.

```php

use Apirone\API\Endpoints\Account;

$invoice = 'amr94MKUQCYAzR6c';
$show_private_info = true;

$invoice_info = $account->invoiceInfo($invoice, $show_private_info);

```

#### Account Invoices List

Authorization required.

- invoice - string, InvoiceID. Required
- options - array of ```offset``` and ```limit```. Optional

```php

use Apirone\API\Endpoints\Account;

$invoice = 'amr94MKUQCYAzR6c';

$options['offset'] = 1;
$options['limit'] = 5;
// or use PagerHelper::create();

$invoice_info = $account->invoicesList($invoice, $options);
```

### Wallets

#### Create new wallet

```php
use Apirone\API\Endpoints\Wallet;

$new_wallet_data = Wallet::create();
```


#### Init existing wallet

```php
use Apirone\API\Endpoints\Wallet;

// Init with params;
$wallet = "btc-a7c5105769724ed80d6eff70338dca08";
$transferKey = "bN2N6rZcXHjHZGJATgTAGADRuUnxSm8C"; // Optional. Can set later

$my_wallet = Wallet::init($wallet, $transferKey);

```

#### Wallet info

```php
use Apirone\API\Endpoints\Wallet;

$wallet_info = $my_wallet->info();

```

#### Wallet balance

- addresses (comma separated string)

```php
use Apirone\API\Endpoints\Wallet;

// Whole Wallet balance
$wallet_info = $my_wallet->balance();

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';

$wallet_info_addresses = $my_wallet->balance($addresses);

```

#### Generate address

Optional

- addrType - String (p2pkh, p2pkh(c), etc... ) Each currency have own supported types list
- callback - JSON

```php
use Apirone\API\Endpoints\Wallet;

$address_default_params = $my_wallet->generateAddress('btc');

// With type and callback
$type = 'p2pkh';
$callback = '
{
    "method":"POST",
    "url":"https://example.com/callback_url",
    "data": {
        "optional_key1":"value1",
        "optional_key2":"value2"
    }
}';


$address_full_params = $my_wallet->generateAddress($type, $callback);

```

Also you can use [CallbackHelper](src/Helpers/CallbackHelper.php) for data generation:

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\CallbackHelper;

$url = 'https://example.com/callback_url';
$method = 'POST'; // Optional
$data = ["key" => "key_value", "other" => "other_value"]; //Optional

// Init with params (optional)
$callback = CallbackHelper::create($url, $method, $data);

// Change/add url
$callback->setUrl('https://example.com/other_callback_url')
    ->setMethod('GET')
    ->dataItemAdd('another_key', 'another_value');
    ->dataItemRemove('key');

$new_address = $my_wallet->generateAddress(null, $callback);

```

#### Address info

```php
use Apirone\API\Endpoints\Wallet;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = $my_wallet->addressInfo($address);

```

#### Address balance

```php
use Apirone\API\Endpoints\Wallet;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = $my_wallet->addressBalance($address);

```

#### Wallet Addresses

- options array (Optional)

```php
// options example
$options = [
    'offset' => 0,
    'limit' => 10,
    'q' => 'address:3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF,empty:false'
]

$addresses = $my_wallet->addresses($options);

```

Also you can use [AddressesHelper](src/Helpers/AddressesHelper.php) for data generation:

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\AddressesHelper;

// Set all params in constructor
$offset = 10;
$limit  = 5;
$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$empty = false;

$helper = AddressesHelper::create($offset, $limit, $address, $empty);

// Or use set methods
$helper = AddressesHelper::create();

$helper->setOffset(10);
    ->setLimit(5);
    ->setAddress('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');
    ->setEmpty(true);


$addresses = $my_wallet->addresses($helper);

```

#### Estimation and Transfer

Both methods have same params with difference for destinations.

- options

Options is an array with next params:

- destinations (required) - Comma separated address and colon separated amount pairs.
- subtract-fee-from-amount - true/false
- fee - normal | priority | custom
- fee-rate - 1, 2, 3 etc... use when fee set to 'custom'

```php
use Apirone\API\Endpoints\Wallet;

$opt_estimations = [
    'destinations' => '3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ:10000,3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF:50%',
    'subtract-fee-from-amount' => false,
    'fee' => 'custom',
    'fee-rate' => 2
];

$opt_transfer = [
    'destinations' => '[
        {
            "address":"3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ",
            "amount":10000
        },
        {
            "address":"3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF",
            "amount":"50%"
        }
    ]',
];

$estimation = $my_wallet->estimation($opt_estimations);
$transfer = $my_wallet->transfer($opt_transfer);

```

Also you can use one [TransferHelper](src/Helpers/TransferHelper.php) for both methods:

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\TransferHelper;

$helper = TransferHelper::create();

$helper->addDestination('3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ', 10000)
    ->addDestination('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', '50%')
    ->subtractFeeFromAmount(true);

// $helper->setFeeNormal();
// $helper->setFeePriority();
$helper->setFeeCustom(3);

$estimation = $my_wallet->estimation($helper);
$transfer = $my_wallet->transfer($helper);

```

#### Wallet History

options - array. All params is optional

```php
use Apirone\API\Endpoints\Wallet;

$options = [
    'limit' => 5,
    'offset' => 0,
    'q' => 'item_type:receipt,address:35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv,date_from:2021-02-01T00:00:01+01:00,date_to:2021-12-01T23:59:59+01:00'
]

$wallet_history = $my_wallet->history($options);
```

Also you can use [HistoryHelper](src/Helpers/HistoryHelper.php) for manage options:

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\HistoryHelper;

$helper = HistoryHelper::create();

$helper->setOffset(2)
    ->setLimit(25);

// Set 'q' parameters
$helper->address('35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv')
    ->setDateFrom('2021-02-01T00:00:01+01:00')
    ->setDateTo('2021-12-01T23:59:59+01:00');


$helper->itemTypePayment(); // Set item type to 'payment'
$helper->itemTypeReceipt(); // Set item type to 'receipt'
$helper->itemType(); // Clear item type or you can set as string value: 'payment' or 'receipt'

$wallet_history = $my_wallet->history($helper);

```

#### Wallet History Item

HistoryItemID - You can obtain from Wallet History

```php
use Apirone\API\Endpoints\Wallet;

$item_id = '5fe79fb866bb3b9b297f3935f6ab1b029f74d59629a19a8c028812d26642b108';
$history_item = $my_wallet->historyItem($item_id);

```

#### Wallet Address History

- address - string. Required.
- options - array with 'limit', 'offset' or both. Optional

```php
use Apirone\API\Endpoints\Wallet;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$options = [
    'offset' => 10,
    'limit' => 5,
};

$address_history = $my_wallet->addressHistory($address, $option);

```

Also you can use [PagerHelper](src/Helpers/PagerHelper.php) for manage options:

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\PagerHelper;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$helper = PagerHelper::create(10, 5); // offset & limit in constructor

// Change values
$helper->setLimit(25)
    ->setOffset(0);

$address_history = $my_wallet->addressHistory($address, $helper);

```

#### Wallet Callback Info

Authorization required.
You can use ```Wallet::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Wallet::setToken($accessToken)```

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

Account::callbackInfo('btc');

```

#### Address Callback Info

Authorization required.
You can use ```Wallet::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Wallet::setToken($accessToken)```

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Wallet;

$address_callback_info = $my_wallet->addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');

```

#### Address Callback Log

Authorization required.
You can use ```Wallet::setTransferKey($transferKey)``` if transfer key not set yet or use JWT token ```Wallet::setToken($accessToken)```

- address - string
- options - array of 'offset' and 'limit' keys. Optional.

```php
use Apirone\API\Endpoints\Wallet;

$options = [
    'offset' => '1',
    'limit' => 5,
];

$address_callback_log = $my_wallet->addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', $options);

```

#### Wallet settings

options - array

```php
use Apirone\API\Endpoints\Wallet;
use Apirone\API\Helpers\CallbackHelper;
use Apirone\API\Helpers\DestinationsHelper;

$wallet = Wallet::init('btc-f43a47823c6f0894c83e3e364fa12654', 'oAqmClPQ69a2upN83N5XoPCBeH3XID41');
$options = [];

$json_callback = $wallet->callbackInfo()->callback;

$callback = CallbackHelper::fromJson($json_callback);
$callback->setUrl('https://another-host.com/callback_handler');
$callback->setData(null); // No params required

$options['callback'] = $callback;

// Destinations not set yet
$destinations = DestinationsHelper::create();
$destinations->itemAdd('3N1nvw5pTR9wEZSq1GcJJjt7dXi2AUtp1Rn', "100%");

$options['destinations'] = $destinations;

$saved_data = $wallet->settings($options);

```

### Authorization with JWT token

#### Login

```php
use Apirone\API\Endpoints\Authorization;

// For login use account or wallet ID
$login = "apr-f9e1211f4b52a50bcf3c36819fdc4ad3";

// Use account or wallet transfer-key
$password = "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7";

$JWT = Authorization::login($login, $password);

```

#### Refresh

After ```access-token``` expiration you got an exception ```UnauthorizedException``` and you need to refresh it.

```php
use Apirone\API\Endpoints\Authorization;

$JWT_refreshed = Authorization::refresh($JWT->{'refresh-token'});

```

#### Logout

```php
use Apirone\API\Endpoints\Authorization;

Authorization::refresh($JWT->{'access-token'}); // Response {}

```

## Error response logging and exceptions

For response logging you can use callback function and process message as you want.

```php

// callback function
$log_handler = static function($message) {
    // process message
};

// Set callback to Error Dispatcher
Apirone\API\Http\ErrorDispatcher::setCallback($log_handler);

```

All request errors generate different exceptions and you need wrap all requests into ```try...catch```.

## Licensing

Licensed under the MIT license. See the [LICENSE](LICENSE) file for details.
