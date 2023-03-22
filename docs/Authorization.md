# Authorization

Some endpoints require Authorization.
Our service provides two authorization options: either JWT token or transfer key.
You can use one of them to request some protected endpoints: process callbacks log,
make transfers, check private invoice information, change account/wallet settings, etc.

## Login

```php
use Apirone\API\Endpoints\Authorization;

// For login use account or wallet ID
$login = "apr-f9e1211f4b52a50bcf3c36819fdc4ad3";

// Use account or wallet transfer-key
$password = "4sSm9aeXQiqMBmeEs42NTjZidJuGrqm7";

$JWT = Authorization::login($login, $password);

```

## Refresh

After ```access-token``` expiration you get an exception ```UnauthorizedException``` and you need to refresh it.

```php
use Apirone\API\Endpoints\Authorization;

$JWT_refreshed = Authorization::refresh($JWT->{'refresh-token'});

```

## Logout

```php
use Apirone\API\Endpoints\Authorization;

Authorization::refresh($JWT->{'access-token'}); // Response {}

```
