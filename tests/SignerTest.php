<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Tests;

use EIOTClub\Sdk\Signer;
use PHPUnit\Framework\TestCase;

final class SignerTest extends TestCase
{
    public function testGenerateMatchesDocExample(): void
    {
        $signer = new Signer();

        $params = [
            'appkey' => '000001',
            'timestamp' => 1556595508,
            'nonce' => 24523,
            'userName' => 'wx',
            'age' => 24,
            'sex' => 1,
        ];

        $sign = $signer->generate($params, 'a1b1c1d1');

        self::assertSame('963A56BAEBED746F2746E4D9B3B77BE0F2E171E7', $sign);
    }

    public function testVerifyReturnsTrueForValidSign(): void
    {
        $signer = new Signer();
        $params = [
            'a' => '1',
            'b' => '2',
        ];

        $sign = $signer->generate($params, 's');
        self::assertTrue($signer->verify($params + ['sign' => $sign], 's', $sign));
    }

    public function testVerifyWithoutAppKeyForNotification(): void
    {
        $signer = new Signer();

        $payload = [
            'appkey' => 'should_be_ignored',
            'timestamp' => 1,
            'nonce' => 2,
            'foo' => 'bar',
        ];

        $sign = $signer->generate([
            'timestamp' => 1,
            'nonce' => 2,
            'foo' => 'bar',
        ], 'secret');

        self::assertTrue($signer->verify($payload + ['sign' => $sign], 'secret', $sign, false));
    }
}

