<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Exceptions;

final class SerializerUsageException extends \RuntimeException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}
