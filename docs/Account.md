# Account

An Apirone account is a powerful multi-currency tool designed to:

- create as many addresses as you wish in any cryptocurrency we support,
- accept and transfer crypto,
- generate invoices for your clients,
- estimate network fees.

[Account API docs](https://apirone.com/docs/account)

## Account methods

### Create a new account

```php
use Apirone\API\Endpoints\Account;

$new_account_data = Account::create();
```

### Init an existing account

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

### Account info

You can use optional parameter currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

// All account currencies
$account_info = Account::fromJson($json)->info();

// Only one currency
$account_info_btc = $my_account->info('btc');
```

### Account balance

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

## Address

### Generate address

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

Also you can use [CallbackHelper](Helpers.md#callback-helper).

### Address info

```php
use Apirone\API\Endpoints\Account;

$address = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = Account::fromJson($json)->addressInfo($address);

```

### Address balance

```php
use Apirone\API\Endpoints\Account;

$address = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
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

Also you can use [AddressesHelper](Helpers.md#addresses-helper) for data generation.

## Estimation and Transfer

[Authorization](Authorization.md#authorization) is required for transfer.

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

Also you can use one [TransferHelper](Helpers.md#transfer-helper) for both methods:

## History

### Account History

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

Also you can use [HistoryHelper](Helpers.md#history-helper) to manage options.

### Account History Item

HistoryItemID - You can obtain from Account History

```php
use Apirone\API\Endpoints\Account;

$item_id = '5fe79fb866bb3b9b297f3935f6ab1b029f74d59629a19a8c028812d26642b108';
$history_item = Account::fromJson($json)->historyItem($item_id);

```

### Account Address History

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

Also you can use [PagerHelper](Helpers.md#pager-helper) to manage options.

## Callbacks

### Account Callback Info

[Authorization](Authorization.md#authorization) is required.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);
$account->callbackInfo('btc');

```

### Address Callback Info

[Authorization](Authorization.md#authorization) is required.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);
$callback_info = $account->addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');

```

### Address Callback Log

[Authorization](Authorization.md#authorization) is required.

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

Also you can use [PagerHelper](Helpers.md#pager-helper) to manage options.

## Settings

[Authorization](Authorization.md#authorization) is required.

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

See more about [CallbackHelper](Helpers.md#callback-helper) and [DestinationsHelper](Helpers.md#destinations-helper) classes usage.
