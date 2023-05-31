<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Format;

/**
 * @template Target
 * @template Carrier
 */
interface SerializerFactory
{
    /**
     * @param Target $data
     * @param Format<Carrier> $format
     * @return Serializer<Target, Carrier>|null
     */
    public function serializable(mixed $data, Format $format): ?Serializer;
}
