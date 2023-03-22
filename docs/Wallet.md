# Wallets

The Apirone wallet is an essential tool for operating with a certain cryptocurrency.
Basically it is a container of addresses, which are generated as many as need.
Apirone API can create wallets, send and receive payments, estimate transaction fees, use callback function, and far more.

## Create a new wallet

```php
use Apirone\API\Endpoints\Wallet;

$new_wallet_data = Wallet::create();
```


## Init an existing wallet

```php
use Apirone\API\Endpoints\Wallet;

// Init with params;
$wallet = "btc-a7c5105769724ed80d6eff70338dca08";
$transferKey = "bN2N6rZcXHjHZGJATgTAGADRuUnxSm8C"; // Optional. Can be set later

$my_wallet = Wallet::init($wallet, $transferKey);

```

## Wallet info

```php
use Apirone\API\Endpoints\Wallet;

$wallet_info = $my_wallet->info();

```

## Wallet balance

- addresses (comma-separated string)

```php
use Apirone\API\Endpoints\Wallet;

// Whole Wallet balance
$wallet_info = $my_wallet->balance();

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';

$wallet_info_addresses = $my_wallet->balance($addresses);

```

## Generate address

Optional

- addrType - String (p2pkh, p2pkh(c), etc... ) Each currency has its own supported types list
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

## Address info

```php
use Apirone\API\Endpoints\Wallet;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = $my_wallet->addressInfo($address);

```

## Address balance

```php
use Apirone\API\Endpoints\Wallet;

$addresses = '3JH4GWtXNz7us8qw1zAtRr4zuq2nDFXTgu';
$addressInfo = $my_wallet->addressBalance($address);

```

## Wallet Addresses

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

## Estimation and Transfer

Both methods have the same params with differences for destinations.

- options

Options is an array with next params:

- destinations (required) - Comma-separated address and colon-separated amount pairs.
- subtract-fee-from-amount - true/false
- fee - normal | priority | custom
- fee-rate - 1, 2, 3 etc... use when the fee is set as 'custom'

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

## Wallet History

options - array. All params are optional

```php
use Apirone\API\Endpoints\Wallet;

$options = [
    'limit' => 5,
    'offset' => 0,
    'q' => 'item_type:receipt,address:35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv,date_from:2021-02-01T00:00:01+01:00,date_to:2021-12-01T23:59:59+01:00'
]

$wallet_history = $my_wallet->history($options);
```

Also you can use [HistoryHelper](src/Helpers/HistoryHelper.php) to manage options:

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

## Wallet History Item

HistoryItemID - You can obtain from Wallet History

```php
use Apirone\API\Endpoints\Wallet;

$item_id = '5fe79fb866bb3b9b297f3935f6ab1b029f74d59629a19a8c028812d26642b108';
$history_item = $my_wallet->historyItem($item_id);

```

## Wallet Address History

- address - string. Required.
- options - array with 'limit', 'offset', or both. Optional

```php
use Apirone\API\Endpoints\Wallet;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$options = [
    'offset' => 10,
    'limit' => 5,
};

$address_history = $my_wallet->addressHistory($address, $option);

```

Also you can use [PagerHelper](src/Helpers/PagerHelper.php) to manage options:

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

## Wallet Callback Info

Authorization is required.
Either you can use ```Wallet::setTransferKey($transferKey)```, if the transfer key is not set yet; or you can use JWT token ```Wallet::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Account;

Account::callbackInfo('btc');

```

## Address Callback Info

Authorization is required.
Either you can use ```Wallet::setTransferKey($transferKey)```, if the transfer key is not set yet; or you can use JWT token ```Wallet::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

- currency (btc, ltc, etc...)

```php
use Apirone\API\Endpoints\Wallet;

$address_callback_info = $my_wallet->addressCallbackInfo('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF');

```

## Address Callback Log

Authorization is required.
Eeither you can use ```Wallet::setTransferKey($transferKey)```, if the transfer key is not set yet; or you can use JWT token ```Wallet::setToken($accessToken)```. If both are set, then JWT token is chosen automatically.

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

## Wallet settings

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

// Destinations are not set yet
$destinations = DestinationsHelper::create();
$destinations->itemAdd('3N1nvw5pTR9wEZSq1GcJJjt7dXi2AUtp1Rn', "100%");

$options['destinations'] = $destinations;

$saved_data = $wallet->settings($options);

```
