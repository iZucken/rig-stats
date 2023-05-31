<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Types;

/**
 * @template T
 */
interface Type
{
    public function equals(Type $other): bool;

    public function describe(): string;
}
