# Invoices

With the Apirone API, you can issue invoices that allow for seamless payments made by customers in crypto Accounts.
In the requests, a customer can see all the invoice details, as well as check its status, callback information and history.
The invoice can contain any data parameters which the customer would like to see in the invoice.
These are predefined fields that the Apirone service added to the invoice, for example, merchant, URL, and price.

## Create an invoice

Generates an invoice. Customer should have the Apirone account to create invoice.

```php
use Apirone\API\Endpoints\Account;

// You can use \stdClass object to invoice data
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

$created_invoice = $my_account->invoiceCreate($invoice_json);

```

Create a new invoice with [InvoiceHelper](Helpers.md#invoice-helper)

## Public invoice info

```php
use Apirone\API\Endpoints\Account;

$my_account = Account::fromJson($json);

$invoice_info = $my_account->invoiceInfo('amr94MKUQCYAzR6c'); //Invoice ID

```

## Private invoice info

[Authorization](Authorization.md#authorization) is required.

```php

use Apirone\API\Endpoints\Account;

$my_account = Account::fromJson($json);

$invoice = 'amr94MKUQCYAzR6c';
$show_private_info = true;

$invoice_info = $my_account->invoiceInfo($invoice, $show_private_info);

```

## Account Invoices List

[Authorization](Authorization.md#authorization) is required.

- options - array of ```offset``` and ```limit```. Optional

```php
use Apirone\API\Endpoints\Account;

$account = Account::fromJson($json);

$options['offset'] = 1;
$options['limit'] = 5;

$invoice_info = $account->invoicesList($options);
```

Also you can use [PagerHelper](Helpers.md#pager-helper) to manage options.
