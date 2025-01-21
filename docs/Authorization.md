# Authorization

Some endpoints require authorization.
Our service provides two authorization options: either JWT token or transfer key.
You can use one of them to request some protected endpoints: process callbacks log,
make transfers, check private invoice information, change account/wallet settings, etc.

If both are set, then JWT token is chosen automatically.

[Authorization API docs](https://apirone.com/docs/authorization)

## Transfer key*

To use this method just set the transfer key for your account or wallet object.

```php
// for account
$my_account->setTransferKey('place_account_transfer_key_here');

// for wallet
$my_wallet->setTransferKey('place_wallet_transfer_key_here');
```

\* You get a transfer-key when you create a new [account](Account.md#create-a-new-account) or [wallet](Wallet.md#create-a-new-wallet)

## JWT token

```text
IMPORTANT: This library just provides the API requests methods.
You need to do implementations of token storage and refreshing by yourself.
```

### Login

Obtaining a new JWT token

```php
use Apirone\API\Endpoints\Authorization;

// For login use the account or wallet ID
$login = "apr-f9e1211f4b52a50bcf3c36819fdc4ad3";

// Use the account or wallet transfer-key
$password = "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7";

$JWT = Authorization::login($login, $password);

```

### Refresh

After ```access-token``` expiration you get an exception ```UnauthorizedException``` and you need to refresh it.

```php
use Apirone\API\Endpoints\Authorization;

$JWT_refreshed = Authorization::refresh($JWT->{'refresh-token'});

```

### Logout

```php
use Apirone\API\Endpoints\Authorization;

Authorization::refresh($JWT->{'access-token'}); // Response {}

```

## Set JWT token

To use this method just get a new token and set it to the account or wallet object.

```php
$my_token = __my_token_storage__get_token(...);

// for account
$my_account->setToken($my_token);

// for wallet
$my_wallet->setToken($my_token);
```
