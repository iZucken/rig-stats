<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialized;

use RigStats\Infrastructure\SerializationFramework\Format;

final readonly class Json implements Serialized
{
    public function __construct(private null|int|string|bool|float|array|\stdClass $normalized)
    {
    }

    public function describe(): string
    {
        return "json (" . gettype($this->normalized) . ")";
    }

    public static function getFormat(): Format
    {
        return new Format(self::class);
    }

    public function getData(): null|int|string|bool|float|array|\stdClass
    {
        return $this->normalized;
    }
}
