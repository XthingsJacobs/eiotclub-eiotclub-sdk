<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Tests;

use EIOTClub\Sdk\Client;
use EIOTClub\Sdk\Config;
use EIOTClub\Sdk\Exceptions\ApiException;
use EIOTClub\Sdk\Exceptions\HttpException;
use EIOTClub\Sdk\Signer;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testPostReturnsDecodedArrayOnSuccess(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'code' => '200',
                'message' => 'Success',
                'data' => ['ok' => true],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
        ]);

        $http = new GuzzleClient(['handler' => HandlerStack::create($mock)]);
        $client = new Client(new Config('k', 's', 'https://example.com'), $http, new Signer());

        $resp = $client->post('/api/v3/demo', ['foo' => 'bar']);

        self::assertSame('200', $resp['code']);
        self::assertSame(true, $resp['data']['ok']);
    }

    public function testPostThrowsApiExceptionWhenApiCodeNot200(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'code' => '500',
                'message' => 'Operation failed',
                'data' => ['x' => 1],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
        ]);

        $http = new GuzzleClient(['handler' => HandlerStack::create($mock)]);
        $client = new Client(new Config('k', 's', 'https://example.com', throwOnApiError: true), $http, new Signer());

        $this->expectException(ApiException::class);
        $client->post('/api/v3/demo', []);
    }

    public function testPostThrowsHttpExceptionOnInvalidJson(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], 'not-json'),
        ]);

        $http = new GuzzleClient(['handler' => HandlerStack::create($mock)]);
        $client = new Client(new Config('k', 's', 'https://example.com'), $http, new Signer());

        $this->expectException(HttpException::class);
        $client->post('/api/v3/demo', []);
    }

    public function testVerifyNotificationSignatureUsesNoAppKey(): void
    {
        $config = new Config('appkey_should_not_matter', 'secret', 'https://example.com');
        $client = new Client($config, new GuzzleClient(['handler' => HandlerStack::create(new MockHandler([]))]), new Signer());

        $payload = [
            'appkey' => 'ignored_in_notification',
            'timestamp' => 1,
            'nonce' => 2,
            'foo' => 'bar',
        ];

        $sign = $client->signer()->generate([
            'timestamp' => 1,
            'nonce' => 2,
            'foo' => 'bar',
        ], 'secret');

        self::assertTrue($client->verifyNotificationSignature($payload + ['sign' => $sign]));
    }
}

