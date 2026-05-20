<?php

declare(strict_types=1);

namespace EIOTClub\Sdk;

final class Config
{
    public function __construct(
        public readonly string $appKey,
        public readonly string $secret,
        public readonly string $baseUri = 'https://oapi.eiotclub.com',
        public readonly float $timeout = 10.0,
        public readonly bool $throwOnApiError = true
    ) {
    }
}

