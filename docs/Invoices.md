# Invoices

With the Apirone API, you can issue invoices that allow for seamless payments made by customers in crypto Accounts.
In the requests, a customer can see all the invoice details, as well as check its status, callback information and history.
The invoice can contain any data parameters which the customer would like to see in the invoice.
These are predefined fields that the Apirone service added to the invoice, for example, merchant, URL, and price.

## Create an invoice

Generates an invoice. Customer should have the Apirone account to create invoice.

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

$created_invoice = Account::invoiceCreate(json_decode($invoice_json));

```

Create a new invoice with [InvoiceHelper](src/Helpers/InvoiceHelper.php)

```php
use Apirone\API\Endpoints\Account;
use Apirone\API\Helpers\InvoiceHelper;

$account = Account::fromJson($json);

$helper = InvoiceHelper::create('btc');
$helper->setAmount(25000)
    ->setLifetime(3600)
    ->setCallbackUrl('https://example.com/callbach-handler?id=123&order_secret=qwerty')
    ->setMerchant('SHOP')
    ->setPrice('usd', 100);

$created_invoice = Account::invoiceCreate($helper);

```

## Public invoice info

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);

$invoice_info = $account->invoiceInfo('amr94MKUQCYAzR6c'); //Invoice ID

```

## Private invoice info

Authorization is required.

```php

use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);

$invoice = 'amr94MKUQCYAzR6c';
$show_private_info = true;

$invoice_info = $account->invoiceInfo($invoice, $show_private_info);

```

## Account Invoices List

Authorization is required.

- invoice - string, InvoiceID. Required
- options - array of ```offset``` and ```limit```. Optional

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);

$invoice = 'amr94MKUQCYAzR6c';

$options['offset'] = 1;
$options['limit'] = 5;
// or use PagerHelper::create();

$invoice_info = $account->invoicesList($invoice, $options);
```
