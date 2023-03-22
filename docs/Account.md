# Account

An Apirone account is a powerful multi-currency tool designed to:

- create as many addresses as you wish in any cryptocurrency we support,
- accept and transfer crypto,
- generate invoices for your clients,
- estimate network fees.

## Create a new account

```php
use Apirone\API\Endpoints\Account;

$new_account_data = Account::create();
```

## Init an existing account

```php
use Apirone\API\Endpoints\Account;

// Init with params;
$account = "apr-f9e1211f4b52a50bcf3c36819fdc4ad3";
$transferKey = "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7"; // Optional. Can be set later

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

## Account info

You can use optional parameter currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

// All account currencies
$account_info = Account::fromJson($json)->info();

// Only one currency
$account_info_btc = $my_account->info('btc');
```

## Account balance

You can use optional parameters:

- currency (btc, ltc, etc...)
- addresses (comma-separated string)

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

## Generate address

- currency (btc, ltc, etc...)

Optional

- addrType - String (p2pkh, p2pkh(c), etc... ) Each currency has its own supported types list
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

## Address info

```php
use Apirone\API\Endpoints\Account;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = Account::fromJson($json)->addressInfo($address);

```

## Address balance

```php
use Apirone\API\Endpoints\Account;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = Account::fromJson($json)->addressBalance($address);

```

## Account Addresses

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
    ->setAddress('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF')
    ->setEmpty(true);

$addresses = Account::fromJson($json)->addresses('btc', $helper);

```

## Estimation and Transfer

Both methods have the same params with differences for destinations - string or objects array.

- currency (btc, ltc, etc...)
- options

Options is an array with the next params:

- destinations (required) - Comma-separated address and colon-separated amount pairs or objects array.
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

## Account History

options - array. All params are optional

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

Also you can use [HistoryHelper](src/Helpers/HistoryHelper.php) to manage options:

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

## Account History Item

HistoryItemID - You can obtain from Account History

```php
use Apirone\API\Endpoints\Account;

$item_id = '5fe79fb866bb3b9b297f3935f6ab1b029f74d59629a19a8c028812d26642b108';
$history_item = Account::fromJson($json)->historyItem($item_id);

```

## Account Address History

- address - string. Required.
- options - array with 'limit', 'offset', or both. Optional

```php
use Apirone\API\Endpoints\Account;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$options = [
    'offset' => 10,
    'limit' => 5,
};

$address_history = Account::fromJson($json)->addressHistory($address, $option);

```

Also you can use [PagerHelper](src/Helpers/PagerHelper.php) to manage options:

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

## Account Callback Info

Authorization is required.
Either you can use ```Account::setTransferKey($transferKey)``` , if the transfer key is not set yet; or you can use JWT token ```Account::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);
$account->callbackInfo('btc');

```

## Address Callback Info

Authorization is required.
Either you can use ```Account::setTransferKey($transferKey)``` , if the transfer key is not set yet; or you can use JWT token ```Account::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);
$callback_info = $account->addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');

```

## Address Callback Log

Authorization is required.
Either you can use ```Account::setTransferKey($transferKey)```, if the transfer key is not set yet; or you can use JWT token ```Account::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

- address - string
- options - array of 'offset' and 'limit' keys. Optional.

```php
use Apirone\API\Endpoints\Account;

$options = [
    'offset' => '1',
    'limit' => 5,
];

$account = Account::fromJson($json);
$address_callback_log = $account->addressCallbackLog('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', $options);

```

## Account settings

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
