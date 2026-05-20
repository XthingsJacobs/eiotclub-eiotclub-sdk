<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Services;

use EIOTClub\Sdk\Client;

final class CdrService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function request(string $path, array $params = []): array
    {
        return $this->client->post($path, $params);
    }

    public function trafficQuery(string $dateType, string $iccids, string $startDate, string $endDate, string $month): array
    {
        return $this->client->post('/api/v3/cdr/traffic/query', [
            'dateType' => $dateType,
            'iccids' => $iccids,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'month' => $month,
        ]);
    }
}
