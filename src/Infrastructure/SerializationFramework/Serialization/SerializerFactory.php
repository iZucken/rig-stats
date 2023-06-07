<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Target
 * @template Carrier
 * @template Serializers
 */
interface SerializerFactory
{
    /**
     * @param Target $data
     * @param Format<Carrier> $format
     * @return Serializers|null
     */
    public function serializable(mixed $data, Format $format): ?Serializer;
}
