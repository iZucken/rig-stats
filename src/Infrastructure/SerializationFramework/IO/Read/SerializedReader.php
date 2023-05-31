<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Read;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Data
 */
interface SerializedReader
{
    /**
     * @return Serialized<Data>
     */
    public function read(): Serialized;
}
