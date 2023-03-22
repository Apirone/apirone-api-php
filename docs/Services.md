# Service general information

## Service info

This API method is used to get information about our general services such as a wallet and an account.

```php
use Apirone\API\Endpoints\Service;

// General account info
$accounts_info = Service::account();

// General wallet info
$wallets_info = Service::wallet();

```

## Network Fee

A network fee is included in a transaction in order to have the transaction processed by a miner and confirmed by the network.

```php
use Apirone\API\Endpoints\Service;

// Get Network Fee for supported crypto currencies
$fee_btc = Service::fee('btc');

```

## Exchange rate

Exchange rates are taken from several sources such as:

- [Coinmarketcap](https://coinmarketcap.com/)
- [Coinpaprika](https://coinpaprika.com/)
- [Coingecko](https://www.coingecko.com/)

```php
use Apirone\API\Endpoints\Service;

// Get Network Fee for supported crypto currencies
$fee_btc = Service::fee('btc');

```
