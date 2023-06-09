<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Read;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template ContainerType
 */
interface SerializedReader
{
    /**
     * @return Serialized<ContainerType>
     */
    public function read(): Serialized;
}
