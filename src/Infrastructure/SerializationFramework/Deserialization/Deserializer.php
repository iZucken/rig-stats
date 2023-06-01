<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Deserialization;

/**
 * @template Carrier
 * @template Target
 */
interface Deserializer
{
    /**
     * @return Target
     */
    public function deserialize(): mixed;
}
