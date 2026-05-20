# EIOTClub OpenAPI PHP SDK

## Install

```bash
composer require eiotclub/eiotclub-sdk
```

## Initialize (Unified Entry)

```php
use EIOTClub\Sdk\EIOTClub;

$client = EIOTClub::create($appKey, $secret, [
    'base_uri' => 'https://oapi.eiotclub.com',
    'timeout' => 10,
]);
```

## Chained Calls by Module

### Cards

```php
$resp = $client->cards()->getAllCardInfo(pageNum: 1, pageSize: 500);
```

If an endpoint is not explicitly wrapped yet, you can call it by path directly (the SDK will automatically add `appkey/timestamp/nonce/sign`):

```php
$resp = $client->cards()->request('/api/v3/card/getCardsInfo', [
    'iccid' => '89860xxxxxx'
]);
```

### Other Modules

```php
$client->packages()->request('/api/v3/package/order', [...]);
$client->pools()->request('/api/v3/pool/list', [...]);
$client->cloudEsim()->request('/api/v3/cloudEsim/list', [...]);
$client->cdr()->request('/api/v3/cdr/query', [...]);
```

## Notification Signature Verification (Verify Only)

```php
$payload = json_decode(file_get_contents('php://input'), true) ?: [];

if (!$client->verifyNotificationSignature($payload)) {
    http_response_code(401);
    exit('invalid sign');
}
```
