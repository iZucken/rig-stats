<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Deserialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\SerializationFramework\Types\Type;

/**
 * @template Carrier
 * @template Target
 */
interface DeserializerProbe
{
    /**
     * @param Serialized<Carrier> $data
     * @param Type<Target> $type
     * @return null|Deserializer<Carrier, Target>
     */
    public function deserializable(Serialized $data, Type $type): ?Deserializer;
}
