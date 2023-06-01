<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Deserialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Carrier
 * @template Target
 */
interface DeserializerFactory
{
    /**
     * @param Serialized<Carrier> $data
     * @return null|Deserializer<Target>
     */
    public function deserializable(Serialized $data): ?Deserializer;
}
