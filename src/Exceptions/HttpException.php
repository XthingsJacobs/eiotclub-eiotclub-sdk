<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Exceptions;

final class HttpException extends EiotException
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }
}

