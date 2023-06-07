<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\Types;

/**
 * @psalm-api
 */
final class TypeDescriber
{
    public static function describe(mixed $type): string
    {
        if (is_object($type)) {
            return get_class($type);
        }
        return gettype($type);
    }
}
