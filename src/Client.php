<?php

declare(strict_types=1);

namespace EIOTClub\Sdk;

use EIOTClub\Sdk\Exceptions\ApiException;
use EIOTClub\Sdk\Exceptions\HttpException;
use EIOTClub\Sdk\Exceptions\InvalidSignatureException;
use EIOTClub\Sdk\Services\CardsService;
use EIOTClub\Sdk\Services\CdrService;
use EIOTClub\Sdk\Services\CloudEsimService;
use EIOTClub\Sdk\Services\PackagesService;
use EIOTClub\Sdk\Services\PoolsService;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

final class Client
{
    private readonly GuzzleClient $http;
    private readonly Signer $signer;

    private ?CardsService $cards = null;
    private ?PackagesService $packages = null;
    private ?PoolsService $pools = null;
    private ?CloudEsimService $cloudEsim = null;
    private ?CdrService $cdr = null;

    public function __construct(
        private readonly Config $config,
        ?GuzzleClient $httpClient = null,
        ?Signer $signer = null
    ) {
        $this->http = $httpClient ?? new GuzzleClient([
            'base_uri' => rtrim($this->config->baseUri, '/'),
            'timeout' => $this->config->timeout,
        ]);
        $this->signer = $signer ?? new Signer();
    }

    public function cards(): CardsService
    {
        return $this->cards ??= new CardsService($this);
    }

    public function packages(): PackagesService
    {
        return $this->packages ??= new PackagesService($this);
    }

    public function pools(): PoolsService
    {
        return $this->pools ??= new PoolsService($this);
    }

    public function cloudEsim(): CloudEsimService
    {
        return $this->cloudEsim ??= new CloudEsimService($this);
    }

    public function cdr(): CdrService
    {
        return $this->cdr ??= new CdrService($this);
    }

    public function post(string $path, array $businessParams = []): array
    {
        $payload = $this->withSystemParams($businessParams);
        $payload['sign'] = $this->signer->generate($payload, $this->config->secret);

        try {
            $resp = $this->http->post($path, [
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), null, null, $e);
        }

        $body = (string) $resp->getBody();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new HttpException('Invalid JSON response.', $resp->getStatusCode(), $body);
        }

        if ($this->config->throwOnApiError) {
            $code = $data['code'] ?? null;
            if ((string) $code !== '200') {
                throw new ApiException((string) $code, (string) ($data['message'] ?? ''), $data['data'] ?? null);
            }
        }

        return $data;
    }

    public function verifyNotificationSignature(array $payload, string $signField = 'sign'): bool
    {
        $sign = $payload[$signField] ?? '';
        if (!is_string($sign) || $sign === '') {
            return false;
        }

        return $this->signer->verify($payload, $this->config->secret, $sign, false);
    }

    public function assertNotificationSignature(array $payload, string $signField = 'sign'): void
    {
        if (!$this->verifyNotificationSignature($payload, $signField)) {
            throw new InvalidSignatureException('Invalid notification signature.');
        }
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function signer(): Signer
    {
        return $this->signer;
    }

    private function withSystemParams(array $businessParams): array
    {
        return array_merge([
            'appkey' => $this->config->appKey,
            'timestamp' => time(),
            'nonce' => random_int(10000, 99999),
        ], $businessParams);
    }
}

