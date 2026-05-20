<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Tests;

use EIOTClub\Sdk\CardStatus;
use EIOTClub\Sdk\Client;
use EIOTClub\Sdk\Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class CardsServiceTest extends TestCase
{
    public function testGetAllCardInfoAcceptsIccidsArray(): void
    {
        $history = [];
        $historyMiddleware = Middleware::history($history);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'code' => '200',
                'message' => 'Success',
                'data' => [],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($historyMiddleware);

        $http = new GuzzleClient([
            'handler' => $stack,
            'base_uri' => 'https://example.com',
        ]);

        $client = new Client(new Config('k', 's', 'https://example.com'), $http);
        $client->cards()->getAllCardInfo(
            pageNum: 1,
            pageSize: 500,
            cardStatus: CardStatus::NON_ACTIVATED_NAME,
            iccids: ['iccid1', 'iccid2']
        );

        self::assertCount(1, $history);

        $request = $history[0]['request'];
        $payload = json_decode((string) $request->getBody(), true);

        self::assertSame(CardStatus::NON_ACTIVATED_NAME, $payload['cardStatus']);
        self::assertSame('iccid1,iccid2', $payload['iccids']);
    }

    public function testGetAllCardInfoRejectsInvalidCardStatus(): void
    {
        $client = new Client(new Config('k', 's', 'https://example.com'), new GuzzleClient(['handler' => HandlerStack::create(new MockHandler([]))]));

        $this->expectException(\InvalidArgumentException::class);
        $client->cards()->getAllCardInfo(cardStatus: 'INVALID_STATUS');
    }
}

