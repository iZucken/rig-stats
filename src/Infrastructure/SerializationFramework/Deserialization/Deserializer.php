<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Deserialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\SerializationFramework\Types\Type;

/**
 * @template Carrier
 * @template Target
 */
interface Deserializer
{
    /**
     * @return Serialized<Carrier>
     */
    public function getCarrier(): Serialized;

    /**
     * @return Type<Target>
     */
    public function getType(): Type;

    /**
     * @return Target
     */
    public function deserialize(): mixed;
}
