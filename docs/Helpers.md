# Helper classes

Helpers are special library classes for easier manipulation of methods' parameters.

## Addresses Helper

Account or wallet addresses endpoints
show a list of all the account/wallet addresses depending on the provided currency which contains short information about each address.

Options parameter is an array with named keys

- limit - The maximum number of items on the page. If not set, the default value: 10
- offset - The sequence number of the item from which the counting starts. If not set, the default value: 0
- q - Filter items by specific criteria

```php
$options = [
    'offset' => 0,
    'limit' => 10,
    'q' => 'address:3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF,empty:false'
]
```

The filter ```q``` is assembled into a string by bare listing of the variables in the comma-separated string. A colon is used as a separator between the parameter name and the value.

Example of ```q```:

**address**:3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF,**empty**:false

Using this helper, you can do it much easier

```php
use Apirone\API\Helpers\AddressesHelper;

// Set all params in the constructor
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

```

You can use this helper for [account](Account.md#account-addresses) / [wallet](Wallet.md#wallet-addresses) addresses requests

## Callback Helper

Once an account, wallet, or address is created, an optional callback parameter can be applied to control certain operations, e.g. confirmation of transactions, receipt of funds, and so on. It is an object of a specified URL page and user's data parameters.

The callback parameter looks like this

```json
...
"callback":{
    "method": "POST",
    "url": "https://example.com",
    "data":{                    
        "optional_key": "key"
    }
},
...
```

A new callback parameter creation

```php
use Apirone\API\Helpers\CallbackHelper';

$new_callback = CallbackHelper::create();
$new_callback->setUrl('https://example.com')
    ->setMethod('POST')
    ->dataItemAdd('optional_key', 'value')
    ->dataItemAdd('another_key', 'another');

$new_callback_json = $new_callback->toJson();

```

Managing the existing callback parameter

```php
use Apirone\API\Helpers\CallbackHelper';

$json = '
{
    "method": "POST",
    "url": "https://example.com",
    "data":{                    
        "optional_key": "key"
    }
}';

$existing_callback = CallbackHelper::fromJson($json));

$existing_callback->setUrl('https://example.com/new_callback_handler')
    ->setMethod('GET')
    ->dataItemRemove('optional_key')
    ->dataItemAdd('another_key', 'another');

$existing_callback_json = $existing_callback->toJson();

```

You can use this helper for a new [account](Account.md#generate-address) / [wallet](Wallet.md#generate-address) addresses generation
and [account](Account.md#account-settings) / [wallet](Wallet.md#wallet-settings) settings

## Destinations Helper

Destinations is parameter of settings used for forwarding incoming funds to specified addresses; it contains addresses and amounts.
Destination addresses shall either have values specified in percentage or be empty to be forwarded 100%

As a string it looks like:

```php
...
$destinations = '3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ:10000,3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF:50%'
...
```

In JSON it looks like:

```json
...
"destinations": [
    {
        "address": "3MzEcYvGbWZNCt83AizrmeQkjr1iHE6xrMm",
        "amount": 10000
    },
    {
        "address": "3QSx5y7g5DZojZbGTsNSNJ5kPBTF56h6Kz",
        "amount": "10%"
    }
],
...
```

This class can help you make destinations manipulation easily.

Create a new object of destinations and add some address:

```php
use Apirone\API\Helpers\DestinationsHelper;

$helper = DestinationsHelper::create();

$helper->itemAdd('3MzEcYvGbWZNCt83AizrmeQkjr1iHE6xrMm', 10000)
    ->itemAdd('3QSx5y7g5DZojZbGTsNSNJ5kPBTF56h6Kz', '10%');

$my_account->settings('btc', $helper);
```

You can use this class for [account](Account.md#account-settings) / [wallet](Wallet.md#wallet-settings) settings.

## History Helper

Allows viewing transaction history, including the opportunity to filter by transfer type, address, and transfer date.

- limit - The maximum number of items on page. If not set, the default value: 10
- offset - The sequence number of the item from which the counting starts. If not set, the default value: 0
- q - Contains a list of filter variables: address, transfer type, calendar period

```php
use Apirone\API\Helpers\HistoryHelper;

$helper = HistoryHelper::create();

$helper->setOffset(2)
    ->setLimit(25)
    ->setCurrency('btc') // matters for accounts only

// Set 'q' parameters
$helper->address('35Gnk75DbehHXkshBX1QzpKdq4AJDW6KKv') // address
    ->setDateFrom('2021-02-01T00:00:01+01:00') // date_from
    ->setDateTo('2021-12-01T23:59:59+01:00'); // date_to

$helper->itemTypePayment(); // Set transfer type to 'payment'
$helper->itemTypeReceipt(); // Set transfer type to 'receipt'
$helper->itemType(); // Clear the transfer type or you can set as a string value: 'payment' or 'receipt'

$account_history = $my_account->history($helper);
$wallet_history = $my_wallet->history($helper);

```

## Invoice Helper

```php
use Apirone\API\Helpers\InvoiceHelper;


$helper = InvoiceHelper::create('btc');
$helper->setAmount(25000)
    ->setLifetime(3600)
    ->setCallbackUrl('https://example.com/callbach-handler?id=123&order_secret=qwerty')
    ->setMerchant('SHOP')
    ->setPrice('usd', 100);

$created_invoice = $my_account->invoiceCreate($helper);

```

Use InvoiceHelper for an account [create invoice](Invoices.md#create-an-invoice) method/

## Pager Helper

A simple class to manipulate pager options.
Pager options look like an array with two optional keys:

- limit - The maximum number of items on the page. If not set, the default value: 10
- offset - The sequence number of the item from which the counting starts. If not set, the default value: 0

```php
$offset = [
    'offset' => 1,
    'limit' => 5
];
```

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\PagerHelper;

$address = '3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF';
$helper = PagerHelper::create(10, 5); // offset & limit in the constructor

// Clear values
$helper->setLimit() // 10
    ->setOffset(); // 0

// Set/change values
$helper->setLimit(25)
    ->setOffset(25);

$address_history = $my_account->addressHistory($address, $helper);

```

Use PagerHelper for [account](Account.md#account-address-history) or [wallet](Wallet.md#wallet-address-history) address history
and [account](Account.md#address-callback-log) or [wallet](Wallet.md#address-callback-log) address callback log

## Transfer Helper

Use this helper to manipulate estimation or transfer parameters.

```php
use Apirone\API\Helpers\TransferHelper;

$helper = TransferHelper::create();

// Add a destination
$helper->addDestination('3N2aXAebXqvV8TDXBabknmi9Gma7HxeMDdZ', 10000);

// Add a destination with percent value
$helper->addDestination('3BntRGKDUxxSjnFjfzDNeAziAgUtGhbkcF', '50%')

// Subtract the fee from the amount. Default - false
$helper->subtractFeeFromAmount(true);

// Normal fee
$helper->setFeeNormal();

// Priority fee
$helper->setFeePriority();

// Value of custom fee (integer)
$helper->setFeeCustom(3);

// Usage with estimation and transfer
$estimation = $my_account->estimation('btc', $helper);
$transfer = $my_account->transfer('btc', $helper);

```

Use TransferHelper for [account](Account.md#estimation-and-transfer) or [wallet](Wallet.md#estimation-and-transfer) estimation and transfer.
