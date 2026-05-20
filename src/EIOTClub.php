<?php

declare(strict_types=1);

namespace EIOTClub\Sdk;

use GuzzleHttp\Client as GuzzleClient;

final class EIOTClub
{
    public static function create(string $appKey, string $secret, array $options = []): Client
    {
        $config = new Config(
            appKey: $appKey,
            secret: $secret,
            baseUri: (string) ($options['base_uri'] ?? 'https://oapi.eiotclub.com'),
            timeout: (float) ($options['timeout'] ?? 10.0),
            throwOnApiError: (bool) ($options['throw_on_api_error'] ?? true)
        );

        $httpClient = $options['http_client'] ?? null;
        if ($httpClient !== null && !$httpClient instanceof GuzzleClient) {
            $httpClient = null;
        }

        return new Client($config, $httpClient);
    }
}

