<?php

declare(strict_types=1);

namespace EIOTClub\Sdk;

final class Signer
{
    public function generate(array $params, string $secret): string
    {
        $pairs = [];

        foreach ($params as $key => $value) {
            if ($this->isEmptyValue($value)) {
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_scalar($value)) {
                $value = (string) $value;
            } else {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $pairs[(string) $key] = $value;
        }

        ksort($pairs, SORT_STRING);

        $stringParam = '';
        foreach ($pairs as $key => $value) {
            $stringParam .= $key . '=' . $value . '&';
        }

        $stringParam .= 'secret=' . $secret;

        return strtoupper(sha1($stringParam));
    }

    public function verify(array $params, string $secret, string $sign, bool $includeAppKey = true): bool
    {
        $params = $this->withoutSign($params);
        if (!$includeAppKey) {
            unset($params['appkey'], $params['appKey'], $params['app_key']);
        }

        return hash_equals(strtoupper($sign), $this->generate($params, $secret));
    }

    public function withoutSign(array $params): array
    {
        unset($params['sign'], $params['SIGN']);
        return $params;
    }

    private function isEmptyValue(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if ($value === '') {
            return true;
        }

        if (is_array($value) && $value === []) {
            return true;
        }

        return false;
    }
}

