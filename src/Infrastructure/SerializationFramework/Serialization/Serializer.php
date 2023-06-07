<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @psalm-api
 * @template Target
 * @template Carrier
 */
interface Serializer
{
    /**
     * @return Serialized<Carrier>
     */
    public function serialize(): Serialized;
}
