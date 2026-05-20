<?php

declare(strict_types=1);

namespace EIOTClub\Sdk;

final class CardStatus
{
    public const ACTIVATION_READY_NAME = 'ACTIVATION_READY_NAME';
    public const NON_ACTIVATED_NAME = 'NON_ACTIVATED_NAME';
    public const ACTIVATED_NAME = 'ACTIVATED_NAME';
    public const DEACTIVATED_NAME = 'DEACTIVATED_NAME';
    public const RETIRED_NAME = 'RETIRED_NAME';

    public static function values(): array
    {
        return [
            self::ACTIVATION_READY_NAME,
            self::NON_ACTIVATED_NAME,
            self::ACTIVATED_NAME,
            self::DEACTIVATED_NAME,
            self::RETIRED_NAME,
        ];
    }
}

