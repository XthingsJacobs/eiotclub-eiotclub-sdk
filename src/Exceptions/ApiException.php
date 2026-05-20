<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Exceptions;

final class ApiException extends EiotException
{
    public function __construct(
        public readonly string $apiCode,
        public readonly string $apiMessage,
        public readonly mixed $apiData = null
    ) {
        parent::__construct($apiMessage, (int) $apiCode);
    }
}

