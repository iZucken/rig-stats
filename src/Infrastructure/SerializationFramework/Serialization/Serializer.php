<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Target
 */
interface Serializer
{
    /**
     * @return Serialized<Target>
     */
    public function serialize(): Serialized;
}
