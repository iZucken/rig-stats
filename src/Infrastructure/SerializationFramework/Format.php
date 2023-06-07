<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework;

use InvalidArgumentException;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template T
 */
final readonly class Format
{
    /**
     * @param class-string<T & Serialized> $format
     */
    public function __construct(private string $format)
    {
        if (!class_exists($format) || !in_array(Serialized::class, class_implements($format) ?: [])) {
            throw new InvalidArgumentException("Invalid format value $format");
        }
    }

    public function equals(Format $other): bool
    {
        return $this->format === $other->format;
    }
}
